<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;

/**
 * The filters of GET /logistics-objects/{id}/logistics-events.
 */
final readonly class EventQuery
{
    public const string SORT_CREATED_ASC = 'ASC-creationDate';
    public const string SORT_CREATED_DESC = 'DESC-creationDate';
    public const string SORT_EVENT_ASC = 'ASC-eventDate';
    public const string SORT_EVENT_DESC = 'DESC-eventDate';

    /**
     * @param list<string> $eventCodes codes the event code must match (any of)
     */
    public function __construct(
        public array $eventCodes = [],
        public ?DateTimeImmutable $createdAfter = null,
        public ?DateTimeImmutable $createdBefore = null,
        public ?DateTimeImmutable $occurredAfter = null,
        public ?DateTimeImmutable $occurredBefore = null,
        public string $sort = self::SORT_CREATED_ASC,
        public ?int $limit = null,
        public int $skip = 0,
    ) {}

    public static function all(): self
    {
        return new self();
    }
}
