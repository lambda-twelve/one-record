<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Testing\RecordingUnitOfWork;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;

/**
 * The host's transaction boundary: one unit per mutating request or holder
 * operation, nested calls joining it, reads outside it.
 */
final class UnitOfWorkTest extends ServerTestCase
{
    private RecordingUnitOfWork $unit;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unit = new RecordingUnitOfWork();
        $this->server = $this->makeServer(unitOfWork: $this->unit);
    }

    public function testReadsRunOutsideAndWritesInsideOneUnit(): void
    {
        $piece = $this->storePiece();
        $this->request('GET', '/logistics-objects/piece-1');
        $this->request('HEAD', '/logistics-objects/piece-1');
        $this->request('GET', '/');
        self::assertSame(0, $this->unit->transactions, 'safe methods need no transaction');

        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject, Permission::PostLogisticsEvent]);
        $heavier = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Magazines')->set(Cargo::coload, false)->build($piece->iri);
        $change = (new ChangeBuilder())->diff($piece, $heavier, 1);
        self::assertNotNull($change);
        $created = $this->request('PATCH', '/logistics-objects/piece-1', body: $change->toJson());
        self::assertSame(201, $created->getStatusCode());
        self::assertSame(1, $this->unit->transactions, 'the PATCH, with the request creation inside it, is one unit');
        self::assertGreaterThan(1, $this->unit->maxDepth, 'ActionRequests::create joined the request unit');

        // The holder accepting over HTTP: one unit covering the revision, the request, the fan-out.
        $this->request('PATCH', substr($created->getHeaderLine('Location'), \strlen(self::BASE)) . '?status=REQUEST_ACCEPTED', self::HOLDER);
        self::assertSame(2, $this->unit->transactions);
        self::assertSame(0, $this->unit->rolledBack);
    }

    public function testHolderOperationsAreUnitsTooAndFailuresRollBack(): void
    {
        $holder = new DataHolder($this->server->services);
        $holder->create($this->piece());
        self::assertSame(1, $this->unit->transactions);

        $holder->update(ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Magazines')->set(Cargo::coload, false)->build(new Iri(self::BASE . '/logistics-objects/piece-1')));
        self::assertSame(2, $this->unit->transactions, 'update = create request + accept, one unit');

        try {
            $holder->create($this->piece());
            self::fail('creating twice must fail');
        } catch (\LambdaTwelve\OneRecord\Server\Spi\StoreException) {
        }
        self::assertSame(3, $this->unit->transactions);
        self::assertSame(1, $this->unit->rolledBack, 'the exception left the unit, so the host rolls back');
    }

    public function testCreationOverHttpNotifiesTypeSubscribersWithAnId(): void
    {
        $holder = new DataHolder($this->server->services);
        $holder->subscribe(new Subscription(new Iri(self::PARTNER), TopicType::Type, Cargo::Piece, [SubscriptionEventType::LogisticsObjectCreated]));
        $this->server->outbox->drain();

        $response = $this->request('POST', '/logistics-objects', self::HOLDER, body: $this->piece('http-made')->toJson());
        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());

        $outbox = $this->server->outbox->drain();
        self::assertCount(1, $outbox, 'objects created over HTTP fan out like those created in PHP');
        self::assertSame(NotificationEventType::LogisticsObjectCreated, $outbox[0]->notification->eventType);
        self::assertSame(self::HOLDER, $outbox[0]->notification->triggeredBy?->value);
        self::assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $outbox[0]->id, 'every outbound notification has a stable id to dedupe on');
    }
}
