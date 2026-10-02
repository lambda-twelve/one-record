<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\EventQuery;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsEventStore;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;

final class InMemoryLogisticsEventStore implements LogisticsEventStore
{
    /** @var array<string, array<string, LogisticsEvent>> object IRI => event IRI => event */
    private array $events = [];

    /** @var array<string, DateTimeImmutable> */
    private array $lastModified = [];

    public function append(LogisticsEvent $event): void
    {
        if ($this->get($event->iri) !== null) {
            // Uniqueness at the scope get() looks up: the whole server, not one object's bucket (AR-022).
            throw StoreException::alreadyExists($event->iri);
        }
        $this->events[$event->logisticsObject->value][$event->iri->value] = $event;
        $current = $this->lastModified[$event->logisticsObject->value] ?? null;
        $this->lastModified[$event->logisticsObject->value] = $current === null || $event->created > $current ? $event->created : $current;
    }

    public function eraseFor(Iri $logisticsObject): void
    {
        unset($this->events[$logisticsObject->value], $this->lastModified[$logisticsObject->value]);
    }

    public function get(Iri $eventIri): ?LogisticsEvent
    {
        foreach ($this->events as $events) {
            if (isset($events[$eventIri->value])) {
                return $events[$eventIri->value];
            }
        }

        return null;
    }

    public function query(Iri $logisticsObject, EventQuery $query): array
    {
        $events = array_values($this->events[$logisticsObject->value] ?? []);
        $events = array_values(array_filter($events, static function (LogisticsEvent $event) use ($query): bool {
            if ($query->eventCodes !== [] && array_filter($query->eventCodes, static fn(string $c): bool => $event->matchesCode($c)) === []) {
                return false;
            }
            $created = $event->creationDate() ?? $event->created;
            $occurred = $event->eventDate();
            if ($query->createdAfter !== null && $created <= $query->createdAfter) {
                return false;
            }
            if ($query->createdBefore !== null && $created >= $query->createdBefore) {
                return false;
            }
            if ($query->occurredAfter !== null && ($occurred === null || $occurred <= $query->occurredAfter)) {
                return false;
            }
            if ($query->occurredBefore !== null && ($occurred === null || $occurred >= $query->occurredBefore)) {
                return false;
            }

            return true;
        }));
        usort($events, static function (LogisticsEvent $a, LogisticsEvent $b) use ($query): int {
            $byEvent = str_ends_with($query->sort, 'eventDate');
            $ka = $byEvent ? ($a->eventDate() ?? $a->created) : ($a->creationDate() ?? $a->created);
            $kb = $byEvent ? ($b->eventDate() ?? $b->created) : ($b->creationDate() ?? $b->created);
            $cmp = $ka <=> $kb;
            if ($cmp === 0) {
                $cmp = strcmp($a->iri->value, $b->iri->value);
            }

            return str_starts_with($query->sort, 'DESC') ? -$cmp : $cmp;
        });

        return \array_slice($events, $query->skip, $query->limit);
    }

    public function lastModified(Iri $logisticsObject): ?DateTimeImmutable
    {
        return $this->lastModified[$logisticsObject->value] ?? null;
    }
}
