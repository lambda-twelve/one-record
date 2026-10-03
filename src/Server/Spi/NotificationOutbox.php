<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * The server enqueues; the host delivers. Sending is deliberately not the
 * SDK's job: it must go through the host's queue, retries, logging and
 * egress allow-list.
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
