<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\InMemory\InMemoryNotificationOutbox;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\AuditTrailQuery;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Testing\HeaderAuthenticator;
use LambdaTwelve\OneRecord\Testing\RecordingUnitOfWork;
use LogicException;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * What the Laravel integration's second review round asked of the SDK
 * (review/laravel2.md): decisions before side effects, and a loud default.
 */
#[CoversNothing]
final class HostFeedbackTest extends ServerTestCase
{
    public function testADecisionThatLosesTheStatusRaceWritesNoGrantsAndNoNotifications(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $pending = (new ActionRequests($this->server->services))->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::PARTNER));
        $this->server->outbox->drain();

        // Another worker accepted first: our compare-and-set loses. No transactional unit of work here,
        // exactly the host setup that lost the race with grants already committed (laravel2.md, item 1).
        $losing = new class ($this->server->actionRequests) implements ActionRequestStore {
            public function __construct(private readonly ActionRequestStore $inner) {}

            public function save(ActionRequest $request): void
            {
                $this->inner->save($request);
            }

            public function transition(ActionRequest $request, RequestStatus $expectedCurrent): void
            {
                throw StoreException::statusConflict($request->iri, $expectedCurrent->shortName(), 'REQUEST_ACCEPTED');
            }

            public function get(Iri $iri): ?ActionRequest
            {
                return $this->inner->get($iri);
            }

            public function auditTrail(Iri $logisticsObject, AuditTrailQuery $query): array
            {
                return $this->inner->auditTrail($logisticsObject, $query);
            }

            public function pendingChanges(Iri $logisticsObject): array
            {
                return $this->inner->pendingChanges($logisticsObject);
            }

            public function accepted(ActionRequestType $type): array
            {
                return $this->inner->accepted($type);
            }
        };
        $factory = new Psr17Factory();
        $services = new Services($this->server->services->config, $this->server->objects, $this->server->events, $losing, $this->server->subscriptions, $this->server->delegations, $this->server->outbox, new HeaderAuthenticator(), $this->server->policy, $this->clock, $this->dispatcher, $factory, $factory);

        try {
            (new DataHolder($services))->accept($pending->iri);
            self::fail('the lost race surfaces');
        } catch (StoreException $e) {
            self::assertSame(StoreException::STATUS_CONFLICT, $e->kind);
        }
        self::assertSame(Decision::Forbid, $this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece->iri), 'no grant was written by the loser');
        self::assertSame([], $this->server->outbox->drain(), 'and nothing was queued');
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
        $factory = new Psr17Factory();
        $s = $this->server;
        $make = static fn(NotificationOutbox $outbox, ?RecordingUnitOfWork $unit) => new Services($s->services->config, $s->objects, $s->events, $s->actionRequests, $s->subscriptions, $s->delegations, $outbox, new HeaderAuthenticator(), $s->policy, $s->services->clock, $s->services->dispatcher, $factory, $factory, $logger, unitOfWork: $unit);

        $make(new InMemoryNotificationOutbox(), null);
        self::assertCount(0, $logger->records, 'in-memory stores have nothing to roll back');
        $make($persistent, new RecordingUnitOfWork());
        self::assertCount(0, $logger->records, 'a bound unit of work is what is wanted');
        $make($persistent, null);
        self::assertCount(1, $logger->records);
        self::assertSame('warning', $logger->only()[0]);
        self::assertStringContainsString('UnitOfWork', $logger->only()[1]);
    }
}
