<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Server\Spi\Decision;
use Psr\Clock\ClockInterface;

/**
 * Every in-memory store of a server, together. It exists so a process can
 * keep the state between requests: bin/serve runs under `php -S`, which
 * starts PHP afresh for each request, and serialises this object to disk in
 * between. The stores hold only value objects, so PHP's serializer suffices.
 */
final class InMemoryState
{
    public readonly InMemoryLogisticsObjectStore $objects;
    public readonly InMemoryLogisticsEventStore $events;
    public readonly InMemoryActionRequestStore $actionRequests;
    public readonly InMemorySubscriptionStore $subscriptions;
    public readonly InMemoryAccessDelegationStore $delegations;
    public readonly InMemoryNotificationOutbox $outbox;
    public readonly InMemoryAccessPolicy $policy;

    public function __construct(ClockInterface $clock, Decision $denial = Decision::Forbid)
    {
        $this->objects = new InMemoryLogisticsObjectStore();
        $this->events = new InMemoryLogisticsEventStore();
        $this->actionRequests = new InMemoryActionRequestStore();
        $this->subscriptions = new InMemorySubscriptionStore($this->actionRequests);
        $this->delegations = new InMemoryAccessDelegationStore();
        $this->outbox = new InMemoryNotificationOutbox();
        $this->policy = new InMemoryAccessPolicy($this->delegations, $clock, $denial);
    }
}
