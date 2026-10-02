<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Server\IdGenerator;
use LambdaTwelve\OneRecord\Server\OneRecordServer;
use LambdaTwelve\OneRecord\Server\ServerBuilder;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;

/**
 * A complete server on in-memory stores: what tests drive and what bin/serve
 * runs. Hosts use it as the worked example of wiring Services; the stores it
 * exposes are also the contract their persistent implementations must meet.
 */
final class InMemoryServer
{
    public readonly InMemoryLogisticsObjectStore $objects;
    public readonly InMemoryLogisticsEventStore $events;
    public readonly InMemoryActionRequestStore $actionRequests;
    public readonly InMemorySubscriptionStore $subscriptions;
    public readonly InMemoryAccessDelegationStore $delegations;
    public readonly InMemoryNotificationOutbox $outbox;
    public readonly InMemoryAccessPolicy $policy;
    public readonly Services $services;
    public readonly OneRecordServer $handler;

    public function __construct(
        ServerConfig $config,
        Authenticator $authenticator,
        ClockInterface $clock,
        EventDispatcherInterface $dispatcher,
        ResponseFactoryInterface $responses,
        StreamFactoryInterface $streams,
        ?LoggerInterface $logger = null,
        Decision $denial = Decision::Forbid,
        ?IdGenerator $ids = null,
    ) {
        $this->objects = new InMemoryLogisticsObjectStore();
        $this->events = new InMemoryLogisticsEventStore();
        $this->actionRequests = new InMemoryActionRequestStore();
        $this->subscriptions = new InMemorySubscriptionStore($this->actionRequests);
        $this->delegations = new InMemoryAccessDelegationStore();
        $this->outbox = new InMemoryNotificationOutbox();
        $this->policy = new InMemoryAccessPolicy($this->delegations, $clock, $denial);
        $this->services = new Services(
            $config,
            $this->objects,
            $this->events,
            $this->actionRequests,
            $this->subscriptions,
            $this->delegations,
            $this->outbox,
            $authenticator,
            $this->policy,
            $clock,
            $dispatcher,
            $responses,
            $streams,
            $logger,
            ids: $ids,
        );
        $this->handler = ServerBuilder::build($this->services);
    }
}
