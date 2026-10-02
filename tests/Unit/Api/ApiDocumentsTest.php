<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Api;

use DateTimeImmutable;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\ActionRequestType;
use LambdaTwelve\OneRecord\Api\Collection;
use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\Api\ErrorDocument;
use LambdaTwelve\OneRecord\Api\InvalidDocument;
use LambdaTwelve\OneRecord\Api\Notification;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\RequestStatusEntry;
use LambdaTwelve\OneRecord\Api\ServerInformation;
use LambdaTwelve\OneRecord\Api\Severity;
use LambdaTwelve\OneRecord\Api\Subscription;
use LambdaTwelve\OneRecord\Api\SubscriptionEventType;
use LambdaTwelve\OneRecord\Api\TopicType;
use LambdaTwelve\OneRecord\Api\Verification;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\Nodes;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiFeatures;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Subscription::class)]
#[CoversClass(AccessDelegation::class)]
#[CoversClass(Verification::class)]
#[CoversClass(Notification::class)]
#[CoversClass(ServerInformation::class)]
#[CoversClass(ActionRequest::class)]
#[CoversClass(ActionRequestType::class)]
#[CoversClass(RequestStatus::class)]
#[CoversClass(RequestStatusEntry::class)]
#[CoversClass(NotificationEventType::class)]
#[CoversClass(SubscriptionEventType::class)]
#[CoversClass(TopicType::class)]
#[CoversClass(Permission::class)]
#[CoversClass(ErrorDocument::class)]
#[CoversClass(Collection::class)]
#[CoversClass(Nodes::class)]
#[CoversClass(InvalidDocument::class)]
#[CoversClass(ApiFeatures::class)]
#[UsesClass(Error::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Api\ErrorDetail::class)]
#[UsesClass(Severity::class)]
#[UsesClass(Change::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Change\Operation::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Change\OperationObject::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Change\OperationKind::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Change\ChangeException::class)]
#[UsesClass(JsonLd::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Expander::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\ExpandedDocument::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Context::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Json::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\JsonLdException::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Writer::class)]
#[UsesClass(Comparer::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Diff::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Model\LogisticsObject::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\Vocabulary::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\ClassInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\PropertyInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\IndividualInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Vocabulary\CodeListInfo::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\BlankNode::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Literal::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Triple::class)]
#[UsesClass(ApiVersion::class)]
#[UsesClass(DataModelVersion::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Spec\Edition::class)]
final class ApiDocumentsTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/../../Fixtures/spec/2026-07/';
    private const string ORG = 'https://1r.example.com/logistics-objects/957e2622-9d31-493b-8b8f-3c805064dbda';
    private const string PIECE = 'https://1r.example.com/logistics-objects/1a8ded38-1804-467c-a369-81a411416b7c';

    private static function fixture(string $name): string
    {
        return (string) file_get_contents(self::FIXTURES . $name . '.json');
    }

    /**
     * @param string|array<string, mixed> $expected
     * @param array<string, mixed> $actual
     */
    private static function assertEquivalentJsonLd(string|array $expected, array $actual, string $message = ''): void
    {
        $diff = (new Comparer())->compare(JsonLd::expand($expected)->graph, JsonLd::expand($actual)->graph);
        self::assertTrue($diff->isEqual(), $message . "\n" . $diff->describe());
    }

    public function testSubscriptionRoundTripsTheSpecExample(): void
    {
        $subscription = Subscription::fromJsonLd(self::fixture('Subscription_example1'));

        self::assertSame(self::ORG, $subscription->subscriber->value);
        self::assertSame(TopicType::Identifier, $subscription->topicType);
        self::assertSame(self::PIECE, $subscription->topic);
        self::assertSame([SubscriptionEventType::LogisticsEventReceived, SubscriptionEventType::LogisticsObjectCreated, SubscriptionEventType::LogisticsObjectUpdated], $subscription->eventTypes);
        self::assertSame(['application/ld+json'], $subscription->contentTypes);
        self::assertFalse($subscription->sendLogisticsObjectBody);
        self::assertTrue($subscription->covers(new Iri(self::PIECE), [Cargo::Piece]));
        self::assertFalse($subscription->covers(new Iri('https://1r.example.com/logistics-objects/other'), [Cargo::Piece]));
        self::assertTrue($subscription->includes(SubscriptionEventType::LogisticsObjectUpdated));
        self::assertEquivalentJsonLd(self::fixture('Subscription_example1'), $subscription->toJsonLd());

        $byType = Subscription::fromJsonLd(['@context' => Nodes::context(), '@type' => 'api:Subscription', 'api:hasSubscriber' => Nodes::ref(self::ORG), 'api:hasTopicType' => Nodes::ref('api:LOGISTICS_OBJECT_TYPE'), 'api:hasTopic' => Cargo::Shipment, 'api:includeSubscriptionEventType' => Nodes::ref('api:LOGISTICS_OBJECT_CREATED'), 'api:expiresAt' => Nodes::dateTimeValue(new DateTimeImmutable('2027-01-01T00:00:00Z')), 'api:sendLogisticsObjectBody' => true]);
        self::assertTrue($byType->covers(new Iri('https://x/any'), [Cargo::Shipment, Cargo::LogisticsObject]));
        self::assertFalse($byType->covers(new Iri('https://x/any'), [Cargo::Piece]));
        self::assertTrue($byType->isExpiredAt(new DateTimeImmutable('2027-06-01T00:00:00Z')));
        self::assertFalse($byType->isExpiredAt(new DateTimeImmutable('2026-06-01T00:00:00Z')));
        self::assertTrue($byType->sendLogisticsObjectBody);
    }

    public function testAccessDelegationRoundTripsAndHonoursTheVersionRules(): void
    {
        $delegation = AccessDelegation::fromJsonLd(self::fixture('AccessDelegation_example1'), ApiVersion::V2_3_0);

        self::assertSame([Permission::GetLogisticsObject], $delegation->permissions);
        self::assertSame(['https://1r.example.com/logistics-objects/Airline_XYZ'], array_map(static fn(Iri $i): string => $i->value, $delegation->delegates));
        self::assertSame([self::PIECE], array_map(static fn(Iri $i): string => $i->value, $delegation->logisticsObjects));
        self::assertFalse($delegation->notifyRequestStatusChange);
        self::assertEquivalentJsonLd(self::fixture('AccessDelegation_example1'), $delegation->toJsonLd(ApiVersion::V2_3_0));

        $two = ['@context' => Nodes::context(), '@type' => 'api:AccessDelegation', 'api:hasPermission' => [Nodes::ref('api:GET_LOGISTICS_OBJECT'), Nodes::ref('api:POST_LOGISTICS_EVENT')], 'api:isRequestedFor' => [Nodes::ref('https://a.example/logistics-objects/a'), Nodes::ref('https://b.example/logistics-objects/b')], 'api:hasLogisticsObject' => Nodes::ref(self::PIECE), 'api:expiresAt' => Nodes::dateTimeValue(new DateTimeImmutable('2027-01-01T00:00:00Z'))];
        $old = AccessDelegation::fromJsonLd($two, ApiVersion::V2_2_0);
        self::assertCount(2, $old->delegates, '2.2 allows several delegates');
        self::assertNotNull($old->expiresAt);
        self::assertArrayNotHasKey('api:expiresAt', $old->toJsonLd(ApiVersion::V2_2_0), 'expiry is a 2.3 property');
        self::assertArrayHasKey('api:expiresAt', $old->toJsonLd(ApiVersion::V2_3_0));
        try {
            AccessDelegation::fromJsonLd($two, ApiVersion::V2_3_0);
            self::fail();
        } catch (InvalidDocument $e) {
            self::assertStringContainsString('exactly one organisation', $e->getMessage());
        }
    }

    public function testVerificationRoundTripsWithSeverity(): void
    {
        $verification = Verification::fromJsonLd(self::fixture('Verification'));

        self::assertSame(self::PIECE, $verification->logisticsObject->value);
        self::assertSame(1, $verification->revision);
        self::assertCount(2, $verification->errors);
        self::assertSame('The gross weight is missing', $verification->errors[0]->title);
        self::assertSame(Severity::Error, $verification->errors[0]->severity);
        self::assertSame('AWB07', $verification->errors[0]->details[0]->code);
        self::assertSame(Cargo::grossWeight, $verification->errors[0]->details[0]->property);
        // The example declares xsd without the trailing "#" (spec question 13), so it cannot be compared as RDF;
        // a write/read round trip of our own document must be lossless instead.
        self::assertEquals($verification, Verification::fromJsonLd($verification->toJsonLd(ApiVersion::V2_3_0)));
        self::assertSame(Severity::Warning, Verification::fromJsonLd(self::fixture('VerificationRequest') === '' ? '' : json_encode(['@context' => Nodes::context(), '@type' => 'api:Verification', 'api:hasLogisticsObject' => Nodes::ref(self::PIECE), 'api:hasError' => ['@type' => 'api:Error', 'api:hasTitle' => 'w', 'api:hasSeverity' => Nodes::ref('api:WARNING')]], JSON_THROW_ON_ERROR))->errors[0]->severity);

        $atTwoTwo = $verification->toJsonLd(ApiVersion::V2_2_0);
        self::assertStringNotContainsString('hasSeverity', json_encode($atTwoTwo, JSON_THROW_ON_ERROR));
    }

    public function testNotificationsRoundTripWithAndWithoutBodies(): void
    {
        $plain = Notification::fromJsonLd(self::fixture('Notification_example1'));
        self::assertSame(NotificationEventType::LogisticsObjectCreated, $plain->eventType);
        self::assertSame(self::PIECE, $plain->logisticsObject?->value);
        self::assertSame(Cargo::Piece, $plain->logisticsObjectType);
        self::assertSame('https://1r.example.com/action-requests/599fea49-7287-42af-b441-1fa618d2aaed', $plain->triggeredBy?->value);
        self::assertNull($plain->body);
        self::assertEquivalentJsonLd(self::fixture('Notification_example1'), $plain->toJsonLd());

        $withBody = Notification::fromJsonLd(self::fixture('Notification_example2'));
        self::assertNotNull($withBody->body);
        self::assertSame('Lots of awesome ONE Record information materials', $withBody->body->literal(Cargo::goodsDescription));
        self::assertEquivalentJsonLd(self::fixture('Notification_example2'), $withBody->toJsonLd());

        $event = Notification::fromJsonLd(self::fixture('Notification_example4'));
        self::assertSame(NotificationEventType::LogisticsEventReceived, $event->eventType);
        self::assertCount(1, $event->logisticsEvents);
        self::assertEquivalentJsonLd(self::fixture('Notification_example4'), $event->toJsonLd());

        $status = Notification::fromJsonLd(self::fixture('Notification_example5'));
        self::assertSame(NotificationEventType::ChangeRequestAccepted, $status->eventType);
        self::assertEquivalentJsonLd(self::fixture('Notification_example5'), $status->toJsonLd());

        $changed = new Notification(NotificationEventType::LogisticsObjectUpdated, new Iri(self::PIECE), Cargo::Piece, null, [Cargo::goodsDescription, Cargo::coload]);
        $again = Notification::fromJsonLd($changed->toJsonLd());
        self::assertSame([Cargo::coload, Cargo::goodsDescription], $again->changedProperties);
    }

    public function testServerInformationRoundTripsAndNegotiatesVersions(): void
    {
        $info = ServerInformation::fromJsonLd(self::fixture('ServerInformation'));
        self::assertSame(self::ORG, $info->dataHolder->value);
        self::assertSame('http://1r.example.com', $info->serverEndpoint->value);
        self::assertSame(['2.3.0'], $info->apiVersions);
        self::assertSame(Cargo::Company, $info->dataHolderType);
        self::assertSame(ApiVersion::V2_3_0, $info->bestCommonApiVersion(ApiVersion::cases()));
        self::assertNull($info->bestCommonApiVersion([ApiVersion::V2_2_0]));
        self::assertEquivalentJsonLd(self::fixture('ServerInformation'), $info->toJsonLd());

        $ours = ServerInformation::for(new Iri('https://1r.example.com'), new Iri(self::ORG), ApiVersion::allDescending(), [DataModelVersion::V3_3, DataModelVersion::V3_2], dataHolderType: Cargo::Company);
        $json = $ours->toJsonLd();
        self::assertSame(['2.3.0', '2.2.0'], $json['api:hasSupportedApiVersion']);
        self::assertSame(['https://onerecord.iata.org/ns/cargo', 'https://onerecord.iata.org/ns/api'], $json['api:hasSupportedOntology'], 'unversioned ontology IRIs (spec question 8)');
        self::assertSame(['https://onerecord.iata.org/ns/cargo/3.3', 'https://onerecord.iata.org/ns/cargo/3.2', 'https://onerecord.iata.org/ns/api/2.3.0', 'https://onerecord.iata.org/ns/api/2.2.0'], $json['api:hasSupportedOntologyVersion']);
        $theirs = ServerInformation::fromJsonLd($json);
        self::assertSame(ApiVersion::V2_2_0, $theirs->bestCommonApiVersion([ApiVersion::V2_2_0]));
    }

    public function testActionRequestLifecycleAndSerialisation(): void
    {
        $at = new DateTimeImmutable('2026-10-02T12:00:00Z');
        $fromSpec = ActionRequest::fromJsonLd(self::fixture('SubscriptionRequest_example'));
        self::assertSame(ActionRequestType::Subscription, $fromSpec->type);
        self::assertSame(RequestStatus::Pending, $fromSpec->status);
        self::assertSame('https://1r.example.com/subscriptions/5f1a4869-e324-45b1-9ab0-60271ba54185', $fromSpec->payload instanceof Subscription ? $fromSpec->payload->id?->value : null);
        self::assertEquivalentJsonLd(self::fixture('SubscriptionRequest_example'), $fromSpec->toJsonLd(ApiVersion::V2_3_0), 'the spec example round-trips (it already carries hasRequestStatusSince)');

        $subscription = Subscription::fromJsonLd(self::fixture('Subscription_example1'));
        $request = ActionRequest::create(new Iri('https://1r.example.com/action-requests/599fea49-7287-42af-b441-1fa618d2aaee'), $subscription, new Iri(self::ORG), $at);
        self::assertSame(ActionRequestType::Subscription, $request->type);
        self::assertSame(RequestStatus::Pending, $request->status);
        self::assertSame([self::PIECE], array_map(static fn(Iri $i): string => $i->value, $request->logisticsObjects()));
        $atTwoTwo = $request->toJsonLd(ApiVersion::V2_2_0);
        self::assertArrayNotHasKey('api:hasRequestStatusSince', $atTwoTwo);

        $accepted = $request->withStatus(RequestStatus::Accepted, $at->modify('+1 hour'), new Iri('https://1r.example.com/logistics-objects/_holder'));
        self::assertSame(RequestStatus::Accepted, $accepted->status);
        self::assertCount(1, $accepted->history);
        self::assertSame(RequestStatus::Pending, $accepted->history[0]->status);
        self::assertEquals($at, $accepted->history[0]->since);
        self::assertSame('https://1r.example.com/logistics-objects/_holder', $accepted->history[0]->changedBy?->value);
        self::assertEquals($at->modify('+1 hour'), $accepted->lastModified());

        $json = $accepted->toJsonLd(ApiVersion::V2_3_0);
        self::assertIsArray($json['api:hasRequestStatusHistory']);
        self::assertCount(1, $json['api:hasRequestStatusHistory']);
        self::assertArrayNotHasKey('api:hasRequestStatusHistory', $accepted->toJsonLd(ApiVersion::V2_2_0));
        $read = ActionRequest::fromJsonLd($json);
        self::assertSame(RequestStatus::Accepted, $read->status);
        self::assertCount(1, $read->history);
        self::assertEquals($accepted->statusSince, $read->statusSince);
        self::assertInstanceOf(Subscription::class, $read->payload);

        $revoked = $accepted->withStatus(RequestStatus::Revoked, $at->modify('+2 hours'), new Iri(self::ORG));
        self::assertSame(self::ORG, $revoked->revokedBy?->value);
        self::assertEquals($at->modify('+2 hours'), $revoked->revokedAt);
        self::assertTrue($revoked->status->isFinal());
        self::assertArrayHasKey('api:isRevokedBy', $revoked->toJsonLd(ApiVersion::V2_2_0));

        $this->expectException(LogicException::class);
        $revoked->withStatus(RequestStatus::Accepted, $at);
    }

    /**
     * @return iterable<string, array{ActionRequestType, RequestStatus, RequestStatus, bool}>
     */
    public static function transitions(): iterable
    {
        yield 'change pending→accepted' => [ActionRequestType::Change, RequestStatus::Pending, RequestStatus::Accepted, true];
        yield 'change pending→rejected' => [ActionRequestType::Change, RequestStatus::Pending, RequestStatus::Rejected, true];
        yield 'change pending→revoked' => [ActionRequestType::Change, RequestStatus::Pending, RequestStatus::Revoked, true];
        yield 'change pending→acknowledged' => [ActionRequestType::Change, RequestStatus::Pending, RequestStatus::Acknowledged, false];
        yield 'change accepted→failed' => [ActionRequestType::Change, RequestStatus::Accepted, RequestStatus::Failed, true];
        yield 'change accepted→revoked' => [ActionRequestType::Change, RequestStatus::Accepted, RequestStatus::Revoked, false];
        yield 'subscription accepted→revoked' => [ActionRequestType::Subscription, RequestStatus::Accepted, RequestStatus::Revoked, true];
        yield 'delegation accepted→revoked' => [ActionRequestType::AccessDelegation, RequestStatus::Accepted, RequestStatus::Revoked, true];
        yield 'rejected is final' => [ActionRequestType::Subscription, RequestStatus::Rejected, RequestStatus::Accepted, false];
        yield 'verification pending→acknowledged' => [ActionRequestType::Verification, RequestStatus::Pending, RequestStatus::Acknowledged, true];
        yield 'verification pending→failed' => [ActionRequestType::Verification, RequestStatus::Pending, RequestStatus::Failed, true];
        yield 'verification pending→accepted' => [ActionRequestType::Verification, RequestStatus::Pending, RequestStatus::Accepted, false];
        yield 'verification acknowledged→revoked' => [ActionRequestType::Verification, RequestStatus::Acknowledged, RequestStatus::Revoked, false];
    }

    #[DataProvider('transitions')]
    public function testStateMachines(ActionRequestType $type, RequestStatus $from, RequestStatus $to, bool $allowed): void
    {
        self::assertSame($allowed, $from->canTransitionTo($to, $type));
    }

    public function testEnumParsingIsLenient(): void
    {
        self::assertSame(RequestStatus::Accepted, RequestStatus::tryFromString('https://onerecord.iata.org/ns/api#REQUEST_ACCEPTED'));
        self::assertSame(RequestStatus::Accepted, RequestStatus::tryFromString('api:REQUEST_ACCEPTED'));
        self::assertSame(RequestStatus::Accepted, RequestStatus::tryFromString('REQUEST_ACCEPTED'));
        self::assertSame(RequestStatus::Accepted, RequestStatus::tryFromString('ACCEPTED'));
        self::assertNull(RequestStatus::tryFromString('MAYBE'));
        self::assertSame(TopicType::Type, TopicType::tryFromString('https://onerecord.iata.org/ns/api/LOGISTICS_OBJECT_TYPE'), 'the spec allows # written as /');
        self::assertSame(TopicType::Identifier, TopicType::tryFromString('api:LOGISTICS_OBJECT_IDENTIFIER'));
        self::assertSame(Permission::PostLogisticsEvent, Permission::tryFromString('POST_LOGISTICS_EVENT'));
        self::assertSame(NotificationEventType::ChangeRequestFailed, ActionRequestType::Change->notificationFor(RequestStatus::Failed));
        self::assertSame(NotificationEventType::VerificationRequestAcknowledged, ActionRequestType::Verification->notificationFor(RequestStatus::Acknowledged));
        self::assertSame(NotificationEventType::LogisticsEventReceived, SubscriptionEventType::LogisticsEventReceived->notification());
        self::assertSame(Api::hasChange, ActionRequestType::Change->payloadProperty());
    }

    public function testErrorDocumentsAndCollections(): void
    {
        $error = Error::of('Logistics Object not found', '404', 'No such object.', null, self::PIECE);
        $json = ErrorDocument::write($error, ApiVersion::V2_3_0);
        self::assertSame('api:Error', $json['@type']);
        self::assertSame(['@id' => 'api:ERROR'], $json['api:hasSeverity']);
        self::assertArrayNotHasKey('api:hasSeverity', ErrorDocument::write($error, ApiVersion::V2_2_0));
        $read = ErrorDocument::read($json);
        self::assertNotNull($read);
        self::assertSame('Logistics Object not found', $read->title);
        self::assertSame('404', $read->details[0]->code);
        self::assertSame(self::PIECE, $read->details[0]->resource);
        self::assertNull(ErrorDocument::read('{"@context": {"api": "https://onerecord.iata.org/ns/api#"}, "@type": "api:Change"}'));
        self::assertNull(ErrorDocument::read('not json'));
        $fromSpec = ErrorDocument::read(self::fixture('Error_404'));
        self::assertSame('Logistics Object not found', $fromSpec?->title);

        $items = [['@id' => 'https://x/e1', '@type' => 'cargo:LogisticsEvent'], ['@id' => 'https://x/e2', '@type' => 'cargo:LogisticsEvent']];
        self::assertSame(2, Collection::write(new Iri('https://x/events'), $items)['api:hasTotalItems']);
        self::assertSame($items, Collection::write(new Iri('https://x/events'), $items)['api:hasItem']);
        self::assertSame($items[0], Collection::write(new Iri('https://x/events'), [$items[0]])['api:hasItem'], 'one item is an object, not a list');
        self::assertArrayNotHasKey('api:hasItem', Collection::write(new Iri('https://x/events'), []));
    }

    /**
     * @return iterable<string, array{callable(): mixed, string}>
     */
    public static function invalid(): iterable
    {
        $ctx = Nodes::context();
        yield 'subscription without subscriber' => [static fn() => Subscription::fromJsonLd(['@context' => $ctx, '@type' => 'api:Subscription', 'api:hasTopicType' => Nodes::ref('api:LOGISTICS_OBJECT_TYPE'), 'api:hasTopic' => Cargo::Piece, 'api:includeSubscriptionEventType' => Nodes::ref('api:LOGISTICS_OBJECT_CREATED')]), 'hasSubscriber'];
        yield 'subscription bad topic type' => [static fn() => Subscription::fromJsonLd(['@context' => $ctx, '@type' => 'api:Subscription', 'api:hasSubscriber' => Nodes::ref(self::ORG), 'api:hasTopicType' => Nodes::ref('api:EVERYTHING'), 'api:hasTopic' => Cargo::Piece, 'api:includeSubscriptionEventType' => Nodes::ref('api:LOGISTICS_OBJECT_CREATED')]), 'hasTopicType'];
        yield 'subscription bad topic' => [static fn() => Subscription::fromJsonLd(['@context' => $ctx, '@type' => 'api:Subscription', 'api:hasSubscriber' => Nodes::ref(self::ORG), 'api:hasTopicType' => Nodes::ref('api:LOGISTICS_OBJECT_TYPE'), 'api:hasTopic' => 'Piece', 'api:includeSubscriptionEventType' => Nodes::ref('api:LOGISTICS_OBJECT_CREATED')]), 'hasTopic'];
        yield 'subscription no event types' => [static fn() => Subscription::fromJsonLd(['@context' => $ctx, '@type' => 'api:Subscription', 'api:hasSubscriber' => Nodes::ref(self::ORG), 'api:hasTopicType' => Nodes::ref('api:LOGISTICS_OBJECT_TYPE'), 'api:hasTopic' => Cargo::Piece]), 'event type'];
        yield 'subscription unknown event type' => [static fn() => Subscription::fromJsonLd(['@context' => $ctx, '@type' => 'api:Subscription', 'api:hasSubscriber' => Nodes::ref(self::ORG), 'api:hasTopicType' => Nodes::ref('api:LOGISTICS_OBJECT_TYPE'), 'api:hasTopic' => Cargo::Piece, 'api:includeSubscriptionEventType' => Nodes::ref('api:CHANGE_REQUEST_ACCEPTED')]), 'not a subscription event type'];
        yield 'wrong type' => [static fn() => Subscription::fromJsonLd(['@context' => $ctx, '@type' => 'api:Change']), 'not an api:Subscription'];
        yield 'not json-ld' => [static fn() => Subscription::fromJsonLd('{"@graph": []}'), 'Invalid body request'];
        yield 'delegation without permission' => [static fn() => AccessDelegation::fromJsonLd(['@context' => $ctx, '@type' => 'api:AccessDelegation', 'api:isRequestedFor' => Nodes::ref(self::ORG), 'api:hasLogisticsObject' => Nodes::ref(self::PIECE)]), 'permission'];
        yield 'delegation unknown permission' => [static fn() => AccessDelegation::fromJsonLd(['@context' => $ctx, '@type' => 'api:AccessDelegation', 'api:hasPermission' => Nodes::ref('api:DELETE_EVERYTHING'), 'api:isRequestedFor' => Nodes::ref(self::ORG), 'api:hasLogisticsObject' => Nodes::ref(self::PIECE)]), 'not a permission'];
        yield 'delegation without delegate' => [static fn() => AccessDelegation::fromJsonLd(['@context' => $ctx, '@type' => 'api:AccessDelegation', 'api:hasPermission' => Nodes::ref('api:GET_LOGISTICS_OBJECT'), 'api:hasLogisticsObject' => Nodes::ref(self::PIECE)]), 'isRequestedFor'];
        yield 'delegation without object' => [static fn() => AccessDelegation::fromJsonLd(['@context' => $ctx, '@type' => 'api:AccessDelegation', 'api:hasPermission' => Nodes::ref('api:GET_LOGISTICS_OBJECT'), 'api:isRequestedFor' => Nodes::ref(self::ORG)]), 'hasLogisticsObject'];
        yield 'verification without errors' => [static fn() => Verification::fromJsonLd(['@context' => $ctx, '@type' => 'api:Verification', 'api:hasLogisticsObject' => Nodes::ref(self::PIECE)]), 'at least one error'];
        yield 'verification without object' => [static fn() => Verification::fromJsonLd(['@context' => $ctx, '@type' => 'api:Verification', 'api:hasError' => ['@type' => 'api:Error', 'api:hasTitle' => 'x']]), 'hasLogisticsObject'];
        yield 'notification without event type' => [static fn() => Notification::fromJsonLd(['@context' => $ctx, '@type' => 'api:Notification', 'api:hasLogisticsObject' => Nodes::ref(self::PIECE)]), 'hasEventType'];
        yield 'notification wrong type' => [static fn() => Notification::fromJsonLd(['@context' => $ctx, '@type' => 'api:Subscription']), 'not an api:Notification'];
        yield 'server information without holder' => [static fn() => ServerInformation::fromJsonLd(['@context' => $ctx, '@id' => 'https://x', '@type' => 'api:ServerInformation']), 'hasDataHolder'];
        yield 'action request without payload' => [static fn() => ActionRequest::fromJsonLd(['@context' => $ctx, '@id' => 'https://x/ar/1', '@type' => 'api:ChangeRequest', 'api:isRequestedBy' => Nodes::ref(self::ORG)]), 'no payload'];
        yield 'action request without id' => [static fn() => ActionRequest::fromJsonLd(['@context' => $ctx, '@type' => 'api:ChangeRequest']), 'identified'];
    }

    /**
     * @param callable(): mixed $read
     */
    #[DataProvider('invalid')]
    public function testRejectsInvalidDocuments(callable $read, string $message): void
    {
        try {
            $read();
            self::fail('expected InvalidDocument');
        } catch (InvalidDocument $e) {
            self::assertStringContainsString($message, $e->getMessage() . ' ' . ($e->errors[0]->details[0]->property ?? ''));
            self::assertSame('400', $e->errors[0]->details[0]->code);
        }
    }

    public function testApiFeaturesTable(): void
    {
        self::assertFalse(ApiFeatures::available(ApiVersion::V2_2_0, ApiFeatures::REQUEST_STATUS_HISTORY));
        self::assertTrue(ApiFeatures::available(ApiVersion::V2_3_0, ApiFeatures::REQUEST_STATUS_HISTORY));
        self::assertTrue(ApiFeatures::available(ApiVersion::V2_3_0, ApiFeatures::BULK_LOGISTICS_EVENTS));
        self::assertNotEmpty(ApiFeatures::all());
        $this->expectException(InvalidArgumentException::class);
        ApiFeatures::available(ApiVersion::V2_3_0, 'no-such-feature');
    }

    public function testChangeRequestReadsItsEmbeddedChange(): void
    {
        $change = Change::fromJsonLd(self::fixture('Change_example1'));
        $request = ActionRequest::create(new Iri('https://1r.example.com/action-requests/6b948f9b-b812-46ed-be39-4501453da99b'), $change, new Iri(self::ORG), new DateTimeImmutable('2026-10-02T12:00:00Z'));
        $failed = $request->withStatus(RequestStatus::Accepted, new DateTimeImmutable('2026-10-02T12:01:00Z'))->withStatus(RequestStatus::Failed, new DateTimeImmutable('2026-10-02T12:02:00Z'), null, [Error::of('Conflict with Logistics Object revision number', '409', 'stale')]);

        $read = ActionRequest::fromJsonLd($failed->toJsonLd(ApiVersion::V2_3_0));
        self::assertInstanceOf(Change::class, $read->payload);
        self::assertCount(3, $read->payload->operations);
        self::assertSame(RequestStatus::Failed, $read->status);
        self::assertCount(2, $read->history);
        self::assertSame('409', $read->errors[0]->details[0]->code);
        self::assertSame([self::PIECE], array_map(static fn(Iri $i): string => $i->value, $read->logisticsObjects()));
    }
}
