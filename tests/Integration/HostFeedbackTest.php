<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestStatusChanged;
use LambdaTwelve\OneRecord\Server\InMemory\InMemoryNotificationOutbox;
use LambdaTwelve\OneRecord\Server\ServerBuilder;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Server\Spi\Volatile;
use LambdaTwelve\OneRecord\Testing\HeaderAuthenticator;
use LambdaTwelve\OneRecord\Testing\RacingActionRequestStore;
use LambdaTwelve\OneRecord\Testing\RecordingUnitOfWork;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LogicException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\AbstractLogger;
use Psr\Log\LoggerInterface;
use Stringable;

/**
 * What the Laravel and Drupal integrations' second review rounds asked of
 * the SDK (review/laravel2.md, review/drupal2.md): decisions before side
 * effects, events after them, and a loud, honest default.
 */
#[CoversNothing]
final class HostFeedbackTest extends ServerTestCase
{
    /**
     * The server's services with the given action-request store, dispatcher and logger swapped in.
     */
    private function services(?ActionRequestStore $requests = null, ?EventDispatcherInterface $dispatcher = null, ?LoggerInterface $logger = null, ?NotificationOutbox $outbox = null, ?RecordingUnitOfWork $unit = null): Services
    {
        $s = $this->server;
        $factory = new Psr17Factory();

        return new Services($s->services->config, $s->objects, $s->events, $requests ?? $s->actionRequests, $s->subscriptions, $s->delegations, $outbox ?? $s->outbox, new HeaderAuthenticator(), $s->policy, $this->clock, $dispatcher ?? $this->dispatcher, $factory, $factory, $logger, unitOfWork: $unit);
    }

    public function testADecisionThatLosesTheStatusRaceWritesNoGrantsAndNoNotifications(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $pending = (new ActionRequests($this->server->services))->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::PARTNER));
        $this->server->outbox->drain();

        // Another worker accepted first: our compare-and-set loses. No transactional unit of work here,
        // exactly the host setup that lost the race with grants already committed (laravel2.md, item 1).
        $racing = new RacingActionRequestStore($this->server->actionRequests);
        $racing->arm($pending->iri);

        try {
            (new DataHolder($this->services($racing)))->accept($pending->iri);
            self::fail('the lost race surfaces');
        } catch (StoreException $e) {
            self::assertSame(StoreException::STATUS_CONFLICT, $e->kind);
        }
        self::assertSame(1, $racing->racesLost);
        self::assertSame(Decision::Forbid, $this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece->iri), 'no grant was written by the loser');
        self::assertSame([], $this->server->outbox->drain(), 'and nothing was queued');
    }

    public function testTheRaceCanBeStagedThroughTheRealCompareAndSet(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $pending = (new ActionRequests($this->server->services))->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::PARTNER));
        $inner = $this->server->actionRequests;
        $racing = new RacingActionRequestStore($inner);
        // The competing worker rejects the request in its own transaction just before ours decides.
        $racing->arm($pending->iri, fn() => $inner->transition($pending->withStatus(RequestStatus::Rejected, $this->clock->now(), new Iri(self::HOLDER)), RequestStatus::Pending));

        try {
            (new DataHolder($this->services($racing)))->accept($pending->iri);
            self::fail('the real compare-and-set loses');
        } catch (StoreException $e) {
            self::assertSame(StoreException::STATUS_CONFLICT, $e->kind);
        }
        self::assertSame(RequestStatus::Rejected, $inner->get($pending->iri)?->status, 'the other worker\'s decision stands');
        self::assertSame(Decision::Forbid, $this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece->iri));
        // Fired once; afterwards the store behaves normally.
        $racing->transition($pending->withStatus(RequestStatus::Revoked, $this->clock->now(), new Iri(self::HOLDER)), RequestStatus::Rejected);
        self::assertSame(RequestStatus::Revoked, $inner->get($pending->iri)->status);
    }

    public function testStatusChangedFiresAfterTheDecisionsEffectsAreInPlace(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $seen = [];
        $probing = new class ($this, $piece->iri, $seen) implements EventDispatcherInterface {
            /** @param list<string> $seen */
            public function __construct(private readonly HostFeedbackTest $test, private readonly Iri $piece, public array &$seen) {}

            public function dispatch(object $event): object
            {
                if ($event instanceof ActionRequestStatusChanged) {
                    $this->seen[] = $this->test->probe($event, $this->piece);
                }

                return $event;
            }
        };
        $services = $this->services(dispatcher: $probing);
        $holder = new DataHolder($services);
        $requests = new ActionRequests($services);

        // An accepted delegation: the listener already finds the partner allowed in (drupal2.md, item 1).
        $delegation = $requests->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::PARTNER));
        $holder->accept($delegation->iri);
        // An accepted change: the listener already reads revision 2.
        $to = $piece->withGraph(new Graph([...array_filter(iterator_to_array($piece->graph), static fn(Triple $t): bool => $t->predicate->value !== Cargo::goodsDescription), new Triple($piece->iri, new Iri(Cargo::goodsDescription), Literal::string('Magazines'))]));
        $change = (new ChangeBuilder())->diff($piece, $to, 1);
        self::assertNotNull($change);
        $holder->accept($requests->create($change, new Iri(self::PARTNER))->iri);
        // A change that fails to apply: one event, Failed from Pending, although the store saw Accepted in between.
        $stale = (new ChangeBuilder())->diff($piece, $to, 7);
        self::assertNotNull($stale);
        $failed = $holder->accept($requests->create($stale, new Iri(self::PARTNER))->iri);

        self::assertSame(RequestStatus::Failed, $failed->status);
        self::assertSame(['accepted delegation: partner allowed', 'accepted change: revision 2', 'failed from Pending'], $seen);
    }

    /**
     * What a listener sees at the moment ActionRequestStatusChanged is dispatched.
     */
    public function probe(ActionRequestStatusChanged $event, Iri $piece): string
    {
        $status = $event->request->status;
        if ($status === RequestStatus::Failed) {
            return 'failed from ' . $event->previous->name;
        }
        if ($event->request->payload instanceof AccessDelegation) {
            return 'accepted delegation: ' . ($this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece) === Decision::Allow ? 'partner allowed' : 'partner still forbidden');
        }

        $stored = $this->server->objects->latest($piece);

        return 'accepted change: revision ' . ($stored === null ? 0 : $stored->revision);
    }

    public function testAnIdentityUnitOfWorkInFrontOfAPersistentStoreIsWarnedAbout(): void
    {
        $logger = new class extends AbstractLogger {
            /** @var list<array{string, string}> */
            public array $records = [];

            public function log($level, string|Stringable $message, array $context = []): void
            {
                $this->records[] = [\is_string($level) ? $level : 'other', (string) $message];
            }

            /** @return array{string, string} */
            public function only(): array
            {
                return $this->records[0] ?? throw new LogicException('nothing was logged');
            }
        };
        $persistent = new class implements NotificationOutbox {
            public function enqueue(OutboundNotification $notification): void {}
        };
        $volatileDecorator = new class implements NotificationOutbox, Volatile {
            public function enqueue(OutboundNotification $notification): void {}
        };

        $this->services(outbox: new InMemoryNotificationOutbox(), logger: $logger);
        self::assertCount(0, $logger->records, 'in-memory stores have nothing to roll back');
        $this->services(outbox: $volatileDecorator, logger: $logger);
        self::assertCount(0, $logger->records, 'a decorator that says it is volatile is believed (drupal2.md, item 2)');
        $this->services(outbox: $persistent, logger: $logger, unit: new RecordingUnitOfWork());
        self::assertCount(0, $logger->records, 'a bound unit of work is what is wanted');
        $services = $this->services(outbox: $persistent, logger: $logger);
        self::assertCount(1, $logger->records);
        self::assertSame('warning', $logger->only()[0]);
        self::assertStringContainsString('outbox', $logger->only()[1]);
        self::assertStringContainsString('Volatile', $logger->only()[1]);
        self::assertSame([$logger->only()[1]], ServerBuilder::check($services), 'the same finding for a status page');
        self::assertSame([], ServerBuilder::check($this->server->services));
    }
}
