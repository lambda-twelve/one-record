<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Spec\ApiVersion;

/**
 * What a server said about a logistics object: the headers the spec requires
 * and, for GET, the object itself (null after HEAD).
 */
final readonly class LogisticsObjectResponse
{
    public function __construct(
        public ?LogisticsObject $object,
        public int $revision,
        public int $latestRevision,
        public ?DateTimeImmutable $lastModified,
        public ?string $type,
        public ApiVersion $apiVersion,
    ) {}

    public function isLatest(): bool
    {
        return $this->revision === $this->latestRevision;
    }
}
