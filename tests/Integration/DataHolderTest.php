<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LocalGraph;
use LambdaTwelve\OneRecord\Model\UuidIriMinter;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectCreated;
use LambdaTwelve\OneRecord\Server\Event\LogisticsObjectRevised;
use LambdaTwelve\OneRecord\Server\PublishResult;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;

/**
 * The holder's PHP API: publishing a local graph, republishing it, telling
 * partners about objects and forgetting them.
 */
final class DataHolderTest extends ServerTestCase
{
    private function graph(float $weight = 20.0, string $description = 'Books'): LocalGraph
    {
        return LocalGraph::create()
            ->add('shipment', ObjectBuilder::of(Cargo::Shipment)->set(Cargo::goodsDescription, $description)->add(Cargo::pieces, Values::ref('piece')))
            ->add('piece', ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, $description)->set(Cargo::grossWeight, Values::quantity($weight, MeasurementUnitCode::KGM))->set(Cargo::ofShipment, Values::ref('shipment')))
            ->root('shipment');
    }

    public function testPublishingAGraphCreatesLinkedObjectsAndRepublishingIsIdempotent(): void
    {
        $holder = new DataHolder($this->server->services);
        $minter = new UuidIriMinter(self::BASE, seed: 'test');

        $result = $holder->publish($this->graph(), $minter);

        self::assertSame(['shipment' => PublishResult::CREATED, 'piece' => PublishResult::CREATED], $result->outcomes);
        self::assertTrue($result->changedAnything());
        $iris = $result->iris();
        self::assertSame($iris['shipment']->value, $result->root()->value);
        self::assertCount(2, $this->dispatcher->of(LogisticsObjectCreated::class));

        foreach ($iris as $iri) {
            $this->server->policy->allow(new Iri(self::PARTNER), $iri, [Permission::GetLogisticsObject]);
        }
        $shipment = self::json($this->request('GET', substr($iris['shipment']->value, \strlen(self::BASE))));
        self::assertSame(['@id' => $iris['piece']->value], $shipment['cargo:pieces'], 'local references became URIs');
        $piece = self::json($this->request('GET', substr($iris['piece']->value, \strlen(self::BASE))));
        self::assertSame(['@id' => $iris['shipment']->value], $piece['cargo:ofShipment']);

        // Same graph, same URIs: nothing to do.
        $again = $holder->publish($this->graph(), $minter, $iris);
        self::assertSame(['shipment' => PublishResult::UNCHANGED, 'piece' => PublishResult::UNCHANGED], $again->outcomes);
        self::assertFalse($again->changedAnything());
        self::assertSame('1', $this->request('GET', substr($iris['piece']->value, \strlen(self::BASE)))->getHeaderLine('Latest-Revision'));

        // One changed object: one new revision, recorded as an accepted change request by the holder.
        $this->clock->advance('+1 hour');
        $changed = $holder->publish($this->graph(weight: 21.0), $minter, $iris);
        self::assertSame(['shipment' => PublishResult::UNCHANGED, 'piece' => PublishResult::UPDATED], $changed->outcomes);
        $read = $this->request('GET', substr($iris['piece']->value, \strlen(self::BASE)));
        self::assertSame('2', $read->getHeaderLine('Latest-Revision'));
        self::assertSame(21.0, self::arr(self::json($read)['cargo:grossWeight'])['cargo:numericalValue']);
        $revised = $this->dispatcher->of(LogisticsObjectRevised::class);
        self::assertCount(1, $revised);
        self::assertSame([Cargo::grossWeight], $revised[0]->changedProperties);
        $trail = self::json($this->request('GET', substr($iris['piece']->value, \strlen(self::BASE)) . '/audit-trail'));
        $requests = self::arr($trail['api:hasActionRequest']);
        self::assertCount(1, $requests);
        self::assertSame(['@id' => self::HOLDER], self::arr($requests[0])['api:isRequestedBy']);
        self::assertSame(['@id' => 'api:REQUEST_ACCEPTED'], self::arr($requests[0])['api:hasRequestStatus']);

        // Without the URIs of what was published before, the minter is deterministic for a seed, so the same URIs come back anyway.
        self::assertSame($iris['piece']->value, $minter->mint('piece', [Cargo::Piece])->value);
    }

    public function testAnnouncingAndForgetting(): void
    {
        $holder = new DataHolder($this->server->services);
        $piece = $holder->create($this->piece());
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->object->iri, [Permission::GetLogisticsObject]);

        $holder->announce($piece->object->iri, new Iri(self::PARTNER));
        $outbox = $this->server->outbox->drain();
        self::assertCount(1, $outbox);
        self::assertSame(NotificationEventType::LogisticsObjectAvailable, $outbox[0]->notification->eventType);
        self::assertSame(self::PARTNER, $outbox[0]->recipient->value);
        self::assertSame(Cargo::Piece, $outbox[0]->notification->logisticsObjectType);
        $json = $outbox[0]->notification->toJsonLd();
        self::assertSame(['@id' => 'api:LOGISTICS_OBJECT_AVAILABLE'], $json['api:hasEventType']);
        self::assertSame(['@id' => $piece->object->iri->value], $json['api:hasLogisticsObject']);

        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1')->getStatusCode());
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->object->iri, [Permission::PostLogisticsEvent]);
        $event = json_encode(['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => 'cargo:LogisticsEvent', 'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z']], JSON_THROW_ON_ERROR);
        self::assertSame(201, $this->request('POST', '/logistics-objects/piece-1/logistics-events', body: $event)->getStatusCode());
        $this->server->delegations->grant(new \LambdaTwelve\OneRecord\Server\Spi\Grant(new Iri(self::STRANGER), $piece->object->iri, [Permission::GetLogisticsObject]));
        self::assertCount(1, $this->server->delegations->grantsFor(new Iri(self::STRANGER), $piece->object->iri));

        $holder->forget($piece->object->iri);
        self::assertError($this->request('GET', '/logistics-objects/piece-1'), 404);
        self::assertSame([], $this->server->events->query($piece->object->iri, \LambdaTwelve\OneRecord\Server\Spi\EventQuery::all()), 'events go with the object by default');
        self::assertSame([], $this->server->delegations->grantsFor(new Iri(self::STRANGER), $piece->object->iri), 'grants too');
        self::assertError($this->request('GET', '/logistics-objects/piece-1/audit-trail'), 404);
        self::assertFalse($this->server->objects->exists($piece->object->iri));

        $this->expectException(InvalidArgumentException::class);
        $holder->announce($piece->object->iri, new Iri(self::PARTNER));
    }

    public function testUpdatingAnUnknownObjectIsAnError(): void
    {
        $holder = new DataHolder($this->server->services);
        self::assertNull($holder->update($this->storePiece()), 'identical content is not an update');

        $this->expectException(InvalidArgumentException::class);
        $holder->update($this->piece('ghost'));
    }
}
