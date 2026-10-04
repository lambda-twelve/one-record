<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing\Contract;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Api\Verification;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\Operation;
use LambdaTwelve\OneRecord\Change\OperationObject;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore;
use LambdaTwelve\OneRecord\Server\Spi\AuditTrailQuery;
use LambdaTwelve\OneRecord\Server\Spi\StoreException;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;

/**
 * The tests behind ActionRequestStoreContract, as a trait, for hosts whose test cases must
 * extend a framework base class (Laravel's Testbench, Drupal's KernelTestBase)
 * and so cannot extend the abstract contract. Use it in any PHPUnit TestCase
 * and implement the abstract hook(s); the abstract class is this trait on a
 * bare TestCase.
 */
trait ActionRequestStoreContractTests
{
    protected const string CONTRACT_OBJECT = 'https://1r.example.com/logistics-objects/p1';
    protected const string CONTRACT_OTHER = 'https://1r.example.com/logistics-objects/p2';
    protected const string CONTRACT_PARTNER = 'https://1r.partner.example/logistics-objects/partner';
    protected const string CONTRACT_HOLDER = 'https://1r.example.com/logistics-objects/holder';

    abstract protected function createStore(): ActionRequestStore;

    protected function change(string $id, string $at, int $revision = 1, string $object = self::CONTRACT_OBJECT): ActionRequest
    {
        $change = new Change(new Iri($object), $revision, [Operation::add(new Iri($object), new Iri(Cargo::goodsDescription), new OperationObject(Literal::XSD_STRING, 'Books ' . $id))], 'Change ' . $id);

        return ActionRequest::create(new Iri('https://1r.example.com/action-requests/' . $id), $change, new Iri(self::CONTRACT_PARTNER), new DateTimeImmutable($at));
    }

    protected function subscription(string $id, string $at, string $topic = self::CONTRACT_OBJECT, TopicType $type = TopicType::Identifier): ActionRequest
    {
        $subscription = new Subscription(new Iri(self::CONTRACT_PARTNER), $type, $topic, [SubscriptionEventType::LogisticsObjectUpdated]);

        return ActionRequest::create(new Iri('https://1r.example.com/action-requests/' . $id), $subscription, new Iri(self::CONTRACT_PARTNER), new DateTimeImmutable($at));
    }

    protected function verification(string $id, string $at, string $object = self::CONTRACT_OBJECT): ActionRequest
    {
        $verification = new Verification(new Iri($object), [Error::of('Weight missing', '422', 'No grossWeight.', Cargo::grossWeight)], 1);

        return ActionRequest::create(new Iri('https://1r.example.com/action-requests/' . $id), $verification, new Iri(self::CONTRACT_PARTNER), new DateTimeImmutable($at));
    }

    protected function delegation(string $id, string $at, string $object = self::CONTRACT_OBJECT): ActionRequest
    {
        $delegation = new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::CONTRACT_PARTNER)], [new Iri($object)]);

        return ActionRequest::create(new Iri('https://1r.example.com/action-requests/' . $id), $delegation, new Iri(self::CONTRACT_PARTNER), new DateTimeImmutable($at));
    }

    /**
     * @param list<ActionRequest> $requests
     * @return list<string>
     */
    private static function ids(array $requests): array
    {
        return array_map(static fn(ActionRequest $r): string => basename($r->iri->value), $requests);
    }

    public function testSaveGetAndReplace(): void
    {
        $store = $this->createStore();
        $request = $this->change('c1', '2026-10-02T12:00:00.000Z');
        self::assertNull($store->get($request->iri));

        $store->save($request);
        $read = $store->get($request->iri);
        self::assertNotNull($read);
        self::assertSame(RequestStatus::Pending, $read->status);
        self::assertSame(ActionRequestType::Change, $read->type);
        self::assertSame(self::CONTRACT_PARTNER, $read->requestedBy->value);
        self::assertSame('2026-10-02T12:00:00.000+00:00', $read->requestedAt->format(DATE_RFC3339_EXTENDED));
        self::assertInstanceOf(Change::class, $read->payload);
        self::assertSame('Change c1', $read->payload->description);

        $accepted = $request->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:05:00.000Z'), new Iri(self::CONTRACT_HOLDER));
        $store->save($accepted);
        $read = $store->get($request->iri);
        self::assertNotNull($read);
        self::assertSame(RequestStatus::Accepted, $read->status);
        self::assertSame('2026-10-02T12:05:00.000+00:00', $read->statusSince?->format(DATE_RFC3339_EXTENDED));
        self::assertCount(1, $read->history, 'the pending phase is history');
        self::assertSame(RequestStatus::Pending, $read->history[0]->status);
        self::assertSame(self::CONTRACT_HOLDER, $read->history[0]->changedBy?->value);

        $failed = $accepted->withStatus(RequestStatus::Failed, new DateTimeImmutable('2026-10-02T12:06:00.000Z'), null, [Error::of('Conflict', '409', 'Stale revision.')]);
        $store->save($failed);
        self::assertSame('Conflict', $store->get($request->iri)?->errors[0]->title ?? null, 'errors are kept');
    }

    public function testEveryPayloadTypeRoundTrips(): void
    {
        $store = $this->createStore();
        foreach ([$this->subscription('s1', '2026-10-02T12:00:00Z'), $this->verification('v1', '2026-10-02T12:00:00Z'), $this->delegation('d1', '2026-10-02T12:00:00Z')] as $request) {
            $store->save($request);
            $read = $store->get($request->iri);
            self::assertNotNull($read);
            self::assertSame($request->type, $read->type);
            self::assertSame($request->payload::class, $read->payload::class);
        }
        $subscription = $store->get(new Iri('https://1r.example.com/action-requests/s1'))?->payload;
        self::assertInstanceOf(Subscription::class, $subscription);
        self::assertSame(self::CONTRACT_OBJECT, $subscription->topic);
        $verification = $store->get(new Iri('https://1r.example.com/action-requests/v1'))?->payload;
        self::assertInstanceOf(Verification::class, $verification);
        self::assertSame('Weight missing', $verification->errors[0]->title);
        $delegation = $store->get(new Iri('https://1r.example.com/action-requests/d1'))?->payload;
        self::assertInstanceOf(AccessDelegation::class, $delegation);
        self::assertSame([Permission::GetLogisticsObject], $delegation->permissions);
    }

    public function testAuditTrailListsChangeAndVerificationRequestsOfOneObjectOldestFirst(): void
    {
        $store = $this->createStore();
        $store->save($this->change('c2', '2026-10-02T13:00:00Z'));
        $store->save($this->change('c1', '2026-10-02T12:00:00Z')->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:30:00Z'), new Iri(self::CONTRACT_HOLDER)));
        $store->save($this->verification('v1', '2026-10-02T14:00:00Z'));
        $store->save($this->subscription('s1', '2026-10-02T12:10:00Z'), );
        $store->save($this->delegation('d1', '2026-10-02T12:20:00Z'));
        $store->save($this->change('other', '2026-10-02T12:00:00Z', 1, self::CONTRACT_OTHER));
        $object = new Iri(self::CONTRACT_OBJECT);

        self::assertSame(['c1', 'c2', 'v1'], self::ids($store->auditTrail($object, AuditTrailQuery::all())), 'changes and verifications about this object, by request time');
        self::assertSame(['c1'], self::ids($store->auditTrail($object, new AuditTrailQuery(status: RequestStatus::Accepted))));
        self::assertSame(['c2', 'v1'], self::ids($store->auditTrail($object, new AuditTrailQuery(status: RequestStatus::Pending))));
        self::assertSame(['c2', 'v1'], self::ids($store->auditTrail($object, new AuditTrailQuery(updatedFrom: new DateTimeImmutable('2026-10-02T12:45:00Z')))), 'updated-from compares the last status change');
        self::assertSame(['c1'], self::ids($store->auditTrail($object, new AuditTrailQuery(updatedTo: new DateTimeImmutable('2026-10-02T12:45:00Z')))));
        self::assertSame([], $store->auditTrail(new Iri('https://1r.example.com/logistics-objects/none'), AuditTrailQuery::all()));
    }

    public function testPendingChangesAndAcceptedByType(): void
    {
        $store = $this->createStore();
        $store->save($this->change('c1', '2026-10-02T12:00:00Z')->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:30:00Z'), new Iri(self::CONTRACT_HOLDER)));
        $store->save($this->change('c2', '2026-10-02T13:00:00Z'));
        $store->save($this->change('c3', '2026-10-02T13:30:00Z'));
        $store->save($this->verification('v1', '2026-10-02T14:00:00Z'));
        $store->save($this->subscription('s1', '2026-10-02T12:00:00Z')->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:01:00Z'), new Iri(self::CONTRACT_HOLDER)));
        $store->save($this->subscription('s2', '2026-10-02T12:00:00Z'));
        $store->save($this->delegation('d1', '2026-10-02T12:00:00Z')->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:01:00Z'), new Iri(self::CONTRACT_HOLDER)));

        self::assertSame(['c2', 'c3'], self::ids($store->pendingChanges(new Iri(self::CONTRACT_OBJECT))), 'pending changes only, no verifications');
        self::assertSame(['s1'], self::ids($store->accepted(ActionRequestType::Subscription)));
        self::assertSame(['d1'], self::ids($store->accepted(ActionRequestType::AccessDelegation)));
        self::assertSame(['c1'], self::ids($store->accepted(ActionRequestType::Change)));
        self::assertSame([], $store->accepted(ActionRequestType::Verification));
    }

    public function testTransitionIsCompareAndSetOnTheStoredStatus(): void
    {
        $store = $this->createStore();
        $pending = $this->change('c1', '2026-10-02T12:00:00Z');
        $accepted = $pending->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:05:00Z'), new Iri(self::CONTRACT_HOLDER));
        $revoked = $pending->withStatus(RequestStatus::Revoked, new DateTimeImmutable('2026-10-02T12:06:00Z'), new Iri(self::CONTRACT_PARTNER));

        try {
            $store->transition($accepted, RequestStatus::Pending);
            self::fail('an unknown request cannot transition');
        } catch (StoreException $e) {
            self::assertSame(StoreException::NOT_FOUND, $e->kind);
        }
        $store->save($pending);
        $store->transition($accepted, RequestStatus::Pending);
        self::assertSame(RequestStatus::Accepted, $store->get($pending->iri)?->status);

        try {
            $store->transition($revoked, RequestStatus::Pending);
            self::fail('a decision made on a stale snapshot must not win');
        } catch (StoreException $e) {
            self::assertSame(StoreException::STATUS_CONFLICT, $e->kind);
        }
        self::assertSame(RequestStatus::Accepted, $store->get($pending->iri)->status, 'the stored state is untouched');
    }
}
