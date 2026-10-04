<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * The server enqueues; the host delivers. Sending is deliberately not the
 * SDK's job: it must go through the host's queue, retries, logging and
 * egress allow-list.
 *
 * Timing: enqueue() is called inside the unit of work, next to the writes it
 * announces. Hand the row to your queue only once that unit has committed,
 * and let a rolled-back unit leave no job behind; a job dispatched at
 * enqueue time finds no row yet, or survives an operation that failed.
 * Delivery through any outbox is at least once, so the recipient deduplicates
 * on the notification id (sent as Idempotency-Key by the SDK client).
 *
 * Ownership: what is enqueued is the notification as it was at that moment.
 * An implementation must not be affected by later edits to the object it was
 * handed, nor hand out its retained state to readers (serialising it, or
 * copying the graph, both do). NotificationOutboxContract checks both.
 */
interface NotificationOutbox
{
    public function enqueue(OutboundNotification $notification): void;
}
