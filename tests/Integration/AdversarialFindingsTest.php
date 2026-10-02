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
}
