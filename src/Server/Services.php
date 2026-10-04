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
use LambdaTwelve\OneRecord\Server\Spi\Volatile;
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
        // The newest configured data model is the validation ceiling, as ServerConfig says (D7-001).
        $this->vocabulary = $vocabulary ?? Vocabulary::for($config->validationModel());
        $this->embeddedIds = $embeddedIds ?? new Uuid5EmbeddedIdMinter();
        $this->logger = $logger ?? new NullLogger();
        $this->ids = $ids ?? new UuidIdGenerator();
        $this->unitOfWork = $unitOfWork ?? new IdentityUnitOfWork();
        foreach ($this->checks() as $finding) {
            $this->logger->warning($finding);
        }
    }

    /**
     * What an operator should know about this wiring, in plain sentences; empty
     * when nothing is amiss. Logged at construction and offered to the host's
     * status page through ServerBuilder::check().
     *
     * @return list<string>
     */
    public function checks(): array
    {
        $findings = [];
        if ($this->unitOfWork instanceof IdentityUnitOfWork) {
            // Without a transaction a failed operation leaves what it had already written. Only
            // stores marked Volatile have nothing to roll back; a host that persists must bind its own.
            $persistent = [];
            foreach (['objects' => $this->objects, 'events' => $this->events, 'actionRequests' => $this->actionRequests, 'subscriptions' => $this->subscriptions, 'delegations' => $this->delegations, 'outbox' => $this->outbox] as $name => $store) {
                if (!$store instanceof Volatile) {
                    $persistent[] = $name . ' (' . $store::class . ')';
                }
            }
            if ($persistent !== []) {
                $findings[] = \sprintf('No UnitOfWork is bound and these stores are not marked Volatile: %s. Operations will not be atomic; bind your database transaction as the UnitOfWork (a decorator around an in-memory store should implement Spi\\Volatile).', implode(', ', $persistent));
            }
        }

        return $findings;
    }
}
