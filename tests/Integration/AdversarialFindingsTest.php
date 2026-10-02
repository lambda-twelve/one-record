<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use DateTimeImmutable;
use LambdaTwelve\OneRecord\Api\AccessDelegation;
use LambdaTwelve\OneRecord\Api\ActionRequest;
use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\ActionRequests;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Server\Event\NotificationReceived;
use LambdaTwelve\OneRecord\Server\IllegalTransition;
use LambdaTwelve\OneRecord\Server\Spi\Action;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Decision;

/**
 * Regressions for the findings of the first adversarial review
 * (review round 2026-10-03), one test per finding, named by its id.
 */
final class AdversarialFindingsTest extends ServerTestCase
{
    public function testAr002StaleSnapshotsCannotUndoOrRepeatDecisions(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $holder = new DataHolder($this->server->services);
        $requests = new ActionRequests($this->server->services);
        $pending = $requests->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::PARTNER));
        $partnerAgent = new Agent(new Iri(self::STRANGER));

        $holder->accept($pending->iri);
        self::assertSame(Decision::Allow, $this->server->policy->decide($partnerAgent, Action::ReadLogisticsObject, $piece->iri));

        // Revoking with the stale pending snapshot still acts on the stored (accepted) state: grants go.
        $revoked = $requests->revoke($pending, new Iri(self::HOLDER));
        self::assertSame(RequestStatus::Revoked, $revoked->status);
        self::assertCount(2, $revoked->history, 'pending, then accepted, then revoked');
        self::assertSame(RequestStatus::Accepted, $revoked->history[1]->status, 'history reflects what really happened');
        self::assertSame([], $this->server->delegations->grantsFor(new Iri(self::STRANGER), $piece->iri));
        self::assertSame(Decision::Forbid, $this->server->policy->decide($partnerAgent, Action::ReadLogisticsObject, $piece->iri));

        // Accepting with the stale pending snapshot after the revocation is refused, not applied.
        try {
            $requests->accept($pending, new Iri(self::HOLDER));
            self::fail('a terminal request cannot be accepted from a stale snapshot');
        } catch (IllegalTransition) {
        }
        self::assertSame(RequestStatus::Revoked, $this->server->actionRequests->get($pending->iri)?->status);
        self::assertSame([], $this->server->delegations->grantsFor(new Iri(self::STRANGER), $piece->iri));
    }

    public function testAr004SharedDescendantsAreVisitedOnce(): void
    {
        // A diamond-shaped flattened body: every layer's two nodes reference both nodes of the next layer.
        $layers = 40;
        $nodes = [];
        for ($i = 0; $i < $layers; $i++) {
            foreach (['a', 'b'] as $side) {
                $node = ['@id' => 'internal:' . $side . $i, '@type' => 'cargo:Value'];
                if ($i + 1 < $layers) {
                    $node['cargo:unit'] = [['@id' => 'internal:a' . ($i + 1)], ['@id' => 'internal:b' . ($i + 1)]];
                } else {
                    $node['cargo:numericalValue'] = 1;
                }
                $nodes[] = $node;
            }
        }
        $object = ['@id' => self::BASE . '/logistics-objects/p', '@type' => 'cargo:Piece', 'cargo:grossWeight' => [['@id' => 'internal:a0'], ['@id' => 'internal:b0']]];
        $body = json_encode([
            '@context' => ['cargo' => 'https://onerecord.iata.org/ns/cargo#', 'api' => 'https://onerecord.iata.org/ns/api#'],
            '@graph' => [
                ['@id' => self::BASE . '/notifications/n1', '@type' => 'api:Notification', 'api:hasEventType' => ['@id' => 'api:LOGISTICS_OBJECT_UPDATED'], 'api:hasLogisticsObject' => ['@id' => self::BASE . '/logistics-objects/p']],
                $object,
                ...$nodes,
            ],
        ], JSON_THROW_ON_ERROR);

        $started = microtime(true);
        $response = $this->request('POST', '/notifications', self::PARTNER, body: $body);

        self::assertSame(204, $response->getStatusCode(), (string) $response->getBody());
        self::assertLessThan(2.0, microtime(true) - $started, 'linear in the graph size, not exponential in its depth');
        $received = $this->dispatcher->of(NotificationReceived::class);
        self::assertCount(1, $received);
        $graph = $received[0]->notification->body?->graph;
        self::assertNotNull($graph);
        // The object: a type and two links. Every node: a type and either two links or one value.
        self::assertCount(3 + ($layers - 1) * 2 * 3 + 2 * 2, $graph, 'every node once');
    }

    public function testAr016AnInvalidExpiryIsRefusedNotTreatedAsUnlimited(): void
    {
        $piece = $this->storePiece();
        $delegation = (new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::STRANGER)], [$piece->iri]))->toJsonLd(\LambdaTwelve\OneRecord\Spec\ApiVersion::V2_3_0);
        foreach (['definitely not a date', 'tomorrow', '2026-02-30T12:00:00Z', '2026-10-02', '20261002T120000Z'] as $bad) {
            $delegation['api:expiresAt'] = ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => $bad];
            self::assertError($this->request('POST', '/access-delegations', body: json_encode($delegation, JSON_THROW_ON_ERROR)), 400, 'Invalid body request');
        }
        self::assertSame([], $this->server->actionRequests->all(), 'nothing pending was created');

        $delegation['api:expiresAt'] = ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-12-31T23:59:59Z'];
        self::assertSame(201, $this->request('POST', '/access-delegations', body: json_encode($delegation, JSON_THROW_ON_ERROR))->getStatusCode());
        unset($delegation['api:expiresAt']);
        self::assertSame(201, $this->request('POST', '/access-delegations', body: json_encode($delegation, JSON_THROW_ON_ERROR))->getStatusCode(), 'absent is still unlimited');
    }

    public function testAr003RevokedReadAccessStopsBodyDisclosureToSubscribers(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $holder = new DataHolder($this->server->services);
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::GetLogisticsObject]);
        $holder->subscribe(new \LambdaTwelve\OneRecord\Api\Subscription(new Iri(self::PARTNER), \LambdaTwelve\OneRecord\Api\TopicType::Identifier, $piece->iri->value, [\LambdaTwelve\OneRecord\Api\SubscriptionEventType::LogisticsObjectUpdated], sendLogisticsObjectBody: true));

        $holder->update(\LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::Piece)->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::goodsDescription, 'Public')->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::coload, false)->build($piece->iri));
        $withAccess = $this->server->outbox->drain();
        self::assertCount(1, $withAccess);
        self::assertNotNull($withAccess[0]->notification->body, 'with read access the body travels');

        $this->server->delegations->eraseFor($piece->iri);
        $holder->update(\LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::Piece)->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::goodsDescription, 'NEW SECRET')->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::coload, false)->build($piece->iri));
        $withoutAccess = $this->server->outbox->drain();
        self::assertCount(1, $withoutAccess, 'the subscriber still learns that the object changed');
        self::assertNull($withoutAccess[0]->notification->body, 'but not what it contains');
        self::assertStringNotContainsString('SECRET', json_encode($withoutAccess[0]->notification->toJsonLd(), JSON_THROW_ON_ERROR));

        // A hiding policy tells nothing at all.
        $this->server = $this->makeServer(Decision::Hide);
        $piece = $this->storePiece('piece-1', null);
        $holder = new DataHolder($this->server->services);
        $holder->subscribe(new \LambdaTwelve\OneRecord\Api\Subscription(new Iri(self::PARTNER), \LambdaTwelve\OneRecord\Api\TopicType::Identifier, $piece->iri->value, [\LambdaTwelve\OneRecord\Api\SubscriptionEventType::LogisticsObjectUpdated]));
        $holder->update(\LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::Piece)->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::goodsDescription, 'Hidden')->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::coload, false)->build($piece->iri));
        self::assertSame([], $this->server->outbox->drain());
    }

    public function testAr006APublisherCanAskAboutItsOwnObject(): void
    {
        $publisherObject = 'https://publisher.example/logistics-objects/p';
        $this->server->subscriptions->offer(new \LambdaTwelve\OneRecord\Api\Subscription(new Iri(self::HOLDER), \LambdaTwelve\OneRecord\Api\TopicType::Identifier, $publisherObject, [\LambdaTwelve\OneRecord\Api\SubscriptionEventType::LogisticsObjectUpdated]));

        $response = $this->request('GET', '/subscriptions?topicType=LOGISTICS_OBJECT_IDENTIFIER&topic=' . rawurlencode($publisherObject), self::PARTNER);

        self::assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame(\LambdaTwelve\OneRecord\Vocabulary\Generated\Api::Subscription, $response->getHeaderLine('Type'));
        self::assertSame($publisherObject, self::arr(self::json($response)['api:hasTopic'])['@value']);
    }

    public function testAr014BulkEventsUseTheSameParsingAndValidationAsSingleOnes(): void
    {
        $this->server = $this->makeServer(bulkEvents: true);
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent, Permission::GetLogisticsEvent]);
        $context = ['c' => 'https://onerecord.iata.org/ns/cargo#'];
        $valid = ['@context' => $context, '@type' => 'c:LogisticsEvent', 'c:eventDate' => ['@type' => 'http://www.w3.org/2001/XMLSchema#dateTime', '@value' => '2026-10-02T11:00:00Z'], 'c:eventFor' => [['@id' => $piece->iri->value]]];

        // An alias for the cargo namespace names the target like any other prefix would.
        $response = $this->request('POST', '/logistics-events', body: json_encode($valid, JSON_THROW_ON_ERROR));
        self::assertSame(207, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame(1, self::json($response)['api:hasTotalCreated']);

        // Content the single route refuses is refused per target here too.
        $invalid = $valid + ['https://example/unknown' => 'x'];
        $single = $this->request('POST', '/logistics-objects/piece-1/logistics-events', body: json_encode(array_diff_key($invalid, ['c:eventFor' => 1]), JSON_THROW_ON_ERROR));
        self::assertSame(400, $single->getStatusCode());
        $bulk = $this->request('POST', '/logistics-events', body: json_encode($invalid, JSON_THROW_ON_ERROR));
        self::assertSame(207, $bulk->getStatusCode(), (string) $bulk->getBody());
        $body = self::json($bulk);
        self::assertSame(0, $body['api:hasTotalCreated']);
        self::assertSame(400, self::arr(self::arr($body['api:hasCreationResult'])[0])['api:hasHTTPStatus']);
        self::assertSame(1, self::json($this->request('GET', '/logistics-objects/piece-1/logistics-events'))['api:hasTotalItems'], 'nothing invalid was stored');
    }

    public function testAr018TypedLinksInHistoricalReadsCarryTheTimestamp(): void
    {
        $shipment = new Iri(self::BASE . '/logistics-objects/shipment-1');
        $this->server->objects->create(\LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::Shipment)->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::goodsDescription, 'Books')->build($shipment), $this->clock->now());
        $piece = \LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder::of(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::Piece)->set(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::coload, false)->build(new Iri(self::BASE . '/logistics-objects/piece-1'));
        // A typed reference: {"@id": shipment, "@type": "cargo:Shipment"}.
        $piece->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($piece->iri, new Iri(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::ofShipment), $shipment));
        $piece->graph->add(new \LambdaTwelve\OneRecord\Rdf\Triple($shipment, new Iri(\LambdaTwelve\OneRecord\Rdf\Graph::RDF_TYPE), new Iri(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::Shipment)));
        $this->server->objects->create($piece, $this->clock->now());
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::GetLogisticsObject]);

        $body = self::json($this->request('GET', '/logistics-objects/piece-1?at=20261002T120000Z'));

        self::assertSame(self::BASE . '/logistics-objects/piece-1?at=20261002T120000Z', $body['@id']);
        $link = self::arr($body['cargo:ofShipment']);
        self::assertSame($shipment->value . '?at=20261002T120000Z', $link['@id'], 'a typed link is still a link into the same instant');
        self::assertSame('cargo:Shipment', $link['@type']);
    }

    public function testAr020ExplicitExclusionsAndUnservedBodyVersionsAreHonoured(): void
    {
        self::assertError($this->request('GET', '/', self::HOLDER, ['Accept' => 'application/ld+json;q=0, */*;q=1']), 406);
        self::assertSame(200, $this->request('GET', '/', self::HOLDER, ['Accept' => '*/*;q=0.1, application/ld+json;q=0.9; version=2.3.0'])->getStatusCode());

        $this->server = $this->makeServerSpeaking([\LambdaTwelve\OneRecord\Spec\ApiVersion::V2_2_0]);
        $body = $this->piece('p')->toJson();
        self::assertError($this->request('POST', '/logistics-objects', self::HOLDER, ['Accept' => 'application/ld+json; version=2.2.0', 'Content-Type' => 'application/ld+json; version=2.3.0'], $body), 415);
        self::assertSame(201, $this->request('POST', '/logistics-objects', self::HOLDER, ['Accept' => 'application/ld+json; version=2.2.0', 'Content-Type' => 'application/ld+json; version=2.2.0'], $body)->getStatusCode());
    }

    public function testAr023InvalidInputIsA400NotA500(): void
    {
        $this->storePiece();
        self::assertError($this->request('GET', '/logistics-objects/piece-1?at=2026-99-99T99:99:99Z'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/logistics-objects/piece-1?at=20261399T000000Z'), 400, 'Invalid query parameter');
        self::assertError($this->request('POST', '/logistics-objects', self::HOLDER, body: '{"@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"}, "@type": "cargo:Piece", "cargo:coload": 1e400}'), 400, 'Invalid body request');
    }

    public function testAr024AnIdempotencyKeyReachesTheNotificationListener(): void
    {
        $body = (string) file_get_contents(__DIR__ . '/../Fixtures/spec/2026-07/Notification_example1.json');
        $this->request('POST', '/notifications', self::PARTNER, ['Idempotency-Key' => 'delivery-42'], $body);
        $this->request('POST', '/notifications', self::PARTNER, [], $body);
        $received = $this->dispatcher->of(NotificationReceived::class);
        self::assertCount(2, $received);
        self::assertSame('delivery-42', $received[0]->idempotencyKey);
        self::assertNull($received[1]->idempotencyKey);
    }

    public function testAr028ADelegateCannotRevokeAnotherDelegatesAccess(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $holder = new DataHolder($this->server->services);
        $request = (new ActionRequests($this->server->services))->create(new AccessDelegation([Permission::GetLogisticsObject], [new Iri(self::PARTNER), new Iri(self::STRANGER)], [$piece->iri]), new Iri(self::HOLDER));
        $holder->accept($request->iri);
        $path = substr($request->iri->value, \strlen(self::BASE));

        self::assertSame(200, $this->request('GET', $path, self::PARTNER)->getStatusCode(), 'a delegate may read the request');
        self::assertError($this->request('DELETE', $path, self::PARTNER), 403, 'Not authorized');
        self::assertSame(Decision::Allow, $this->server->policy->decide(new Agent(new Iri(self::STRANGER)), Action::ReadLogisticsObject, $piece->iri), 'the other delegate keeps its access');

        self::assertSame(204, $this->request('DELETE', $path, self::HOLDER)->getStatusCode(), 'the requestor revokes');
    }

    public function testR2003HistoricalReadsWithEmbeddingAreAccepted(): void
    {
        $piece = $this->storePiece();
        $factory = new \Nyholm\Psr7\Factory\Psr17Factory();
        $client = new \LambdaTwelve\OneRecord\Client\OneRecordClient(new \LambdaTwelve\OneRecord\Testing\InProcessHttpClient($this->server->handler, $factory, $factory), $factory, $factory, new \LambdaTwelve\OneRecord\Client\StaticTokenProvider(self::PARTNER), self::BASE, clock: $this->clock);

        foreach ([false, true] as $embedded) {
            $read = $client->getLogisticsObject($piece->iri, new DateTimeImmutable('2026-10-02T12:00:00Z'), $embedded);
            self::assertSame($piece->iri->value . '?at=20261002T120000Z', $read->object?->iri->value);
            self::assertSame(1, $read->revision);
        }
    }

    public function testR2004ASubscriberMayEndASubscriptionAThirdPartyCreated(): void
    {
        $piece = $this->storePiece('piece-1', null);
        $holder = new DataHolder($this->server->services);
        // Created by the holder on behalf of the partner: requestor and subscriber differ.
        $request = (new ActionRequests($this->server->services))->create(new \LambdaTwelve\OneRecord\Api\Subscription(new Iri(self::PARTNER), \LambdaTwelve\OneRecord\Api\TopicType::Identifier, $piece->iri->value, [\LambdaTwelve\OneRecord\Api\SubscriptionEventType::LogisticsObjectUpdated]), new Iri(self::HOLDER));
        $holder->accept($request->iri);
        $path = substr($request->iri->value, \strlen(self::BASE));

        self::assertError($this->request('DELETE', $path, self::STRANGER), 403);
        self::assertSame(204, $this->request('DELETE', $path, self::PARTNER)->getStatusCode(), 'the subscriber unsubscribes (spec question 30)');
        self::assertSame(RequestStatus::Revoked, $this->server->actionRequests->get($request->iri)?->status);
    }

    public function testR2006ChangesMayNotWriteServerMetadata(): void
    {
        $piece = $this->storePiece();
        $applier = new \LambdaTwelve\OneRecord\Change\ChangeApplier();
        foreach ([
            \LambdaTwelve\OneRecord\Change\Operation::add($piece->iri, new Iri(\LambdaTwelve\OneRecord\Vocabulary\Generated\Api::hasRevision), new \LambdaTwelve\OneRecord\Change\OperationObject(\LambdaTwelve\OneRecord\Rdf\Literal::XSD_INTEGER, '999')),
            \LambdaTwelve\OneRecord\Change\Operation::add($piece->iri, new Iri(\LambdaTwelve\OneRecord\Spec\Namespaces::API . 'notAnOntologyProperty'), new \LambdaTwelve\OneRecord\Change\OperationObject(\LambdaTwelve\OneRecord\Rdf\Literal::XSD_STRING, 'arbitrary')),
        ] as $operation) {
            try {
                $applier->apply($piece, 1, new \LambdaTwelve\OneRecord\Change\Change($piece->iri, 1, [$operation]));
                self::fail('API-namespace predicates are the server\'s');
            } catch (\LambdaTwelve\OneRecord\Change\ChangeRejected $e) {
                self::assertStringContainsString('set by the server', $e->errors[0]->details[0]->message ?? '');
            }
        }
        // An object that already carries revision metadata still accepts an ordinary change.
        $withMetadata = $piece->withGraph(new \LambdaTwelve\OneRecord\Rdf\Graph([...$piece->graph, new \LambdaTwelve\OneRecord\Rdf\Triple($piece->iri, new Iri(\LambdaTwelve\OneRecord\Vocabulary\Generated\Api::hasRevision), \LambdaTwelve\OneRecord\Rdf\Literal::integer(1))]));
        $result = $applier->apply($withMetadata, 1, new \LambdaTwelve\OneRecord\Change\Change($piece->iri, 1, [\LambdaTwelve\OneRecord\Change\Operation::add($piece->iri, new Iri(\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::goodsDescription), new \LambdaTwelve\OneRecord\Change\OperationObject(\LambdaTwelve\OneRecord\Rdf\Literal::XSD_STRING, 'More books'))]));
        self::assertSame([\LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo::goodsDescription], $result->changedProperties);
    }

    public function testR2012ALosingWorkersConflictIsA409(): void
    {
        $inner = $this->server->actionRequests;
        $conflicting = new class ($inner) implements \LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore {
            public function __construct(private readonly \LambdaTwelve\OneRecord\Server\Spi\ActionRequestStore $inner) {}

            public function save(ActionRequest $request): void
            {
                $this->inner->save($request);
            }

            public function transition(ActionRequest $request, RequestStatus $expectedCurrent): void
            {
                throw \LambdaTwelve\OneRecord\Server\Spi\StoreException::statusConflict($request->iri, $expectedCurrent->shortName(), 'REQUEST_ACCEPTED');
            }

            public function get(Iri $iri): ?ActionRequest
            {
                return $this->inner->get($iri);
            }

            public function auditTrail(Iri $logisticsObject, \LambdaTwelve\OneRecord\Server\Spi\AuditTrailQuery $query): array
            {
                return $this->inner->auditTrail($logisticsObject, $query);
            }

            public function pendingChanges(Iri $logisticsObject): array
            {
                return $this->inner->pendingChanges($logisticsObject);
            }

            public function accepted(\LambdaTwelve\OneRecord\Api\ActionRequestType $type): array
            {
                return $this->inner->accepted($type);
            }
        };
        $unit = new \LambdaTwelve\OneRecord\Testing\RecordingUnitOfWork();
        $services = new \LambdaTwelve\OneRecord\Server\Services($this->server->services->config, $this->server->objects, $this->server->events, $conflicting, $this->server->subscriptions, $this->server->delegations, $this->server->outbox, new \LambdaTwelve\OneRecord\Testing\HeaderAuthenticator(), $this->server->policy, $this->clock, $this->dispatcher, new \Nyholm\Psr7\Factory\Psr17Factory(), new \Nyholm\Psr7\Factory\Psr17Factory(), unitOfWork: $unit);
        $handler = \LambdaTwelve\OneRecord\Server\ServerBuilder::build($services);
        $piece = $this->storePiece('piece-1', null);
        $pending = (new ActionRequests($this->server->services))->create(new \LambdaTwelve\OneRecord\Api\Subscription(new Iri(self::PARTNER), \LambdaTwelve\OneRecord\Api\TopicType::Identifier, $piece->iri->value, [\LambdaTwelve\OneRecord\Api\SubscriptionEventType::LogisticsObjectUpdated]), new Iri(self::PARTNER));

        $response = $handler->handle(new \Nyholm\Psr7\ServerRequest('PATCH', $pending->iri->value . '?status=REQUEST_REJECTED', ['Accept' => 'application/ld+json; version=2.3.0', 'X-Test-Agent' => self::HOLDER]));

        self::assertSame(409, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame('api:Error', self::json($response)['@type']);
        self::assertSame(1, $unit->rolledBack, 'the unit of work unwound before the answer');
    }
}
