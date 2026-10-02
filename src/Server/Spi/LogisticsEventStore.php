<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Model\LogisticsEvent;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * An append-only log of events per logistics object. Events are never
 * changed or deleted (the spec), so the interface has no update.
 */
interface LogisticsEventStore
{
    public function append(LogisticsEvent $event): void;

    public function get(Iri $eventIri): ?LogisticsEvent;

    /**
     * @return list<LogisticsEvent>
     */
    public function query(Iri $logisticsObject, EventQuery $query): array;

    /**
     * When the list for this object last changed (for Last-Modified on the collection).
     */
    public function lastModified(Iri $logisticsObject): ?DateTimeImmutable;
}
