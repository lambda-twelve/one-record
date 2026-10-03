<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use Psr\Http\Message\ResponseInterface;

/**
 * Subscriptions (both directions), access delegations, verification requests
 * and the 2.3 bulk event endpoint, each through HTTP and the holder's API.
 */
final class SubscriptionsDelegationsAndVerificationTest extends ServerTestCase
{
    private const string FIXTURES = __DIR__ . '/../Fixtures/spec/2026-07/';

    /**
     * @param array<string, string> $headers
     */
    private function post(string $path, string $body, string $agent = self::PARTNER, array $headers = []): ResponseInterface
    {
        return $this->request('POST', $path, $agent, $headers, $body);
    }

    private function subscription(string $topic, TopicType $type = TopicType::Identifier, string $subscriber = self::PARTNER, bool $body = false): Subscription
    {
        return new Subscription(new Iri($subscriber), $type, $topic, [SubscriptionEventType::LogisticsObjectCreated, SubscriptionEventType::LogisticsObjectUpdated, SubscriptionEventType::LogisticsEventReceived], sendLogisticsObjectBody: $body);
    }

    public function testPublishersAskWhatWeWantToSubscribeTo(): void
    {
        $piece = $this->storePiece();
        $query = '/subscriptions?topicType=LOGISTICS_OBJECT_IDENTIFIER&topic=' . rawurlencode($piece->iri->value);

        self::assertError($this->request('GET', '/subscriptions'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/subscriptions?topicType=NOPE&topic=x'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/subscriptions?topicType=LOGISTICS_OBJECT_TYPE'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/subscriptions?topicType=LOGISTICS_OBJECT_TYPE&topic=' . rawurlencode(Cargo::Value)), 400, 'Invalid resource');
        // A publisher asks about its own object, which lives on its server: not a local-existence check (AR-006).
        self::assertSame(200, $this->request('GET', '/subscriptions?topicType=LOGISTICS_OBJECT_IDENTIFIER&topic=' . rawurlencode('https://elsewhere.example/logistics-objects/x'))->getStatusCode());
        self::assertError($this->request('GET', '/subscriptions?topicType=LOGISTICS_OBJECT_IDENTIFIER&topic=' . rawurlencode('not a uri')), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', $query, null), 401);

        // No interest registered: an empty collection, not an error.
        $none = $this->request('GET', $query);
        self::assertSame(200, $none->getStatusCode());
        self::assertSame(Api::Collection, $none->getHeaderLine('Type'));
        self::assertSame(0, self::json($none)['api:hasTotalItems']);

        $this->server->subscriptions->offer($this->subscription($piece->iri->value, subscriber: self::HOLDER));
        $one = $this->request('GET', $query);
        self::assertSame(200, $one->getStatusCode(), (string) $one->getBody());
        self::assertSame(Api::Subscription, $one->getHeaderLine('Type'));
        $body = self::json($one);
        self::assertSame('api:Subscription', $body['@type']);
        self::assertSame(['@id' => self::HOLDER], $body['api:hasSubscriber']);
        self::assertSame(['@id' => 'api:LOGISTICS_OBJECT_IDENTIFIER'], $body['api:hasTopicType']);
        self::assertSame($piece->iri->value, self::arr($body['api:hasTopic'])['@value']);
        self::assertCount(3, self::arr($body['api:includeSubscriptionEventType']));

        // Several offers for one topic are a collection; the version parameter is echoed.
        $this->server->subscriptions->offer($this->subscription($piece->iri->value, subscriber: self::HOLDER, body: true));
        $two = $this->request('GET', $query, headers: ['Accept' => 'application/ld+json; version=2.2.0']);
        self::assertSame('application/ld+json; version=2.2.0', $two->getHeaderLine('Content-Type'));
        self::assertSame(Api::Collection, $two->getHeaderLine('Type'));
        self::assertSame(2, self::json($two)['api:hasTotalItems']);

        $head = $this->request('HEAD', $query);
        self::assertSame(200, $head->getStatusCode());
        self::assertSame('', (string) $head->getBody());

        $byType = '/subscriptions?topicType=' . rawurlencode(Api::LOGISTICS_OBJECT_TYPE) . '&topic=' . rawurlencode(Cargo::Piece);
        $this->server->subscriptions->offer($this->subscription(Cargo::Piece, TopicType::Type, self::HOLDER));
        self::assertSame(Api::Subscription, $this->request('GET', $byType)->getHeaderLine('Type'), 'the IRI spelling of the topic type works too');
    }

    public function testASubscriberIsNotifiedOnceTheHolderAccepts(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);
        $body = (string) file_get_contents(self::FIXTURES . 'Subscription_example1.json');
        $body = str_replace(['https://1r.example.com/logistics-objects/957e2622-9d31-493b-8b8f-3c805064dbda', 'https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c'], [self::PARTNER, $piece->iri->value], $body);

        $response = $this->post('/subscriptions', $body);

        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame(Api::SubscriptionRequest, $response->getHeaderLine('Type'));
        $location = $response->getHeaderLine('Location');
        $read = self::json($this->request('GET', substr($location, \strlen(self::BASE))));
        self::assertSame('api:SubscriptionRequest', $read['@type']);
        self::assertSame(['@id' => 'api:REQUEST_PENDING'], $read['api:hasRequestStatus']);
        self::assertSame('api:Subscription', self::arr($read['api:hasSubscription'])['@type']);

        // Pending means silent: an event on the object produces no notification.
        $event = json_encode(['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => 'cargo:LogisticsEvent', 'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z'], 'cargo:eventCode' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP']], JSON_THROW_ON_ERROR);
        self::assertSame(201, $this->post('/logistics-objects/piece-1/logistics-events', $event)->getStatusCode());
        self::assertSame([], $this->server->outbox->all());

        $holder = new DataHolder($this->server->services);
        $holder->accept(new Iri($location));

        $eventResponse = $this->post('/logistics-objects/piece-1/logistics-events', $event);
        self::assertSame(201, $eventResponse->getStatusCode());
        $notifications = $this->server->outbox->drain();
        self::assertCount(1, $notifications);
        self::assertSame(self::PARTNER, $notifications[0]->recipient->value);
        self::assertSame(NotificationEventType::LogisticsEventReceived, $notifications[0]->notification->eventType);
        self::assertSame($piece->iri->value, $notifications[0]->notification->logisticsObject?->value);
        self::assertSame([$eventResponse->getHeaderLine('Location')], array_map(static fn(Iri $i): string => $i->value, $notifications[0]->notification->logisticsEvents));
        self::assertSame('https://1r.partner.example/notifications', $notifications[0]->suggestedEndpoint(), 'the partner server behind the agent URI');
        $json = $notifications[0]->notification->toJsonLd();
        self::assertSame('api:Notification', $json['@type']);
        self::assertSame(['@id' => 'api:LOGISTICS_EVENT_RECEIVED'], $json['api:hasEventType']);
        self::assertSame(['@id' => $eventResponse->getHeaderLine('Location')], $json['api:hasLogisticsEvent']);

        // An update by the holder reaches the subscriber with the changed properties.
        $holder->update(ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Magazines')->set(Cargo::coload, false)->build($piece->iri));
        $notifications = $this->server->outbox->drain();
        self::assertCount(1, $notifications);
        self::assertSame(NotificationEventType::LogisticsObjectUpdated, $notifications[0]->notification->eventType);
        self::assertSame([Cargo::goodsDescription, Cargo::grossWeight], $notifications[0]->notification->changedProperties);
        self::assertSame(Cargo::Piece, $notifications[0]->notification->logisticsObjectType);

        // The subscriber revokes: silence again.
        $revoked = $this->request('DELETE', substr($location, \strlen(self::BASE)));
        self::assertSame(204, $revoked->getStatusCode());
        $holder->update(ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Newspapers')->set(Cargo::coload, false)->build($piece->iri));
        self::assertSame([], $this->server->outbox->all());
    }

    public function testTypeSubscriptionsCoverNewObjectsAndCanEmbedTheBody(): void
    {
        $holder = new DataHolder($this->server->services);
        $request = $holder->subscribe($this->subscription(Cargo::Piece, TopicType::Type, body: true));
        self::assertSame(RequestStatus::Accepted, $request->status);

        // A new object: the subscriber has no read grant on it yet, so it learns of it without the body.
        $holder->create($this->piece('piece-9'));
        $notifications = $this->server->outbox->drain();
        self::assertCount(1, $notifications);
        self::assertSame(NotificationEventType::LogisticsObjectCreated, $notifications[0]->notification->eventType);
        self::assertNull($notifications[0]->notification->body, 'the body follows read access, not the subscription alone');
        self::assertSame(['@id' => self::BASE . '/logistics-objects/piece-9'], $notifications[0]->notification->toJsonLd()['api:hasLogisticsObject']);

        // A subscriber the policy lets read everything (the host's own system, say) gets the body embedded.
        $this->server->policy->addInternal(new Iri(self::PARTNER));
        $holder->create($this->piece('piece-9b'));
        $notifications = $this->server->outbox->drain();
        $json = $notifications[0]->notification->toJsonLd();
        $embedded = self::arr($json['api:hasLogisticsObject']);
        self::assertSame(self::BASE . '/logistics-objects/piece-9b', $embedded['@id']);
        self::assertSame('Books', $embedded['cargo:goodsDescription']);

        // A Shipment is not a Piece.
        $holder->create(ObjectBuilder::of(Cargo::Shipment)->set(Cargo::goodsDescription, 'Books')->build(new Iri(self::BASE . '/logistics-objects/shipment-1')));
        self::assertSame([], $this->server->outbox->all());

        // An expired subscription no longer counts.
        $expiring = new Subscription(new Iri(self::STRANGER), TopicType::Type, Cargo::Piece, [SubscriptionEventType::LogisticsObjectCreated], expiresAt: $this->clock->now()->modify('+1 hour'));
        $holder->subscribe($expiring);
        $holder->create($this->piece('piece-10'));
        self::assertCount(2, $this->server->outbox->drain());
        $this->clock->advance('+2 hours');
        $holder->create($this->piece('piece-11'));
        self::assertCount(1, $this->server->outbox->drain());
    }

    public function testSubscriptionRequestsAreValidated(): void
    {
        $this->storePiece('piece-1', null);
        $valid = $this->subscription(self::BASE . '/logistics-objects/piece-1')->toJsonLd();
        self::assertSame(201, $this->post('/subscriptions', json_encode($valid, JSON_THROW_ON_ERROR))->getStatusCode());

        $missing = $valid;
        $missing['api:hasTopic'] = ['@type' => 'http://www.w3.org/2001/XMLSchema#anyURI', '@value' => self::BASE . '/logistics-objects/nope'];
        self::assertError($this->post('/subscriptions', json_encode($missing, JSON_THROW_ON_ERROR)), 400, 'Invalid resource');

        $foreign = $valid;
        $foreign['api:hasTopic'] = ['@type' => 'http://www.w3.org/2001/XMLSchema#anyURI', '@value' => 'https://elsewhere.example/logistics-objects/x'];
        self::assertError($this->post('/subscriptions', json_encode($foreign, JSON_THROW_ON_ERROR)), 400, 'Invalid resource');

        $noEvents = $valid;
        unset($noEvents['api:includeSubscriptionEventType']);
        self::assertError($this->post('/subscriptions', json_encode($noEvents, JSON_THROW_ON_ERROR)), 400);

        self::assertError($this->post('/subscriptions', 'nope'), 400, 'Invalid body request');
        self::assertError($this->post('/subscriptions', json_encode($valid, JSON_THROW_ON_ERROR), headers: ['Content-Type' => 'text/plain']), 415);
        self::assertError($this->request('PUT', '/subscriptions'), 405);

        // A hidden object looks like a missing one.
        $this->server = $this->makeServer(Decision::Hide);
        $this->storePiece('piece-1', null);
        self::assertError($this->post('/subscriptions', json_encode($valid, JSON_THROW_ON_ERROR)), 400, 'Invalid resource');
    }

    public function testAccessDelegationGrantsAndRevokesPermissions(): void
    {
        $piece = $this->storePiece();
        $body = (string) file_get_contents(self::FIXTURES . 'AccessDelegation_example1.json');
        $body = str_replace(['https://1r.example.com/logistics-objects/Airline_XYZ', 'https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c'], [self::STRANGER, $piece->iri->value], $body);

        self::assertError($this->request('GET', '/logistics-objects/piece-1', self::STRANGER), 403);

        $response = $this->post('/access-delegations', $body);
        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame(Api::AccessDelegationRequest, $response->getHeaderLine('Type'));
        $location = $response->getHeaderLine('Location');
        $path = substr($location, \strlen(self::BASE));

        // The delegate is a party to the request and may read it; a third party may not.
        $read = self::json($this->request('GET', $path, self::STRANGER));
        self::assertSame('api:AccessDelegationRequest', $read['@type']);
        // Both editions' examples write the delegates as an array, so one delegate is a one-element array.
        self::assertSame([['@id' => self::STRANGER]], self::arr($read['api:hasAccessDelegation'])['api:isRequestedFor']);
        $at22 = self::json($this->request('GET', $path, self::STRANGER, ['Accept' => 'application/ld+json; version=2.2.0']));
        self::assertSame([['@id' => self::STRANGER]], self::arr($at22['api:hasAccessDelegation'])['api:isRequestedFor']);
        self::assertError($this->request('GET', $path, 'https://nobody.example/logistics-objects/x'), 404);

        $accepted = $this->request('PATCH', $path . '?status=REQUEST_ACCEPTED', self::HOLDER);
        self::assertSame(204, $accepted->getStatusCode(), (string) $accepted->getBody());

        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1', self::STRANGER)->getStatusCode(), 'GET_LOGISTICS_OBJECT was delegated');
        self::assertError($this->request('GET', '/logistics-objects/piece-1/logistics-events', self::STRANGER), 403);
        $notifications = $this->server->outbox->drain();
        self::assertCount(1, $notifications);
        self::assertSame(self::STRANGER, $notifications[0]->recipient->value);
        self::assertSame(NotificationEventType::LogisticsObjectAccessGranted, $notifications[0]->notification->eventType);
        self::assertSame($location, $notifications[0]->notification->triggeredBy?->value);

        // Accepted delegations can be revoked; the grants go with them.
        $revoked = $this->request('DELETE', $path, self::HOLDER);
        self::assertSame(204, $revoked->getStatusCode());
        self::assertError($this->request('GET', '/logistics-objects/piece-1', self::STRANGER), 403);
        self::assertSame(['@id' => 'api:REQUEST_REVOKED'], self::json($this->request('GET', $path, self::PARTNER))['api:hasRequestStatus']);
    }

    public function testAccessDelegationsExpireAndAreValidated(): void
    {
        $piece = $this->storePiece();
        $delegation = new AccessDelegation([Permission::GetLogisticsObject, Permission::GetLogisticsEvent], [new Iri(self::STRANGER)], [$piece->iri], 'Handling', expiresAt: $this->clock->now()->modify('+1 day'));
        $response = $this->post('/access-delegations', json_encode($delegation->toJsonLd(), JSON_THROW_ON_ERROR), headers: ['Content-Type' => 'application/ld+json; version=2.3.0']);
        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        (new DataHolder($this->server->services))->accept(new Iri($response->getHeaderLine('Location')));

        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1', self::STRANGER)->getStatusCode());
        $this->clock->advance('+2 days');
        self::assertError($this->request('GET', '/logistics-objects/piece-1', self::STRANGER), 403);

        // The 2.2 shape (array of delegates) is read when the body says 2.2.
        $old = $delegation->toJsonLd(\LambdaTwelve\OneRecord\Spec\ApiVersion::V2_2_0);
        self::assertSame(201, $this->post('/access-delegations', json_encode($old, JSON_THROW_ON_ERROR), headers: ['Content-Type' => 'application/ld+json; version=2.2.0'])->getStatusCode());

        $foreign = new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [new Iri('https://elsewhere.example/logistics-objects/x')]);
        self::assertError($this->post('/access-delegations', json_encode($foreign->toJsonLd(), JSON_THROW_ON_ERROR)), 400, 'Invalid resource');
        $missing = new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [new Iri(self::BASE . '/logistics-objects/nope')]);
        self::assertError($this->post('/access-delegations', json_encode($missing->toJsonLd(), JSON_THROW_ON_ERROR)), 400, 'Invalid resource');
        self::assertError($this->post('/access-delegations', '{"@context": {"api": "https://onerecord.iata.org/ns/api#"}, "@type": "api:AccessDelegation"}'), 400);
        self::assertError($this->post('/access-delegations', '[', self::PARTNER), 400, 'Invalid body request');
        self::assertError($this->request('GET', '/access-delegations'), 405);
    }

    public function testVerificationRequestsAreAcknowledgedOrRejected(): void
    {
        $piece = $this->storePiece();
        $body = str_replace('https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c', $piece->iri->value, (string) file_get_contents(self::FIXTURES . 'Verification.json'));

        $response = $this->post('/logistics-objects/piece-1', $body, self::STRANGER);
        self::assertSame(201, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame(Api::VerificationRequest, $response->getHeaderLine('Type'));
        $location = $response->getHeaderLine('Location');
        $path = substr($location, \strlen(self::BASE));

        $read = self::json($this->request('GET', $path, self::STRANGER));
        self::assertSame('api:VerificationRequest', $read['@type']);
        $verification = self::arr($read['api:hasVerification']);
        self::assertSame('api:Verification', $verification['@type']);
        self::assertCount(2, self::arr($verification['api:hasError']));

        // A verification is acknowledged, never accepted.
        self::assertError($this->request('PATCH', $path . '?status=REQUEST_ACCEPTED', self::HOLDER), 422);
        $acknowledged = $this->request('PATCH', $path . '?status=REQUEST_ACKNOWLEDGED', self::HOLDER);
        self::assertSame(204, $acknowledged->getStatusCode(), (string) $acknowledged->getBody());
        self::assertSame(['@id' => 'api:REQUEST_ACKNOWLEDGED'], self::json($this->request('GET', $path, self::STRANGER))['api:hasRequestStatus']);
        self::assertError($this->request('DELETE', $path, self::STRANGER), 422);

        $second = $this->post('/logistics-objects/piece-1', $body, self::STRANGER)->getHeaderLine('Location');
        (new DataHolder($this->server->services))->reject(new Iri($second));
        self::assertSame(['@id' => 'api:REQUEST_REJECTED'], self::json($this->request('GET', substr($second, \strlen(self::BASE)), self::STRANGER))['api:hasRequestStatus']);

        self::assertError($this->post('/logistics-objects/nope', $body, self::STRANGER), 404);
        self::assertError($this->post('/logistics-objects/piece-1', str_replace($piece->iri->value, self::BASE . '/logistics-objects/other', $body), self::STRANGER), 400, 'Invalid resource');
        self::assertError($this->post('/logistics-objects/piece-1', str_replace('"@value": "1"', '"@value": "9"', $body), self::STRANGER), 422);
        self::assertError($this->post('/logistics-objects/piece-1', '{"@context": {"api": "https://onerecord.iata.org/ns/api#"}, "@type": "api:Change"}', self::STRANGER), 400);

        $this->server = $this->makeServer(Decision::Hide);
        $this->storePiece('piece-1', null);
        self::assertError($this->post('/logistics-objects/piece-1', $body, self::STRANGER), 404);
    }

    public function testBulkEventsAnswerPerObject(): void
    {
        $this->server = $this->makeServer(bulkEvents: true);
        $allowed = $this->storePiece('allowed');
        $this->storePiece('forbidden');
        $this->server->policy->allow(new Iri(self::PARTNER), $allowed->iri, [Permission::PostLogisticsEvent]);
        $body = str_replace(
            ['https://1r.example.com/logistics-objects/78fee8e2-772b-4fff-bede-cfda67900d3b', 'https://1r.example.com/logistics-objects/f166f1fa-ea2d-4c01-b1d3-cde8bb973757', 'https://1r.example.com/logistics-objects/22f07756-9d67-4622-adab-c62d1ebde115'],
            [$allowed->iri->value, self::BASE . '/logistics-objects/missing', self::BASE . '/logistics-objects/forbidden'],
            (string) file_get_contents(self::FIXTURES . 'MultipleEvents_example1.json'),
        );

        $response = $this->post('/logistics-events', $body);

        self::assertSame(207, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame(Api::MultiStatusResponse, $response->getHeaderLine('Type'));
        $json = self::json($response);
        self::assertSame('api:MultiStatusResponse', $json['@type']);
        self::assertSame(3, $json['api:hasTotalItems']);
        self::assertSame(1, $json['api:hasTotalCreated']);
        self::assertSame(2, $json['api:hasTotalFailed']);
        $results = array_map(self::arr(...), self::arr($json['api:hasCreationResult']));
        self::assertSame([201, 404, 403], array_column($results, 'api:hasHTTPStatus'));
        self::assertSame(['@id' => $allowed->iri->value], $results[0]['api:hasLogisticsObject']);
        $eventIri = self::str(self::arr($results[0]['api:hasLogisticsEvent'])['@id']);
        self::assertStringStartsWith($allowed->iri->value . '/logistics-events/', $eventIri);
        self::assertSame('api:Error', self::arr($results[1]['api:hasError'])['@type']);
        self::assertSame('Not authorized to perform action', self::arr($results[2]['api:hasError'])['api:hasTitle']);

        $this->server->policy->allow(new Iri(self::PARTNER), $allowed->iri, [Permission::GetLogisticsEvent]);
        $events = self::json($this->request('GET', '/logistics-objects/allowed/logistics-events'));
        self::assertSame(1, $events['api:hasTotalItems']);

        self::assertError($this->post('/logistics-events', '{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:LogisticsEvent"}'), 400, 'Invalid resource');
        self::assertError($this->post('/logistics-events', $body, headers: ['Accept' => 'application/ld+json; version=2.2.0', 'Content-Type' => 'application/ld+json; version=2.2.0']), 404);

        $this->server = $this->makeServer();
        self::assertError($this->post('/logistics-events', $body), 404);
    }

    public function testAStatusNotificationNamesTheObjectOnlyWhenTheRequestConcernsExactlyOne(): void
    {
        $one = $this->storePiece('piece-1', null);
        $two = $this->storePiece('piece-2', null);
        $requests = new ActionRequests($this->server->services);
        $this->server->outbox->drain();

        $requests->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$one->iri], notifyRequestStatusChange: true), new Iri(self::PARTNER));
        $single = $this->server->outbox->drain();
        self::assertCount(1, $single);
        self::assertSame($one->iri->value, $single[0]->notification->logisticsObject?->value);
        self::assertSame(Cargo::Piece, $single[0]->notification->logisticsObjectType);

        // Two objects: api:hasLogisticsObject allows one at most, so none is named and isTriggeredBy carries the request (spec question 32).
        $request = $requests->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$one->iri, $two->iri], notifyRequestStatusChange: true), new Iri(self::PARTNER));
        $several = $this->server->outbox->drain();
        self::assertCount(1, $several);
        self::assertNull($several[0]->notification->logisticsObject);
        self::assertNull($several[0]->notification->logisticsObjectType);
        self::assertSame($request->iri->value, $several[0]->notification->triggeredBy?->value);
        self::assertArrayNotHasKey('api:hasLogisticsObject', $several[0]->notification->toJsonLd());
    }
}
