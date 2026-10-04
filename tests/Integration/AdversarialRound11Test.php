<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\Operation;
use LambdaTwelve\OneRecord\Change\OperationObject;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Review 11: an embedded node is embedded whatever id the client gave it.
 */
#[CoversNothing]
final class AdversarialRound11Test extends ServerTestCase
{
    private const string XSD = 'http://www.w3.org/2001/XMLSchema#';

    /**
     * @param array<string, mixed> $dimensions
     */
    private function pieceWith(array $dimensions): string
    {
        return json_encode(['@context' => ['cargo' => Cargo::NAMESPACE, 'xsd' => self::XSD], '@type' => 'cargo:Piece', 'cargo:goodsDescription' => 'Books', 'cargo:dimensions' => $dimensions], JSON_THROW_ON_ERROR);
    }

    public function testAnEmbeddedNodeWithAClientChosenIdIsValidatedAndReissuedAnInternalId(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $height = ['@type' => 'cargo:Value', 'cargo:numericalValue' => ['@type' => 'xsd:double', '@value' => '1.5'], 'cargo:unit' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#MTR']];

        $fake = $this->request('POST', '/logistics-objects', self::HOLDER, [], $this->pieceWith(['@id' => 'https://attacker.example/custom-dim-1', '@type' => 'https://example.com/FakeDimensionsClass', 'https://example.com/fakeProp' => 'x', 'cargo:height' => 'not-a-number']));
        self::assertError($fake, 400, 'Invalid resource');

        $created = $this->request('POST', '/logistics-objects', self::HOLDER, [], $this->pieceWith(['@id' => 'https://company.example/dims/101', '@type' => 'cargo:Dimensions', 'cargo:height' => $height]));
        self::assertSame(201, $created->getStatusCode(), (string) $created->getBody());
        $iri = new Iri($created->getHeaderLine('Location'));
        $read = self::json($this->request('GET', substr($iri->value, \strlen(self::BASE)), self::HOLDER));
        $dims = self::arr($read['cargo:dimensions']);
        self::assertStringStartsWith('internal:', self::str($dims['@id']), 'the server minted the id; the client\'s is gone');

        // The node is addressable by the id the server gave it, and goes away with its last link.
        $node = new Iri(self::str($dims['@id']));
        $holder = new DataHolder($this->server->services);
        $edited = $holder->change(new Change($iri, 1, [Operation::add($node, new Iri(Cargo::length), new OperationObject(Cargo::Value, '_:l')), Operation::add(new \LambdaTwelve\OneRecord\Rdf\BlankNode('l'), new Iri(Cargo::numericalValue), OperationObject::literal(Literal::double(2.0)))]));
        self::assertSame(RequestStatus::Accepted, $edited->status);
        $unlinked = $holder->change(new Change($iri, 2, [Operation::delete($iri, new Iri(Cargo::dimensions), new OperationObject(Cargo::Dimensions, $node->value))]));
        self::assertSame(RequestStatus::Accepted, $unlinked->status);
        $after = $this->server->objects->latest($iri);
        self::assertNotNull($after);
        self::assertSame([], $after->object->graph->about($node), 'no orphan left behind');
        self::assertCount(2, $after->object->graph, 'type and description remain');
    }

    public function testAnObjectStoredEarlierWithAClientIdCanStillBeEditedAndCleansed(): void
    {
        // Stored straight into the store, as an object created before this round would have been.
        $p = new Iri(self::BASE . '/logistics-objects/legacy');
        $theirs = new Iri('https://company.example/dims/101');
        $legacy = new LogisticsObject($p, new Graph([
            new Triple($p, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($p, new Iri(Cargo::dimensions), $theirs),
            new Triple($theirs, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($theirs, new Iri(Cargo::width), new Iri('internal:w')),
            new Triple(new Iri('internal:w'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple(new Iri('internal:w'), new Iri(Cargo::numericalValue), Literal::double(0.5)),
        ]));
        $this->server->objects->create($legacy, $this->clock->now());
        $holder = new DataHolder($this->server->services);

        $edited = $holder->change(new Change($p, 1, [Operation::add($theirs, new Iri(Cargo::height), new OperationObject(Cargo::Value, '_:h')), Operation::add(new \LambdaTwelve\OneRecord\Rdf\BlankNode('h'), new Iri(Cargo::numericalValue), OperationObject::literal(Literal::double(1.0)))]));
        self::assertSame(RequestStatus::Accepted, $edited->status, 'the node is one of the object\'s, whatever its id');
        $unlinked = $holder->change(new Change($p, 2, [Operation::delete($p, new Iri(Cargo::dimensions), new OperationObject(Cargo::Dimensions, $theirs->value))]));
        self::assertSame(RequestStatus::Accepted, $unlinked->status);
        self::assertSame([], $this->server->objects->latest($p)?->object->graph->about($theirs) ?? ['x'], 'and it is cleansed when unlinked');
    }

    public function testAnEmbeddedEventNodeWithAClientChosenIdIsValidated(): void
    {
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);
        $event = static fn(array $code, array $location): string => json_encode([
            '@context' => ['cargo' => Cargo::NAMESPACE],
            '@type' => 'cargo:LogisticsEvent',
            'cargo:eventCode' => $code,
            'cargo:eventDate' => ['@type' => self::XSD . 'dateTime', '@value' => '2026-10-02T11:00:00Z'],
            'cargo:eventLocation' => $location,
        ], JSON_THROW_ON_ERROR);
        $post = fn(string $body) => $this->request('POST', '/logistics-objects/piece-1/logistics-events', self::PARTNER, [], $body);
        $dep = ['@id' => 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP'];
        $fra = ['@id' => 'https://1r.example.com/logistics-objects/fra'];

        // The reviewer's probe: a location with an id of its own, of a class the ontology does not know.
        self::assertSame(400, $post($event($dep, ['@id' => 'https://attacker.example/custom-loc-1', '@type' => 'https://example.com/FakeLocation', 'https://example.com/fakeProp' => 'x']))->getStatusCode());
        // A described location is a logistics object embedded, whatever id it carries.
        self::assertSame(400, $post($event($dep, ['@id' => 'https://company.example/locations/fra', '@type' => 'cargo:Location', 'cargo:locationName' => 'Frankfurt']))->getStatusCode());
        // The nearest inputs that must pass: a typed link to the location, and an embedded code-list element
        // the client identified itself.
        $fine = $post($event(['@id' => 'https://company.example/codes/DEP', '@type' => 'cargo:CodeListElement', 'cargo:code' => 'DEP'], $fra + ['@type' => 'cargo:Location']));
        self::assertSame(201, $fine->getStatusCode(), (string) $fine->getBody());
        self::assertSame(400, $post($event(['@id' => 'https://company.example/codes/DEP', '@type' => 'cargo:CodeListElement', 'cargo:coload' => true], $fra))->getStatusCode(), 'and its properties are judged');
    }
}
