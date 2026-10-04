<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Model\EmbeddedIdMinter;
use LambdaTwelve\OneRecord\Model\Uuid5EmbeddedIdMinter;
use LambdaTwelve\OneRecord\Server\Http\RequestBody;
use LambdaTwelve\OneRecord\Server\Http\Responder;
use LambdaTwelve\OneRecord\Server\Spi\AccessDelegationStore;
use LambdaTwelve\OneRecord\Server\Spi\AccessPolicy;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use LambdaTwelve\OneRecord\Server\Spi\IdGenerator;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsEventStore;
use LambdaTwelve\OneRecord\Server\Spi\LogisticsObjectStore;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\SubscriptionStore;
use LambdaTwelve\OneRecord\Server\Spi\UnitOfWork;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use Psr\Clock\ClockInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Everything the endpoints share: the host's SPI implementations and PSR
 * services, plus the helpers built from them. One object, so an endpoint's
 * constructor stays a single parameter and a host wires things in one place.
 */
final readonly class Services
{
    public Responder $responder;
    public RequestBody $body;
    public Vocabulary $vocabulary;
    public EmbeddedIdMinter $embeddedIds;
    public LoggerInterface $logger;
    public IdGenerator $ids;
    public UnitOfWork $unitOfWork;

    public function __construct(
        public ServerConfig $config,
        public LogisticsObjectStore $objects,
        public LogisticsEventStore $events,
        public ActionRequestStore $actionRequests,
        public SubscriptionStore $subscriptions,
        public AccessDelegationStore $delegations,
        public NotificationOutbox $outbox,
        public Authenticator $authenticator,
        public AccessPolicy $policy,
        public ClockInterface $clock,
        public EventDispatcherInterface $dispatcher,
        ResponseFactoryInterface $responses,
        StreamFactoryInterface $streams,
        ?LoggerInterface $logger = null,
        ?Vocabulary $vocabulary = null,
        ?EmbeddedIdMinter $embeddedIds = null,
        ?IdGenerator $ids = null,
        ?UnitOfWork $unitOfWork = null,
    ) {
        $this->responder = new Responder($responses, $streams);
        $this->body = new RequestBody($config->maxBodyBytes);
        $this->vocabulary = $vocabulary ?? Vocabulary::default();
        $this->embeddedIds = $embeddedIds ?? new Uuid5EmbeddedIdMinter();
        $this->logger = $logger ?? new NullLogger();
        $this->ids = $ids ?? new UuidIdGenerator();
        $this->unitOfWork = $unitOfWork ?? new IdentityUnitOfWork();
        if ($unitOfWork === null && !$this->storesAreInMemory()) {
            // Without a transaction a failed operation leaves what it had already written. Only the
            // in-memory stores have nothing to roll back; a host that persists must bind its own.
            $this->logger->warning('No UnitOfWork was given and at least one store is not the SDK\'s in-memory one: operations will not be atomic. Bind your database transaction as the UnitOfWork.');
        }
    }

    private function storesAreInMemory(): bool
    {
        foreach ([$this->objects, $this->events, $this->actionRequests, $this->subscriptions, $this->delegations, $this->outbox] as $store) {
            if (!str_starts_with($store::class, __NAMESPACE__ . '\\InMemory\\')) {
                return false;
            }
        }

        return true;
    }
}
