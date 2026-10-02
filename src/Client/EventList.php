<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;

/**
 * The events of a logistics object as a server listed them.
 */
final readonly class EventList
{
    /**
     * @param list<LogisticsEvent> $events
     */
    public function __construct(
        public array $events,
        public int $totalItems,
        public ?DateTimeImmutable $lastModified,
    ) {}
}
