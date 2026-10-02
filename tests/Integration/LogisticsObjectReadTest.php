<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Model\Builder\Values;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;

#[CoversNothing]
final class LogisticsObjectReadTest extends ServerTestCase
{
    public function testGetReturnsTheObjectWithRevisionHeadersAndProperties(): void
    {
        $piece = $this->storePiece();
        $response = $this->request('GET', '/logistics-objects/piece-1');

        self::assertSame(200, $response->getStatusCode(), (string) $response->getBody());
        self::assertSame('application/ld+json; version=2.3.0', $response->getHeaderLine('Content-Type'));
        self::assertSame('en-US', $response->getHeaderLine('Content-Language'));
        self::assertSame(Cargo::Piece, $response->getHeaderLine('Type'));
        self::assertSame('1', $response->getHeaderLine('Revision'));
        self::assertSame('1', $response->getHeaderLine('Latest-Revision'));
        self::assertSame('Fri, 02 Oct 2026 12:00:00 GMT', $response->getHeaderLine('Last-Modified'));
        self::assertSame($piece->iri->value, $response->getHeaderLine('Location'));

        $body = self::json($response);
        self::assertSame($piece->iri->value, $body['@id']);
        self::assertSame('cargo:Piece', $body['@type']);
        self::assertSame(1, $body['api:hasRevision']);
        self::assertSame(1, $body['api:hasLatestRevision']);
        self::assertSame('Books', $body['cargo:goodsDescription']);
        self::assertFalse($body['cargo:coload']);
        self::assertSame(['@type' => 'cargo:Value', 'cargo:numericalValue' => 20.0, 'cargo:unit' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM']], $body['cargo:grossWeight']);

        // The body is the stored object plus its revision properties, nothing else.
        $read = LogisticsObject::fromJsonLd($body);
        $graph = new \LambdaTwelve\OneRecord\Rdf\Graph();
        foreach ($read->graph as $triple) {
            if (!str_starts_with($triple->predicate->value, 'https://onerecord.iata.org/ns/api#')) {
                $graph->add($triple);
            }
        }
        self::assertTrue((new Comparer())->isomorphic($piece->graph, $graph));
    }

    public function testHeadHasTheSameHeadersAndNoBody(): void
    {
        $this->storePiece();
        $response = $this->request('HEAD', '/logistics-objects/piece-1');

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('', (string) $response->getBody());
        self::assertSame(Cargo::Piece, $response->getHeaderLine('Type'));
        self::assertSame('1', $response->getHeaderLine('Latest-Revision'));
        self::assertGreaterThan(0, (int) $response->getHeaderLine('Content-Length'));
        self::assertSame(404, $this->request('HEAD', '/logistics-objects/none')->getStatusCode());
        self::assertSame('', (string) $this->request('HEAD', '/logistics-objects/none')->getBody(), 'HEAD errors have no body either');
    }

    public function testAccessControl(): void
    {
        $this->storePiece();

        self::assertError($this->request('GET', '/logistics-objects/piece-1', agent: self::STRANGER), 403, 'Not authorized');
        self::assertError($this->request('GET', '/logistics-objects/piece-1', agent: null), 401);
        self::assertError($this->request('GET', '/logistics-objects/unknown'), 404, 'Resource not found');
        self::assertError($this->request('GET', '/logistics-objects/unknown', agent: self::STRANGER), 404, 'Resource not found');
        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1', agent: self::HOLDER)->getStatusCode(), 'internal agents read everything');

        $this->server->policy->allowEveryone(new Iri(self::BASE . '/logistics-objects/piece-1'), [Permission::GetLogisticsObject]);
        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1', agent: self::STRANGER)->getStatusCode(), 'public authorisation');
    }

    public function testHistoricalReads(): void
    {
        $piece = $this->storePiece();
        $this->clock->advance('+1 hour');
        $updated = ObjectBuilder::of(Cargo::Piece)->set(Cargo::goodsDescription, 'Magazines')->set(Cargo::coload, true)->build($piece->iri);
        $this->server->objects->saveRevision($updated, 1, $this->clock->now());
        $this->clock->advance('+1 hour');

        $latest = $this->request('GET', '/logistics-objects/piece-1');
        self::assertSame('2', $latest->getHeaderLine('Revision'));
        self::assertSame('Magazines', self::json($latest)['cargo:goodsDescription']);

        $historical = $this->request('GET', '/logistics-objects/piece-1?at=20261002T123000Z');
        self::assertSame(200, $historical->getStatusCode(), (string) $historical->getBody());
        self::assertSame('1', $historical->getHeaderLine('Revision'));
        self::assertSame('2', $historical->getHeaderLine('Latest-Revision'));
        self::assertSame('Fri, 02 Oct 2026 12:00:00 GMT', $historical->getHeaderLine('Last-Modified'));
        self::assertSame($piece->iri->value . '?at=20261002T123000Z', $historical->getHeaderLine('Location'));
        $body = self::json($historical);
        self::assertSame('Books', $body['cargo:goodsDescription']);
        self::assertSame($piece->iri->value . '?at=20261002T123000Z', $body['@id']);
        self::assertSame(1, $body['api:hasRevision']);
        self::assertSame(2, $body['api:hasLatestRevision']);

        self::assertSame(200, $this->request('GET', '/logistics-objects/piece-1?at=2026-10-02T12:30:00Z')->getStatusCode(), 'RFC 3339 is accepted too');
        self::assertError($this->request('GET', '/logistics-objects/piece-1?at=20261001T000000Z'), 404, 'Resource not found');
        self::assertError($this->request('GET', '/logistics-objects/piece-1?at=20991001T000000Z'), 400, 'Invalid query parameter');
        self::assertError($this->request('GET', '/logistics-objects/piece-1?at=yesterday'), 400, 'Invalid query parameter');
    }

    public function testEmbeddedReadsFollowLocalLinksTheCallerMayRead(): void
    {
        $piece = $this->storePiece('piece-1');
        $secret = $this->storePiece('piece-secret', readableBy: null);
        $shipment = ObjectBuilder::of(Cargo::Shipment)
            ->set(Cargo::goodsDescription, 'Lots of books')
            ->add(Cargo::pieces, Values::iri($piece->iri->value))
            ->add(Cargo::pieces, Values::iri($secret->iri->value))
            ->add(Cargo::pieces, Values::iri('https://elsewhere.example/logistics-objects/remote'))
            ->build(new Iri(self::BASE . '/logistics-objects/shipment-1'));
        $this->server->objects->create($shipment, $this->clock->now());
        $this->server->policy->allow(new Iri(self::PARTNER), $shipment->iri, [Permission::GetLogisticsObject]);

        $plain = self::json($this->request('GET', '/logistics-objects/shipment-1'));
        $pieces = self::arr($plain['cargo:pieces']);
        self::assertCount(3, $pieces);
        foreach ($pieces as $ref) {
            self::assertSame(['@id'], array_keys(self::arr($ref)), 'linked, not embedded, by default');
        }

        $embedded = self::json($this->request('GET', '/logistics-objects/shipment-1?embedded=true'));
        $byId = [];
        foreach (self::arr($embedded['cargo:pieces']) as $node) {
            $node = self::arr($node);
            self::assertIsString($node['@id']);
            $byId[$node['@id']] = $node;
        }
        self::assertSame('Books', $byId[$piece->iri->value]['cargo:goodsDescription'], 'readable local object is embedded');
        self::assertSame(1, $byId[$piece->iri->value]['api:hasRevision'], 'embedded objects carry their revision too');
        self::assertSame(['@id'], array_keys($byId[$secret->iri->value]), 'an object the caller may not read stays a link');
        self::assertSame(['@id'], array_keys($byId['https://elsewhere.example/logistics-objects/remote']), 'remote objects stay links');

        $historical = self::json($this->request('GET', '/logistics-objects/shipment-1?at=20261002T120000Z'));
        $ids = [];
        foreach (self::arr($historical['cargo:pieces']) as $ref) {
            $id = self::arr($ref)['@id'];
            self::assertIsString($id);
            $ids[] = $id;
        }
        sort($ids);
        self::assertSame([
            $piece->iri->value . '?at=20261002T120000Z',
            $secret->iri->value . '?at=20261002T120000Z',
            'https://elsewhere.example/logistics-objects/remote',
        ], $ids, 'local links carry the same ?at=, remote links are untouched');
    }
}
