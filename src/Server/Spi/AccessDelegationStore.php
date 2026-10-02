<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Grants given to organisations, directly by the holder or by accepting an
 * access delegation request. A policy that honours delegations consults it;
 * revoking a delegation request withdraws the grants it created (and the
 * spec's trust chains: anything further delegated from them).
 */
interface AccessDelegationStore
{
    public function grant(Grant $grant): void;

    /**
     * @return list<Grant>
     */
    public function grantsFor(Iri $agent, Iri $logisticsObject): array;

    /**
     * Withdraws every grant that came from this delegation request.
     */
    public function revokeFrom(Iri $accessDelegationRequest): void;

    /**
     * Withdraws every grant on an object: the host forgetting it (DataHolder::forget()).
     */
    public function eraseFor(Iri $logisticsObject): void;
}
