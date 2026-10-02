<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;

final class InMemoryNotificationOutbox implements NotificationOutbox
{
    /** @var list<OutboundNotification> */
    private array $queue = [];

    public function enqueue(OutboundNotification $notification): void
    {
        $this->queue[] = $notification;
    }

    /**
     * @return list<OutboundNotification>
     */
    public function all(): array
    {
        return $this->queue;
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
}
