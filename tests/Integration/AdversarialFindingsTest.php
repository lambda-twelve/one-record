<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\AccessDelegation;
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
}
