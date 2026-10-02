<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\GrantAccessPolicy;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\AccessPolicy;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use Psr\Clock\ClockInterface;

/**
 * The old name of GrantAccessPolicy, kept for one beta so wrappers written
 * against it keep compiling. It was never only in memory: it read every grant
 * from the AccessDelegationStore it was given.
 *
 * @deprecated use GrantAccessPolicy
 */
final class InMemoryAccessPolicy implements AccessPolicy
{
    private readonly GrantAccessPolicy $policy;

    public function __construct(AccessDelegationStore $delegations, ClockInterface $clock, Decision $denial = Decision::Forbid)
    {
        $this->policy = new GrantAccessPolicy($delegations, $clock, $denial);
    }

    public function addInternal(Iri $agent): void
    {
        $this->policy->addInternal($agent);
    }

    /**
     * @param non-empty-list<Permission> $permissions
     */
    public function allow(Iri $agent, Iri $logisticsObject, array $permissions): void
    {
        $this->policy->allow($agent, $logisticsObject, $permissions);
    }

    /**
     * @param non-empty-list<Permission> $permissions
     */
    public function allowEveryone(Iri $logisticsObject, array $permissions): void
    {
        $this->policy->allowEveryone($logisticsObject, $permissions);
    }

    public function decide(Agent $agent, Action $action, ?Iri $resource): Decision
    {
        return $this->policy->decide($agent, $action, $resource);
    }
}
