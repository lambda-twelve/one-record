<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Who may do what. The spec puts access control at logistics-object level
 * with deny by default; how a host decides (its own ownership rules, groups,
 * accepted access delegations) is up to it.
 *
 * The server never asks for actions the spec grants to any authenticated
 * party (server information, posting notifications, requesting subscriptions,
 * delegations and verifications, reading one's own action requests).
 */
interface AccessPolicy
{
    /**
     * @param ?Iri $resource the logistics object (or action request) concerned, null for server-wide actions
     */
    public function decide(Agent $agent, Action $action, ?Iri $resource): Decision;
}
