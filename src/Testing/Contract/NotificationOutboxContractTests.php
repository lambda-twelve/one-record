<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\NotificationOutbox;
use LambdaTwelve\OneRecord\Server\Spi\OutboundNotification;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;

/**
 * The tests behind NotificationOutboxContract, as a trait, for hosts whose test cases must
 * extend a framework base class (Laravel's Testbench, Drupal's KernelTestBase)
 * and so cannot extend the abstract contract. Use it in any PHPUnit TestCase
 * and implement the abstract hook(s); the abstract class is this trait on a
 * bare TestCase.
 */
trait NotificationOutboxContractTests
{
    protected const string OBJECT = 'https://1r.example.com/logistics-objects/p1';
    protected const string PARTNER = 'https://1r.partner.example/logistics-objects/partner';

    abstract protected function createOutbox(): NotificationOutbox;

    /**
     * What the host would pick up for delivery, oldest first, without removing it.
     *
     * @return list<OutboundNotification>
     */
    abstract protected function pending(NotificationOutbox $outbox): array;

    public function testEnqueuedNotificationsAreKeptInOrderWithTheirIdentity(): void
    {
        $outbox = $this->createOutbox();
        self::assertSame([], $this->pending($outbox));

        $first = new OutboundNotification(
            new Iri(self::PARTNER),
            new Notification(NotificationEventType::LogisticsObjectUpdated, new Iri(self::OBJECT), Cargo::Piece, new Iri('https://1r.example.com/action-requests/c1'), [Cargo::goodsDescription]),
            new DateTimeImmutable('2026-10-02T12:00:00.000Z'),
            'n-1',
        );
        $second = new OutboundNotification(
            new Iri(self::PARTNER),
            new Notification(NotificationEventType::LogisticsEventReceived, new Iri(self::OBJECT), Cargo::Piece, null, [], [new Iri(self::OBJECT . '/logistics-events/e1')]),
            new DateTimeImmutable('2026-10-02T12:00:01.000Z'),
            'n-2',
        );
        $outbox->enqueue($first);
        $outbox->enqueue($second);

        $pending = $this->pending($outbox);
        self::assertSame(['n-1', 'n-2'], array_map(static fn(OutboundNotification $n): string => $n->id, $pending));
        self::assertSame(self::PARTNER, $pending[0]->recipient->value);
        self::assertSame('https://1r.partner.example/notifications', $pending[0]->suggestedEndpoint());
        self::assertSame('2026-10-02T12:00:00.000+00:00', $pending[0]->createdAt->format(DATE_RFC3339_EXTENDED));
        self::assertSame(NotificationEventType::LogisticsObjectUpdated, $pending[0]->notification->eventType);
        self::assertSame([Cargo::goodsDescription], $pending[0]->notification->changedProperties);
        self::assertSame('https://1r.example.com/action-requests/c1', $pending[0]->notification->triggeredBy?->value);
        self::assertSame(self::OBJECT . '/logistics-events/e1', $pending[1]->notification->logisticsEvents[0]->value);
        self::assertSame('api:Notification', $pending[1]->notification->toJsonLd()['@type'], 'the document is ready to send');
    }

    public function testQueuedBodiesAreSnapshots(): void
    {
        $outbox = $this->createOutbox();
        $piece = \LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'SECRET')->build(new Iri(self::OBJECT));
        $outbox->enqueue(new OutboundNotification(new Iri(self::PARTNER), new Notification(NotificationEventType::LogisticsObjectCreated, $piece->iri, Cargo::Piece, null, [], [], $piece), new DateTimeImmutable('2026-10-02T12:00:00Z'), 'n-1'));

        // What the caller does with its object afterwards does not change what will be delivered (R2-005).
        $piece->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($piece->iri, new Iri(Cargo::goodsDescription), \LambdaTwelve\OneRecord\Rdf\Literal::string('after-queue')));
        $pending = $this->pending($outbox);
        self::assertCount(1, $pending);
        $json = json_encode($pending[0]->notification->toJsonLd(), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('SECRET', $json);
        self::assertStringNotContainsString('after-queue', $json);
    }

    public function testPendingReadsAreSnapshotsToo(): void
    {
        $outbox = $this->createOutbox();
        $piece = \LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'SECRET')->build(new Iri(self::OBJECT));
        $outbox->enqueue(new OutboundNotification(new Iri(self::PARTNER), new Notification(NotificationEventType::LogisticsObjectCreated, $piece->iri, Cargo::Piece, null, [], [], $piece), new DateTimeImmutable('2026-10-02T12:00:00Z'), 'n-1'));

        // Editing what a read handed out must not change what is delivered later under the same id (R3-003).
        $first = $this->pending($outbox);
        self::assertCount(1, $first);
        $first[0]->notification->body?->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($piece->iri, new Iri(Cargo::goodsDescription), \LambdaTwelve\OneRecord\Rdf\Literal::string('CHANGED-AFTER-READ')));
        $second = $this->pending($outbox);
        self::assertSame('n-1', $second[0]->id);
        $json = json_encode($second[0]->notification->toJsonLd(), JSON_THROW_ON_ERROR);
        self::assertStringContainsString('SECRET', $json);
        self::assertStringNotContainsString('CHANGED-AFTER-READ', $json);
    }
}
