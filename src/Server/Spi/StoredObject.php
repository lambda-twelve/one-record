<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsObject;

/**
 * One revision of a logistics object as the store holds it.
 */
final readonly class StoredObject
{
    public function __construct(
        public LogisticsObject $object,
        public int $revision,
        public int $latestRevision,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $lastModified,
    ) {}

    public function isLatest(): bool
    {
        return $this->revision === $this->latestRevision;
    }
}
