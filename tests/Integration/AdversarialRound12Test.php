<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\Operation;
use LambdaTwelve\OneRecord\Change\OperationObject;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Server\ChangeFailed;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;

/**
 * Review 12: a type-only node with an id of its own is a typed link. Its class is judged,
 * it survives unrelated changes, and it is a reference rather than an embedded node.
 */
#[CoversNothing]
final class AdversarialRound12Test extends ServerTestCase
{
    private const string XSD = 'http://www.w3.org/2001/XMLSchema#';

    /**
     * @param array<string, mixed> $properties
     */
    private function post(string $path, array $properties, string $type = 'cargo:Piece'): \Psr\Http\Message\ResponseInterface
    {
        return $this->request('POST', $path, self::HOLDER, [], json_encode(['@context' => ['cargo' => Cargo::NAMESPACE], '@type' => $type] + $properties, JSON_THROW_ON_ERROR));
    }

    public function testR12001ATypedLinkOfAnUnknownClassIsRefusedOnCreationAndOnEvents(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $fake = $this->post('/logistics-objects', ['cargo:dimensions' => ['@id' => 'https://attacker.example/only-type-dim', '@type' => 'https://example.com/FakeDimensionsClass']]);
        self::assertError($fake, 400, 'Invalid resource');
        self::assertStringContainsString('FakeDimensionsClass', (string) $fake->getBody());

        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::HOLDER), $piece->iri, [Permission::PostLogisticsEvent]);
        $event = static fn(array $location): array => ['cargo:eventDate' => ['@type' => self::XSD . 'dateTime', '@value' => '2026-10-02T11:00:00Z'], 'cargo:eventLocation' => $location];
        self::assertError($this->post('/logistics-objects/piece-1/logistics-events', $event(['@id' => 'https://attacker.example/only-type-loc', '@type' => 'https://example.com/FakeLocation']), 'cargo:LogisticsEvent'), 400, 'Invalid resource');
        // The nearest input that must pass: the same link, typed with the class it has.
        $fine = $this->post('/logistics-objects/piece-1/logistics-events', $event(['@id' => 'https://1r.example.com/logistics-objects/fra', '@type' => 'cargo:Location']), 'cargo:LogisticsEvent');
        self::assertSame(201, $fine->getStatusCode(), (string) $fine->getBody());
    }

    public function testR12002ATypedLinkKeepsItsClassThroughAnUnrelatedChange(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $piece2 = 'https://1r.example.com/logistics-objects/piece-2';
        $created = $this->post('/logistics-objects', ['cargo:pieces' => ['@id' => $piece2, '@type' => 'cargo:Piece']], 'cargo:Shipment');
        self::assertSame(201, $created->getStatusCode(), (string) $created->getBody());
        $iri = new Iri($created->getHeaderLine('Location'));

        $holder = new DataHolder($this->server->services);
        $edited = $holder->change(new Change($iri, 1, [Operation::add($iri, new Iri(Cargo::goodsDescription), OperationObject::literal(Literal::string('Books')))]));
        self::assertSame(RequestStatus::Accepted, $edited->status);
        $graph = $this->server->objects->latest($iri)?->object->graph;
        self::assertNotNull($graph);
        self::assertTrue($graph->has(new Triple($iri, new Iri(Cargo::pieces), new Iri($piece2))));
        self::assertTrue($graph->has(new Triple(new Iri($piece2), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece))), 'the link keeps its class (R12-002)');

        // Deleting the link does take the class away with it: nothing else refers to the node.
        $unlinked = $holder->change(new Change($iri, 2, [Operation::delete($iri, new Iri(Cargo::pieces), new OperationObject(Cargo::Piece, $piece2))]));
        self::assertSame(RequestStatus::Accepted, $unlinked->status);
        self::assertSame([], $this->server->objects->latest($iri)?->object->graph->about(new Iri($piece2)) ?? ['x']);
    }

    public function testR12003ATypeOnlyNodeWithItsOwnIdIsAReferenceNotAnEmbeddedNode(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $theirs = 'https://company.example/dims/minimal-1';
        $created = $this->post('/logistics-objects', ['cargo:dimensions' => ['@id' => $theirs, '@type' => 'cargo:Dimensions']]);
        self::assertSame(201, $created->getStatusCode(), (string) $created->getBody());
        $iri = new Iri($created->getHeaderLine('Location'));
        $read = self::json($this->request('GET', substr($iri->value, \strlen(self::BASE)), self::HOLDER));
        self::assertSame($theirs, self::arr($read['cargo:dimensions'])['@id'], 'a typed link keeps the id it came with');

        // It cannot be edited in place: it is not one of the object's embedded nodes.
        $holder = new DataHolder($this->server->services);
        try {
            $holder->change(new Change($iri, 1, [Operation::add(new Iri($theirs), new Iri(Cargo::height), new OperationObject(Cargo::Value, '_:h'))]));
            self::fail('a reference cannot be described through a change');
        } catch (ChangeFailed $e) {
            self::assertStringContainsString('neither the logistics object nor one of its embedded objects', $e->getMessage());
        }
        // Replacing the link by an embedded node is the way to describe it, and the link survives until then.
        $edited = $holder->change(new Change($iri, 1, [Operation::add($iri, new Iri(Cargo::goodsDescription), OperationObject::literal(Literal::string('Books')))]));
        self::assertSame(RequestStatus::Accepted, $edited->status);
        $graph = $this->server->objects->latest($iri)?->object->graph;
        self::assertNotNull($graph);
        self::assertTrue($graph->has(new Triple(new Iri($theirs), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions))));
    }
}
