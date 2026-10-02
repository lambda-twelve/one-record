<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\AccessPolicy;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use Psr\Clock\ClockInterface;

/**
 * The spec's access model in memory: deny by default, single and public
 * authorisations per logistics object, grants from accepted access
 * delegations, and a set of "internal" agents (the host itself) who may do
 * everything including the internal-only endpoints. Denials answer Forbid
 * (403) unless the policy is told to hide.
 */
final class InMemoryAccessPolicy implements AccessPolicy
{
    /** @var array<string, true> */
    private array $internal = [];

    /** @var array<string, array<string, true>> object IRI => permission values granted to everyone authenticated */
    private array $public = [];

    public function __construct(
        private readonly AccessDelegationStore $delegations,
        private readonly ClockInterface $clock,
        private readonly Decision $denial = Decision::Forbid,
    ) {}

    /**
     * An agent that acts for the host: full access, internal endpoints included.
     */
    public function addInternal(Iri $agent): void
    {
        $this->internal[$agent->value] = true;
    }

    /**
     * @param non-empty-list<Permission> $permissions
     */
    public function allow(Iri $agent, Iri $logisticsObject, array $permissions): void
    {
        $this->delegations->grant(new \LambdaTwelve\OneRecord\Server\Spi\Grant($agent, $logisticsObject, $permissions));
    }

    /**
     * @param non-empty-list<Permission> $permissions
     */
    public function allowEveryone(Iri $logisticsObject, array $permissions): void
    {
        foreach ($permissions as $permission) {
            $this->public[$logisticsObject->value][$permission->value] = true;
        }
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
        if (isset($this->public[$resource->value][$permission->value])) {
            return Decision::Allow;
        }
        $now = $this->clock->now();
        foreach ($this->delegations->grantsFor($agent->iri, $resource) as $grant) {
            if ($grant->isActiveAt($now) && $grant->allows($permission)) {
                return Decision::Allow;
            }
        }

        return $this->denial;
    }
}
