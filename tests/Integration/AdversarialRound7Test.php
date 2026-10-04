<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\NotificationEventType;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Api\ServerInformation;
use LambdaTwelve\OneRecord\Change\ChangeBuilder;
use LambdaTwelve\OneRecord\Client\OneRecordClient;
use LambdaTwelve\OneRecord\Client\StaticTokenProvider;
use LambdaTwelve\OneRecord\Model\Builder\Embedded;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\ChangeFailed;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestCreated;
use LambdaTwelve\OneRecord\Server\Event\ActionRequestStatusChanged;
use LambdaTwelve\OneRecord\Server\InMemory\InMemoryServer;
use LambdaTwelve\OneRecord\Server\ServerConfig;
use LambdaTwelve\OneRecord\Server\Services;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Testing\FakeHttpClient;
use LambdaTwelve\OneRecord\Testing\FixedClock;
use LambdaTwelve\OneRecord\Testing\HeaderAuthenticator;
use LambdaTwelve\OneRecord\Testing\InProcessHttpClient;
use LambdaTwelve\OneRecord\Testing\RecordingDispatcher;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists\MeasurementUnitCode;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\AbstractLogger;
use Stringable;

/**
 * The seventh adversarial review (review/adversarial7.md): listeners that
 * decide, datatypes that survive the wire, events for one object, and a
 * configured data model that means what it says.
 */
#[CoversNothing]
final class AdversarialRound7Test extends ServerTestCase
{
    /**
     * A dispatcher that acts on events the way a host listener would.
     *
     * @param callable(object, Services): void $listener
     */
    private function servicesWithListener(callable $listener): Services
    {
        $s = $this->server;
        $factory = new Psr17Factory();
        $services = null;
        $dispatcher = new class ($listener, $services) implements EventDispatcherInterface {
            /** @param callable(object, Services): void $listener */
            public function __construct(private $listener, public ?Services &$services) {}

            public function dispatch(object $event): object
            {
                if ($this->services !== null) {
                    ($this->listener)($event, $this->services);
                }

                return $event;
            }
        };
        $services = new Services($s->services->config, $s->objects, $s->events, $s->actionRequests, $s->subscriptions, $s->delegations, $s->outbox, new HeaderAuthenticator(), $s->policy, $this->clock, $dispatcher, $factory, $factory);
        $dispatcher->services = $services;

        return $services;
    }

    public function testR7001ACreationListenerThatDecidesLeavesConsistentStateAndOrderedNotifications(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $services = $this->servicesWithListener(static function (object $event, Services $services): void {
            if ($event instanceof ActionRequestCreated && $event->request->payload instanceof AccessDelegation) {
                (new ActionRequests($services))->accept($event->request, new Iri(self::HOLDER));
            }
        });
        $this->server->outbox->drain();

        $returned = (new ActionRequests($services))->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri], notifyRequestStatusChange: true), new Iri(self::PARTNER));

        self::assertSame(RequestStatus::Accepted, $returned->status, 'the caller gets what is stored, not the Pending snapshot');
        self::assertSame(RequestStatus::Accepted, $this->server->actionRequests->get($returned->iri)?->status);
        self::assertSame(Decision::Allow, $this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece->iri));
        $types = array_map(static fn($n): string => $n->notification->eventType->name, $this->server->outbox->drain());
        self::assertSame([NotificationEventType::AccessDelegationRequestPending->name, NotificationEventType::LogisticsObjectAccessGranted->name, NotificationEventType::AccessDelegationRequestAccepted->name], $types, 'Pending is queued before the listener can advance the request');
    }

    public function testR7001TheHolderDoesNotDecideTwiceWhenAListenerDecidedFirst(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $to = $piece->withGraph(new Graph([...array_filter(iterator_to_array($piece->graph), static fn(Triple $t): bool => $t->predicate->value !== Cargo::goodsDescription), new Triple($piece->iri, new Iri(Cargo::goodsDescription), Literal::string('Magazines'))]));
        $change = (new ChangeBuilder())->diff($piece, $to, 1);
        self::assertNotNull($change);

        // A listener that accepts the holder's own change as it is created.
        $accepting = $this->servicesWithListener(static function (object $event, Services $services): void {
            if ($event instanceof ActionRequestCreated) {
                (new ActionRequests($services))->accept($event->request, new Iri(self::HOLDER));
            }
        });
        $decided = (new DataHolder($accepting))->change($change);
        self::assertSame(RequestStatus::Accepted, $decided->status, 'no IllegalTransition from a second accept');
        self::assertSame(2, $this->server->objects->latest($piece->iri)?->revision);

        // A listener that rejects it: the holder learns that the change was not applied.
        $piece2 = $this->storePiece('piece-2', null);
        $to2 = $piece2->withGraph(new Graph([...array_filter(iterator_to_array($piece2->graph), static fn(Triple $t): bool => $t->predicate->value !== Cargo::goodsDescription), new Triple($piece2->iri, new Iri(Cargo::goodsDescription), Literal::string('Magazines'))]));
        $change2 = (new ChangeBuilder())->diff($piece2, $to2, 1);
        self::assertNotNull($change2);
        $rejecting = $this->servicesWithListener(static function (object $event, Services $services): void {
            if ($event instanceof ActionRequestCreated) {
                (new ActionRequests($services))->reject($event->request, new Iri(self::HOLDER));
            }
        });
        try {
            (new DataHolder($rejecting))->change($change2);
            self::fail('rejected by the host itself');
        } catch (ChangeFailed $e) {
            self::assertSame(RequestStatus::Rejected, $e->request->status);
            self::assertStringContainsString('REQUEST_REJECTED', $e->getMessage());
        }
        self::assertSame(1, $this->server->objects->latest($piece2->iri)?->revision);
    }

    public function testR7001AStatusListenerThatRevokesIsReflectedInTheResult(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $services = $this->servicesWithListener(static function (object $event, Services $services): void {
            if ($event instanceof ActionRequestStatusChanged && $event->request->status === RequestStatus::Accepted && $event->request->payload instanceof AccessDelegation) {
                (new ActionRequests($services))->revoke($event->request, new Iri(self::HOLDER));
            }
        });
        $requests = new ActionRequests($services);
        $pending = $requests->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::PARTNER));

        $result = $requests->accept($pending, new Iri(self::HOLDER));

        self::assertSame(RequestStatus::Revoked, $result->status, 'accept() returns what the request became');
        self::assertSame(Decision::Forbid, $this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece->iri), 'the grant the listener revoked is gone');
    }

    public function testR7002AnIntegralDoubleKeepsItsDatatypeThroughTheClient(): void
    {
        $factory = new Psr17Factory();
        $piece = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Books')->set(Cargo::grossWeight, Values::quantity(20, MeasurementUnitCode::KGM))->build(new Iri(self::BASE . '/logistics-objects/p-double'));

        // The bytes on the wire name the datatype; a bare 20 would read back as xsd:integer anywhere.
        $information = ServerInformation::for(new Iri(self::BASE), new Iri(self::HOLDER), [ApiVersion::V2_3_0], [DataModelVersion::V3_3]);
        $fake = (new FakeHttpClient())
            ->queue(new Response(200, ['Content-Type' => 'application/ld+json; version=2.3.0'], json_encode($information->toJsonLd(), JSON_THROW_ON_ERROR)))
            ->queue(new Response(201, ['Location' => $piece->iri->value, 'Type' => Cargo::Piece]));
        (new OneRecordClient($fake, $factory, $factory, new StaticTokenProvider(self::HOLDER), self::BASE, null, new FixedClock()))->createLogisticsObject($piece);
        $sent = (string) $fake->lastRequest()->getBody();
        self::assertStringContainsString('"@type":"xsd:double"', $sent);
        self::assertStringContainsString('"@value":"2.0E1"', $sent);
        self::assertStringNotContainsString('"cargo:numericalValue":20', $sent);

        // And the real server stores a double.
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $client = new OneRecordClient(new InProcessHttpClient($this->server->handler, $factory, $factory), $factory, $factory, new StaticTokenProvider(self::HOLDER), self::BASE, clock: $this->clock);
        $client->createLogisticsObject($piece);
        $stored = $this->server->objects->latest($piece->iri);
        self::assertNotNull($stored);
        $weight = $stored->object->graph->firstObject($piece->iri, Cargo::grossWeight);
        self::assertInstanceOf(Iri::class, $weight);
        $value = $stored->object->graph->firstObject($weight, Cargo::numericalValue);
        self::assertInstanceOf(Literal::class, $value);
        self::assertSame(Literal::XSD_DOUBLE, $value->datatype);
    }

    public function testR7006AnEventNamesExactlyTheObjectItIsPostedOnWhateverTheOrder(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);
        $event = static fn(array|string $for): string => json_encode([
            '@context' => ['cargo' => Cargo::NAMESPACE],
            '@type' => 'cargo:LogisticsEvent',
            'cargo:eventCode' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP'],
            'cargo:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z'],
            'cargo:eventFor' => $for,
        ], JSON_THROW_ON_ERROR);
        $post = fn(string $body, string $version) => $this->request('POST', '/logistics-objects/piece-1/logistics-events', self::PARTNER, ['Accept' => 'application/ld+json; version=' . $version, 'Content-Type' => 'application/ld+json; version=' . $version], $body);
        $mine = ['@id' => $piece->iri->value];
        $other = ['@id' => 'https://other.example/object'];

        foreach (['2.2.0', '2.3.0'] as $version) {
            self::assertSame(400, $post($event([$mine, $other]), $version)->getStatusCode(), 'a second target, listed after mine');
            self::assertSame(400, $post($event([$other, $mine]), $version)->getStatusCode(), 'and before it');
            self::assertSame(400, $post($event('not an iri'), $version)->getStatusCode(), 'a literal target');
            self::assertSame(201, $post($event([$mine, $mine]), $version)->getStatusCode(), 'the same object twice is still one object');
        }
    }

    public function testD7001TheConfiguredDataModelIsTheValidationCeilingAndDeprecatedTermsAreLogged(): void
    {
        $factory = new Psr17Factory();
        $logger = new class extends AbstractLogger {
            /** @var list<string> */
            public array $notices = [];

            public function log($level, string|Stringable $message, array $context = []): void
            {
                $this->notices[] = (\is_string($level) ? $level : 'other') . ': ' . $message;
            }
        };
        $server = new InMemoryServer(new ServerConfig(self::BASE, new Iri(self::HOLDER), dataModelVersions: [DataModelVersion::V3_2]), new HeaderAuthenticator(), $this->clock, new RecordingDispatcher(), $factory, $factory, $logger);
        $server->policy->addInternal(new Iri(self::HOLDER));
        self::assertSame(DataModelVersion::V3_2, $server->services->vocabulary->ceiling());

        // securityDeclarations on a Shipment arrived in 3.3: a 3.2 server does not accept it (the review's
        // contactDetails example is a 3.2 term on Organization, so it is accepted at either ceiling).
        self::assertFalse($server->services->vocabulary->accepts([Cargo::Shipment], Cargo::securityDeclarations));
        self::assertTrue($this->server->services->vocabulary->accepts([Cargo::Shipment], Cargo::securityDeclarations), 'the default server, at 3.3, accepts it');
        $body = json_encode(['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => 'cargo:Shipment', 'cargo:goodsDescription' => 'Books', 'cargo:securityDeclarations' => ['@id' => self::BASE . '/logistics-objects/sd1']], JSON_THROW_ON_ERROR);
        $response = $server->handler->handle(new \Nyholm\Psr7\ServerRequest('POST', self::BASE . '/logistics-objects', ['Accept' => 'application/ld+json; version=2.2.0', 'Content-Type' => 'application/ld+json; version=2.2.0', 'X-Test-Agent' => self::HOLDER], $body));
        self::assertSame(400, $response->getStatusCode(), (string) $response->getBody());

        // A deprecated term is accepted and logged.
        $vocabulary = $server->services->vocabulary;
        $class = $vocabulary->accepts([Cargo::Piece], Cargo::totalDimensions) ? Cargo::Piece : Cargo::Shipment;
        self::assertTrue($vocabulary->accepts([$class], Cargo::totalDimensions));
        $object = ObjectBuilder::of($class)->set(Cargo::totalDimensions, Embedded::of(Cargo::Dimensions)->set(Cargo::height, Values::quantity(1, MeasurementUnitCode::MTR)))->build(new Iri(self::BASE . '/logistics-objects/old-style'));
        (new DataHolder($server->services))->create($object);
        self::assertCount(1, array_filter($logger->notices, static fn(string $n): bool => str_starts_with($n, 'notice: ') && str_contains($n, 'totalDimensions') && str_contains($n, 'deprecated in data model 3.3')), implode("\n", $logger->notices));
    }

    public function testR8001ThePublishersSubscriptionHonoursACreationListenersDecision(): void
    {
        $this->storePiece('piece-1', null);
        $subscription = new \LambdaTwelve\OneRecord\Api\Subscription(new Iri(self::PARTNER), \LambdaTwelve\OneRecord\Api\TopicType::Type, Cargo::Piece, [\LambdaTwelve\OneRecord\Api\SubscriptionEventType::LogisticsObjectCreated], notifyRequestStatusChange: true);

        $accepting = $this->servicesWithListener(static function (object $event, Services $services): void {
            if ($event instanceof ActionRequestCreated) {
                (new ActionRequests($services))->accept($event->request, new Iri(self::HOLDER));
            }
        });
        $this->server->outbox->drain();
        $request = (new DataHolder($accepting))->subscribe($subscription);
        self::assertSame(RequestStatus::Accepted, $request->status, 'accepted once, by the listener; no IllegalTransition');
        self::assertCount(1, $request->history, 'one transition, not two');
        $types = array_map(static fn($n): string => $n->notification->eventType->name, $this->server->outbox->drain());
        self::assertSame([NotificationEventType::SubscriptionRequestPending->name, NotificationEventType::SubscriptionRequestAccepted->name], $types);

        $rejecting = $this->servicesWithListener(static function (object $event, Services $services): void {
            if ($event instanceof ActionRequestCreated) {
                (new ActionRequests($services))->reject($event->request, new Iri(self::HOLDER));
            }
        });
        $request = (new DataHolder($rejecting))->subscribe($subscription);
        self::assertSame(RequestStatus::Rejected, $request->status, 'the host\'s own listener said no; the caller reads that');
    }

    public function testR8002AnObjectStoredWithADerivedIntegerCanStillBeEdited(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $body = json_encode(['@context' => ['cargo' => Cargo::NAMESPACE, 'xsd' => 'http://www.w3.org/2001/XMLSchema#'], '@type' => 'cargo:ULD', 'cargo:numberOfDoors' => ['@type' => 'xsd:int', '@value' => '2']], JSON_THROW_ON_ERROR);
        $created = $this->request('POST', '/logistics-objects', self::HOLDER, [], $body);
        self::assertSame(201, $created->getStatusCode(), (string) $created->getBody());
        $iri = new Iri($created->getHeaderLine('Location'));
        $stored = $this->server->objects->latest($iri);
        self::assertNotNull($stored);

        // An unrelated edit must not trip over the stored xsd:int (R8-002).
        $edited = $stored->object->withGraph(new Graph([...$stored->object->graph, new Triple($iri, new Iri(Cargo::goodsDescription), Literal::string('Books'))]));
        $decided = (new DataHolder($this->server->services))->update($edited, 'add a description');
        self::assertNotNull($decided);
        self::assertSame(RequestStatus::Accepted, $decided->status);
        self::assertSame(2, $this->server->objects->latest($iri)?->revision);
    }
}
