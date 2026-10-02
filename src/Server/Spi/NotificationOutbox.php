<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

/**
 * The server enqueues; the host delivers. Sending is deliberately not the
 * SDK's job: it must go through the host's queue, retries, logging and
 * egress allow-list.
 */
interface NotificationOutbox
{
    public function enqueue(OutboundNotification $notification): void;
}
