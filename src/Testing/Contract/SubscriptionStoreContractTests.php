<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\SubscriptionStore;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;

/**
 * The tests behind SubscriptionStoreContract, as a trait, for hosts whose test cases must
 * extend a framework base class (Laravel's Testbench, Drupal's KernelTestBase)
 * and so cannot extend the abstract contract. Use it in any PHPUnit TestCase
 * and implement the abstract hook(s); the abstract class is this trait on a
 * bare TestCase.
 */
trait SubscriptionStoreContractTests
{
    protected const string CONTRACT_OBJECT = 'https://1r.example.com/logistics-objects/p1';
    protected const string CONTRACT_PARTNER = 'https://1r.partner.example/logistics-objects/partner';
    protected const string CONTRACT_OTHER_PARTNER = 'https://1r.other.example/logistics-objects/other';
    protected const string CONTRACT_HOLDER = 'https://1r.example.com/logistics-objects/holder';

    /**
     * @return array{requests: ActionRequestStore, subscriptions: SubscriptionStore}
     */
    abstract protected function createStores(): array;

    protected function subscription(string $subscriber, TopicType $type, string $topic, ?DateTimeImmutable $expiresAt = null, bool $body = false): Subscription
    {
        return new Subscription(new Iri($subscriber), $type, $topic, [SubscriptionEventType::LogisticsObjectUpdated, SubscriptionEventType::LogisticsEventReceived], sendLogisticsObjectBody: $body, expiresAt: $expiresAt);
    }

    protected function request(string $id, Subscription $subscription, RequestStatus $status = RequestStatus::Accepted): ActionRequest
    {
        $request = ActionRequest::create(new Iri('https://1r.example.com/action-requests/' . $id), $subscription, $subscription->subscriber, new DateTimeImmutable('2026-10-02T12:00:00Z'));

        return $status === RequestStatus::Pending ? $request : $request->withStatus($status, new DateTimeImmutable('2026-10-02T12:01:00Z'), new Iri(self::CONTRACT_HOLDER));
    }

    public function testOffersAreRegisteredAnsweredAndWithdrawn(): void
    {
        ['subscriptions' => $store] = $this->createStores();
        self::assertSame([], $store->offered(TopicType::Identifier, self::CONTRACT_OBJECT));

        $plain = $this->subscription(self::CONTRACT_HOLDER, TopicType::Identifier, self::CONTRACT_OBJECT);
        $withBody = $this->subscription(self::CONTRACT_HOLDER, TopicType::Identifier, self::CONTRACT_OBJECT, body: true);
        $byType = $this->subscription(self::CONTRACT_HOLDER, TopicType::Type, Cargo::Piece);
        $store->offer($plain);
        $store->offer($withBody);
        $store->offer($byType);

        self::assertCount(2, $store->offered(TopicType::Identifier, self::CONTRACT_OBJECT));
        self::assertCount(1, $store->offered(TopicType::Type, Cargo::Piece));
        self::assertSame(Cargo::Piece, $store->offered(TopicType::Type, Cargo::Piece)[0]->topic);
        self::assertSame([], $store->offered(TopicType::Type, Cargo::Shipment));

        $store->withdraw($plain);
        self::assertSame([], $store->offered(TopicType::Identifier, self::CONTRACT_OBJECT), 'withdraw matches subscriber, topic type and topic, whatever the other fields');
        self::assertCount(1, $store->offered(TopicType::Type, Cargo::Piece), 'other topics untouched');
    }

    public function testSubscribersComeFromAcceptedRequestsOnly(): void
    {
        ['requests' => $requests, 'subscriptions' => $store] = $this->createStores();
        $now = new DateTimeImmutable('2026-10-02T15:00:00Z');
        $object = new Iri(self::CONTRACT_OBJECT);
        // PHPStan remembers a narrowed return type for an identical call, so the empty check uses another object.
        self::assertSame([], $store->subscribersOf(new Iri('https://1r.example.com/logistics-objects/p0'), [Cargo::Piece], $now));

        $requests->save($this->request('by-id', $this->subscription(self::CONTRACT_PARTNER, TopicType::Identifier, self::CONTRACT_OBJECT)));
        $requests->save($this->request('by-type', $this->subscription(self::CONTRACT_OTHER_PARTNER, TopicType::Type, Cargo::Piece)));
        $requests->save($this->request('pending', $this->subscription(self::CONTRACT_OTHER_PARTNER, TopicType::Identifier, self::CONTRACT_OBJECT), RequestStatus::Pending));
        $requests->save($this->request('rejected', $this->subscription(self::CONTRACT_OTHER_PARTNER, TopicType::Identifier, self::CONTRACT_OBJECT), RequestStatus::Rejected));
        $requests->save($this->request('expired', $this->subscription(self::CONTRACT_OTHER_PARTNER, TopicType::Identifier, self::CONTRACT_OBJECT, new DateTimeImmutable('2026-10-02T14:00:00Z'))));
        $requests->save($this->request('elsewhere', $this->subscription(self::CONTRACT_OTHER_PARTNER, TopicType::Identifier, 'https://1r.example.com/logistics-objects/p2')));
        $requests->save($this->request('shipments', $this->subscription(self::CONTRACT_OTHER_PARTNER, TopicType::Type, Cargo::Shipment)));

        $subscribers = $store->subscribersOf($object, [Cargo::Piece], $now);
        $byRequest = [];
        foreach ($subscribers as $entry) {
            $byRequest[basename($entry['request']->value)] = $entry['subscription']->subscriber->value;
        }
        ksort($byRequest);
        self::assertSame(['by-id' => self::CONTRACT_PARTNER, 'by-type' => self::CONTRACT_OTHER_PARTNER], $byRequest, 'accepted, unexpired, covering the object by id or type');

        // Revoking the accepted request removes the subscriber.
        $revoked = $requests->get(new Iri('https://1r.example.com/action-requests/by-id'));
        self::assertNotNull($revoked);
        $requests->save($revoked->withStatus(RequestStatus::Revoked, $now, new Iri(self::CONTRACT_PARTNER)));
        self::assertCount(1, $store->subscribersOf($object, [Cargo::Piece], $now));
        $asShipment = $store->subscribersOf($object, [Cargo::Shipment], $now);
        self::assertCount(1, $asShipment, 'a type subscription covers every object of that type');
        self::assertSame('shipments', basename($asShipment[0]['request']->value));
    }
}
