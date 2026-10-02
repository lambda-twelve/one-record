<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\RequestStatus;

/**
 * The filters of GET /logistics-objects/{id}/audit-trail.
 */
final readonly class AuditTrailQuery
{
    public function __construct(
        public ?DateTimeImmutable $updatedFrom = null,
        public ?DateTimeImmutable $updatedTo = null,
        public ?RequestStatus $status = null,
    ) {}

    public static function all(): self
    {
        return new self();
    }
}
