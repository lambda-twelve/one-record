<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\AccessPolicy;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\Grant;
use Psr\Clock\ClockInterface;

/**
 * The spec's access model over a grant store: deny by default; per-object
 * grants to single agents or to everyone authenticated, whether given by the
 * host or created by accepting an access delegation; and a set of internal
 * agents (the host's own systems) allowed everything, internal-only
 * endpoints included. Denials answer Forbid (403) unless told to Hide (404).
 *
 * Every grant lives in the AccessDelegationStore, including the "everyone"
 * ones (a grant to the EVERYONE agent), so a persistent store keeps all of
 * the state and this class holds only the internal agents. It is the policy
 * production hosts run over their database; both wrapper packages do.
 */
final class GrantAccessPolicy implements AccessPolicy
{
    /** The agent IRI a public grant is written to: "anyone with a valid token". */
    public const string EVERYONE = 'urn:lambda-twelve:one-record:everyone';

    /** @var array<string, true> */
    private array $internal = [];

    /**
     * @param list<Iri> $internalAgents
     */
    public function __construct(
        private readonly AccessDelegationStore $delegations,
        private readonly ClockInterface $clock,
        private readonly Decision $denial = Decision::Forbid,
        array $internalAgents = [],
    ) {
        foreach ($internalAgents as $agent) {
            $this->addInternal($agent);
        }
    }

    /**
     * An agent that acts for the host: full access, internal endpoints included.
     */
    public function addInternal(Iri $agent): void
    {
        $this->internal[$agent->value] = true;
    }

    public function isInternal(Iri $agent): bool
    {
        return isset($this->internal[$agent->value]);
    }

    /**
     * @param non-empty-list<Permission> $permissions
     */
    public function allow(Iri $agent, Iri $logisticsObject, array $permissions): void
    {
        $this->delegations->grant(new Grant($agent, $logisticsObject, $permissions));
    }

    /**
     * @param non-empty-list<Permission> $permissions
     */
    public function allowEveryone(Iri $logisticsObject, array $permissions): void
    {
        $this->delegations->grant(new Grant(new Iri(self::EVERYONE), $logisticsObject, $permissions));
    }

    public function decide(Agent $agent, Action $action, ?Iri $resource): Decision
    {
        if (isset($this->internal[$agent->iri->value])) {
            return Decision::Allow;
        }
        $permission = $action->permission() ?? ($action === Action::ReadAuditTrail ? Permission::GetLogisticsObject : null);
        if ($permission === null || $resource === null) {
            return $this->denial;
        }
        $now = $this->clock->now();
        foreach ([$agent->iri, new Iri(self::EVERYONE)] as $grantee) {
            foreach ($this->delegations->grantsFor($grantee, $resource) as $grant) {
                if ($grant->isActiveAt($now) && $grant->allows($permission)) {
                    return Decision::Allow;
                }
            }
        }

        return $this->denial;
    }
}
