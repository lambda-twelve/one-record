<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\ActionRequest;

/**
 * The audit trail of a logistics object: its latest revision and the change
 * requests behind the revisions.
 */
final readonly class AuditTrail
{
    /**
     * @param list<ActionRequest> $requests
     */
    public function __construct(
        public int $latestRevision,
        public array $requests,
        public ?DateTimeImmutable $lastModified,
    ) {}
}
