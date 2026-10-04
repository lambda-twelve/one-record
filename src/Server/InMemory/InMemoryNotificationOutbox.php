<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Server\Spi\Volatile;

final class InMemoryNotificationOutbox implements NotificationOutbox, Volatile
{
    /** @var list<OutboundNotification> */
    private array $queue = [];

    public function enqueue(OutboundNotification $notification): void
    {
        // The queued body is what was true when it was queued, whatever the caller does with its object afterwards (R2-005).
        $notification = self::snapshot($notification);
        $this->queue[] = $notification;
    }

    /**
     * @return list<OutboundNotification>
     */
    public function all(): array
    {
        // What is read is a copy: editing it must not change what will be delivered (R3-003).
        return array_map(self::snapshot(...), $this->queue);
    }

    /**
     * Takes everything off the queue, as a host's delivery job would.
     *
     * @return list<OutboundNotification>
     */
    public function drain(): array
    {
        $queue = $this->queue;
        $this->queue = [];

        return $queue;
    }

    private static function snapshot(OutboundNotification $outbound): OutboundNotification
    {
        $n = $outbound->notification;
        if ($n->body === null) {
            return $outbound;
        }
        $copy = new Notification($n->eventType, $n->logisticsObject, $n->logisticsObjectType, $n->triggeredBy, $n->changedProperties, $n->logisticsEvents, $n->body->withGraph(new Graph($n->body->graph)));

        return new OutboundNotification($outbound->recipient, $copy, $outbound->createdAt, $outbound->id);
    }
}
