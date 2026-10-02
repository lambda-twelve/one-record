<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Access an organisation holds on a logistics object: from the holder
 * directly, or through an accepted access delegation.
 */
final readonly class Grant
{
    /**
     * @param non-empty-list<Permission> $permissions
     * @param ?Iri $source the access delegation request that granted it, null for the holder's own grants
     */
    public function __construct(
        public Iri $agent,
        public Iri $logisticsObject,
        public array $permissions,
        public ?DateTimeImmutable $expiresAt = null,
        public ?Iri $source = null,
    ) {}

    public function isActiveAt(DateTimeImmutable $now): bool
    {
        return $this->expiresAt === null || $this->expiresAt > $now;
    }

    public function allows(Permission $permission): bool
    {
        return \in_array($permission, $this->permissions, true);
    }
}
