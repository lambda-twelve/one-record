<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use DateTimeImmutable;
use DateTimeZone;

/**
 * The query parameters of GET /logistics-objects/{id}/logistics-events, as
 * the specification names them. Timestamps are sent in the spec's basic
 * ISO form (YYYYMMDDThhmmssZ).
 */
final readonly class EventFilter
{
    public const string SORT_CREATED_ASC = 'ASC-creationDate';
    public const string SORT_CREATED_DESC = 'DESC-creationDate';
    public const string SORT_EVENT_ASC = 'ASC-eventDate';
    public const string SORT_EVENT_DESC = 'DESC-eventDate';

    /**
     * @param list<string> $eventCodes codes or code IRIs; any match passes
     */
    public function __construct(
        public array $eventCodes = [],
        public ?DateTimeImmutable $createdAfter = null,
        public ?DateTimeImmutable $createdBefore = null,
        public ?DateTimeImmutable $occurredAfter = null,
        public ?DateTimeImmutable $occurredBefore = null,
        public ?string $sort = null,
        public ?int $limit = null,
        public ?int $skip = null,
    ) {}

    public static function all(): self
    {
        return new self();
    }

    public function toQueryString(): string
    {
        $parameters = [];
        if ($this->eventCodes !== []) {
            $parameters['event-code'] = implode(',', $this->eventCodes);
        }
        foreach (['created-after' => $this->createdAfter, 'created-before' => $this->createdBefore, 'occurred-after' => $this->occurredAfter, 'occurred-before' => $this->occurredBefore] as $name => $value) {
            if ($value !== null) {
                $parameters[$name] = $value->setTimezone(new DateTimeZone('UTC'))->format('Ymd\THis\Z');
            }
        }
        if ($this->sort !== null) {
            $parameters['sort'] = $this->sort;
        }
        if ($this->limit !== null) {
            $parameters['limit'] = (string) $this->limit;
        }
        if ($this->skip !== null) {
            $parameters['skip'] = (string) $this->skip;
        }

        return $parameters === [] ? '' : '?' . http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
    }
}
