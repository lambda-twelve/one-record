<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Api\Verification;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Client\EventFilter;
use LambdaTwelve\OneRecord\Client\OneRecordClient;
use LambdaTwelve\OneRecord\Client\OneRecordHttpException;
use LambdaTwelve\OneRecord\Client\StaticTokenProvider;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Event\NotificationReceived;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Testing\InProcessHttpClient;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * The SDK client driving the SDK server in one process: every client method
 * against the real endpoints, at both API versions.
 */
final class ClientAgainstServerTest extends ServerTestCase
{
    private function client(string $agent = self::PARTNER, ?ApiVersion $version = null): OneRecordClient
    {
        $factory = new Psr17Factory();
        $client = new OneRecordClient(new InProcessHttpClient($this->server->handler, $factory, $factory), $factory, $factory, new StaticTokenProvider($agent), self::BASE, clock: $this->clock);

        return $version === null ? $client : $client->withApiVersion($version);
    }

    public function testDiscoveryAndObjectReads(): void
    {
        $piece = $this->storePiece();
        $client = $this->client();

        $information = $client->serverInformation();
        self::assertSame(self::HOLDER, $information->dataHolder->value);
        self::assertSame(ApiVersion::V2_3_0, $client->apiVersion());

        $read = $client->getLogisticsObject($piece->iri);
        self::assertSame(1, $read->revision);
        self::assertSame(1, $read->latestRevision);
        self::assertSame(Cargo::Piece, $read->type);
        self::assertSame('Books', $read->object?->literal(Cargo::goodsDescription));
        self::assertTrue($read->object->isSameAs($piece), 'the partner reads exactly what the holder stored');

        $head = $client->headLogisticsObject($piece->iri);
        self::assertNull($head->object);
        self::assertSame(1, $head->latestRevision);
        self::assertSame('2026-10-02T12:00:00+00:00', $head->lastModified?->format(DATE_ATOM));

        try {
            $client->getLogisticsObject(self::BASE . '/logistics-objects/nope');
            self::fail('expected 404');
        } catch (OneRecordHttpException $e) {
            self::assertTrue($e->isNotFound());
            self::assertSame('Resource not found', $e->error?->title);
        }
        try {
            $this->client(self::STRANGER)->getLogisticsObject($piece->iri);
            self::fail('expected 403');
        } catch (OneRecordHttpException $e) {
            self::assertTrue($e->isForbidden());
        }
    }

    public function testChangeRequestLifecycleThroughTheClient(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PatchLogisticsObject]);
        $client = $this->client();
        $holderClient = $this->client(self::HOLDER);

        $heavier = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->set(Cargo::coload, false)->set(Cargo::grossWeight, Values::quantity(25.0, MeasurementUnitCode::KGM))->build($piece->iri);
        $change = (new ChangeBuilder())->diff($piece, $heavier, 1, 'Heavier');
        self::assertNotNull($change);

        $requestIri = $client->requestChange($change);
        $request = $client->getActionRequest($requestIri);
        self::assertSame(RequestStatus::Pending, $request->status);
        self::assertSame(self::PARTNER, $request->requestedBy->value);
        self::assertNotNull($request->statusSince, '2.3 carries the timestamp');

        // The holder decides over HTTP with the same client class.
        $this->clock->advance('+1 minute');
        $holderClient->updateActionRequestStatus($requestIri, RequestStatus::Accepted);
        $accepted = $client->getActionRequest($requestIri);
        self::assertSame(RequestStatus::Accepted, $accepted->status);
        self::assertCount(1, $accepted->history);

        $read = $client->getLogisticsObject($piece->iri);
        self::assertSame(2, $read->latestRevision);
        self::assertTrue($read->object?->isSameAs($heavier));
        $old = $client->getLogisticsObject($piece->iri, at: new DateTimeImmutable('2026-10-02T12:00:30Z'));
        self::assertSame(1, $old->revision);
        self::assertSame(2, $old->latestRevision);
        self::assertFalse($old->isLatest());

        $trail = $client->getAuditTrail($piece->iri);
        self::assertSame(2, $trail->latestRevision);
        self::assertCount(1, $trail->requests);
        self::assertSame($requestIri->value, $trail->requests[0]->iri->value);
        self::assertSame(RequestStatus::Accepted, $trail->requests[0]->status);
        self::assertSame([], $client->getAuditTrail($piece->iri, status: RequestStatus::Rejected)->requests);

        try {
            $client->revokeActionRequest($requestIri);
            self::fail('an accepted change cannot be revoked');
        } catch (OneRecordHttpException $e) {
            self::assertSame(422, $e->status);
        }

        // At 2.2 the same partner reads without the 2.3-only properties and gets 400 instead of 422.
        $old22 = $this->client(version: ApiVersion::V2_2_0);
        self::assertNull($old22->getActionRequest($requestIri)->statusSince);
        try {
            $old22->revokeActionRequest($requestIri);
            self::fail('expected 400');
        } catch (OneRecordHttpException $e) {
            self::assertSame(400, $e->status);
        }
    }

    public function testEventsVerificationSubscriptionsAndDelegations(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent, Permission::GetLogisticsEvent]);
        $client = $this->client();
        $holder = new DataHolder($this->server->services);

        $event = ['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => 'cargo:LogisticsEvent', 'cargo:eventName' => 'Departed', 'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z'], 'cargo:eventCode' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP']];
        $eventIri = $client->postLogisticsEvent($piece->iri, $event);
        self::assertStringStartsWith($piece->iri->value . '/logistics-events/', $eventIri->value);

        $one = $client->getLogisticsEvent($eventIri);
        self::assertSame($eventIri->value, $one->iri->value);
        self::assertTrue($one->matchesCode('DEP'));
        self::assertSame($piece->iri->value, $one->logisticsObject->value);
        self::assertSame('2026-10-02T12:00:00+00:00', $one->created->format(DATE_ATOM), 'created when the server received it');

        $list = $client->getLogisticsEvents($piece->iri);
        self::assertSame(1, $list->totalItems);
        self::assertCount(1, $list->events);
        self::assertSame($eventIri->value, $list->events[0]->iri->value);
        self::assertStringContainsString('"Departed"', json_encode($list->events[0]->toJsonLd(), JSON_THROW_ON_ERROR), 'the listed event carries its properties');
        self::assertSame(0, $client->getLogisticsEvents($piece->iri, new EventFilter(eventCodes: ['ARR']))->totalItems);
        self::assertSame(1, $client->getLogisticsEvents($piece->iri, new EventFilter(occurredAfter: new DateTimeImmutable('2026-10-02T10:00:00Z'), sort: EventFilter::SORT_EVENT_DESC, limit: 5))->totalItems);

        // Bulk at 2.3 against a server that has the endpoint off: the client falls back per object.
        $other = $this->storePiece('piece-2');
        $results = $client->postLogisticsEvents($event, [$piece->iri, $other->iri]);
        self::assertTrue($results[0]->created());
        self::assertSame(403, $results[1]->status, 'no POST_LOGISTICS_EVENT on piece-2');
        self::assertSame('Not authorized to perform action', $results[1]->error?->title);

        // And against a server with it on, the bulk endpoint answers per object too.
        $this->server = $this->makeServer(bulkEvents: true);
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);
        $this->storePiece('piece-2');
        $bulk = $this->client()->postLogisticsEvents($event, [$piece->iri, self::BASE . '/logistics-objects/piece-2', self::BASE . '/logistics-objects/nope']);
        self::assertSame([201, 403, 404], array_map(static fn($r): int => $r->status, $bulk));
        self::assertStringStartsWith($piece->iri->value . '/logistics-events/', (string) $bulk[0]->event?->value);
        self::assertSame('Resource not found', $bulk[2]->error?->title);
        self::assertCount(1, $this->server->events->query($piece->iri, \LambdaTwelve\OneRecord\Server\Spi\EventQuery::all()));
        $client = $this->client();
        $holder = new DataHolder($this->server->services);

        // Verification.
        $verificationIri = $client->requestVerification(new Verification($piece->iri, [Error::of('Weight missing', '422', 'No grossWeight.', Cargo::grossWeight)]));
        $payload = $client->getActionRequest($verificationIri)->payload;
        self::assertInstanceOf(Verification::class, $payload);
        self::assertCount(1, $payload->errors);
        self::assertSame('Weight missing', $payload->errors[0]->title);
        $holder->acknowledge($verificationIri);
        self::assertSame(RequestStatus::Acknowledged, $client->getActionRequest($verificationIri)->status);

        // Subscription: pending, accepted, then notifications flow to the outbox.
        $subscriptionIri = $client->subscribe(new Subscription(new Iri(self::PARTNER), TopicType::Identifier, $piece->iri->value, [SubscriptionEventType::LogisticsEventReceived]));
        $holder->accept($subscriptionIri);
        $this->server->outbox->drain();
        $client->postLogisticsEvent($piece->iri, $event);
        $outbox = $this->server->outbox->drain();
        self::assertCount(1, $outbox);
        self::assertSame(self::PARTNER, $outbox[0]->recipient->value);
        $client->revokeActionRequest($subscriptionIri);
        self::assertSame(RequestStatus::Revoked, $client->getActionRequest($subscriptionIri)->status);

        // The publisher asks what we offer; nothing is registered, so an empty list.
        self::assertSame([], $client->getSubscriptions(TopicType::Identifier, $piece->iri->value));

        // Access delegation for a third party, accepted by the holder.
        $delegationIri = $client->requestAccessDelegation(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri], 'Handling'));
        $holder->accept($delegationIri);
        self::assertSame(1, $this->client(self::STRANGER)->getLogisticsObject($piece->iri)->latestRevision);

        // Notifications: the holder's outbox entry is delivered with the client to the partner's server (here: ourselves).
        $client->sendNotification(new Notification(NotificationEventType::LogisticsObjectUpdated, $piece->iri, Cargo::Piece, changedProperties: [Cargo::grossWeight]));
        $received = $this->dispatcher->of(NotificationReceived::class);
        self::assertCount(1, $received);
        self::assertSame(NotificationEventType::LogisticsObjectUpdated, $received[0]->notification->eventType);
    }

    public function testCreatingAnObjectOnThePartnerServer(): void
    {
        $holder = $this->client(self::HOLDER);
        $iri = $holder->createLogisticsObject($this->piece('predefined'));
        self::assertSame(self::BASE . '/logistics-objects/predefined', $iri->value, 'a URI under the server is honoured');
        $minted = $holder->createLogisticsObject($this->piece()->toJsonLd() + ['@id' => null]);
        self::assertStringStartsWith(self::BASE . '/logistics-objects/', $minted->value);
        self::assertNotSame($iri->value, $minted->value);
        self::assertSame(1, $holder->getLogisticsObject($iri)->latestRevision);

        try {
            $this->client()->createLogisticsObject($this->piece());
            self::fail('partners may not create objects here');
        } catch (OneRecordHttpException $e) {
            self::assertTrue($e->isForbidden());
        }
    }
}
