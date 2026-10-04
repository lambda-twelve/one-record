<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\Grant;
use LambdaTwelve\OneRecord\Server\Spi\Volatile;

final class InMemoryAccessDelegationStore implements AccessDelegationStore, Volatile
{
    /** @var list<Grant> */
    private array $grants = [];

    public function grant(Grant $grant): void
    {
        $this->grants[] = $grant;
    }

    public function grantsFor(Iri $agent, Iri $logisticsObject): array
    {
        return array_values(array_filter($this->grants, static fn(Grant $g): bool => $g->agent->equals($agent) && $g->logisticsObject->equals($logisticsObject)));
    }

    public function eraseFor(Iri $logisticsObject): void
    {
        $this->grants = array_values(array_filter($this->grants, static fn(Grant $g): bool => !$g->logisticsObject->equals($logisticsObject)));
    }

    public function revokeFrom(Iri $accessDelegationRequest): void
    {
        $this->grants = array_values(array_filter($this->grants, static fn(Grant $g): bool => $g->source === null || !$g->source->equals($accessDelegationRequest)));
    }
}
