<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\InMemory;

use LambdaTwelve\OneRecord\Server\GrantAccessPolicy;
use LambdaTwelve\OneRecord\Server\OneRecordServer;
use LambdaTwelve\OneRecord\Server\ServerBuilder;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\IdGenerator;
use LambdaTwelve\OneRecord\Server\Spi\UnitOfWork;
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
    public readonly InMemoryState $state;
    public readonly InMemoryLogisticsObjectStore $objects;
    public readonly InMemoryLogisticsEventStore $events;
    public readonly InMemoryActionRequestStore $actionRequests;
    public readonly InMemorySubscriptionStore $subscriptions;
    public readonly InMemoryAccessDelegationStore $delegations;
    public readonly InMemoryNotificationOutbox $outbox;
    public readonly GrantAccessPolicy $policy;
    public readonly Services $services;
    public readonly OneRecordServer $handler;

    /**
     * @param ?InMemoryState $state stores to continue from (bin/serve reloads them between requests); fresh ones when null
     * @param ?UnitOfWork $unitOfWork the host's transaction boundary; none by default
     */
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
        ?InMemoryState $state = null,
        ?UnitOfWork $unitOfWork = null,
    ) {
        $this->state = $state ?? new InMemoryState($clock, $denial);
        $this->objects = $this->state->objects;
        $this->events = $this->state->events;
        $this->actionRequests = $this->state->actionRequests;
        $this->subscriptions = $this->state->subscriptions;
        $this->delegations = $this->state->delegations;
        $this->outbox = $this->state->outbox;
        $this->policy = $this->state->policy;
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
            unitOfWork: $unitOfWork,
        );
        $this->handler = ServerBuilder::build($this->services);
    }
}
