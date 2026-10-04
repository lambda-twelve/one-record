<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * An append-only log of events per logistics object. Events are never
 * changed or deleted through the API (the spec), so the interface has no
 * update; eraseFor() exists for the host's data-protection operation only.
 *
 * Every event a store receives carries cargo:eventDate: the server refuses a
 * posted event without one and the checked builder refuses to build one. A
 * store may rely on that for its occurred-at filters and sorting; the
 * in-memory store's fallbacks to the receipt time are defensive only.
 */
interface LogisticsEventStore
{
    /**
     * Appending an event whose IRI already exists is a StoreException: event
     * IRIs are minted by the server, so a repeat is a bug, never an update.
     *
     * @throws StoreException
     */
    public function append(LogisticsEvent $event): void;

    public function get(Iri $eventIri): ?LogisticsEvent;

    /**
     * @return list<LogisticsEvent>
     */
    public function query(Iri $logisticsObject, EventQuery $query): array;

    /**
     * When the list for this object last changed: the newest `created` among
     * its events (not the last one appended, which differs only under a
     * non-monotonic clock), for Last-Modified on the collection.
     */
    public function lastModified(Iri $logisticsObject): ?DateTimeImmutable;

    /**
     * Removes every event of an object: the host forgetting it (DataHolder::forget()).
     */
    public function eraseFor(Iri $logisticsObject): void;
}
