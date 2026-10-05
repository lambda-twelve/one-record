<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Integration;

use LambdaTwelve\OneRecord\Api\Permission;
use LambdaTwelve\OneRecord\Api\RequestStatus;
use LambdaTwelve\OneRecord\Change\Change;
use LambdaTwelve\OneRecord\Change\Operation;
use LambdaTwelve\OneRecord\Change\OperationObject;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Server\ChangeFailed;
use LambdaTwelve\OneRecord\Server\DataHolder;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversNothing;
use Psr\Http\Message\ResponseInterface;

/**
 * Review 14: what one entry point accepts, the next must not break; what one route refuses, the other must too.
 */
#[CoversNothing]
final class AdversarialRound14Test extends ServerTestCase
{
    private const string XSD = 'http://www.w3.org/2001/XMLSchema#';

    /**
     * @param array<string, mixed> $document
     */
    private function create(array $document): ResponseInterface
    {
        return $this->request('POST', '/logistics-objects', self::HOLDER, [], json_encode(['@context' => ['cargo' => Cargo::NAMESPACE, 'xsd' => self::XSD]] + $document, JSON_THROW_ON_ERROR));
    }

    public function testR14001ANestedLogisticsObjectAcceptedAtCreationSurvivesUnrelatedChanges(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $created = $this->create(['@type' => 'cargo:Piece', 'cargo:contactPersons' => ['@type' => 'cargo:Person', 'cargo:firstName' => 'Alice']]);
        self::assertSame(201, $created->getStatusCode(), (string) $created->getBody());
        $iri = new Iri($created->getHeaderLine('Location'));
        $holder = new DataHolder($this->server->services);

        $edited = $holder->change(new Change($iri, 1, [Operation::add($iri, new Iri(Cargo::goodsDescription), OperationObject::literal(Literal::string('Books')))]));
        self::assertSame(RequestStatus::Accepted, $edited->status, 'the reviewer\'s probe: an unrelated change (R14-001)');
        $stored = $this->server->objects->latest($iri);
        self::assertNotNull($stored);
        self::assertSame(2, $stored->revision);
        $person = $stored->object->graph->firstObject($iri, Cargo::contactPersons);
        self::assertInstanceOf(Iri::class, $person);
        $edited = $holder->change(new Change($iri, 2, [Operation::add($person, new Iri(Cargo::lastName), OperationObject::literal(Literal::string('Example')))]));
        self::assertSame(RequestStatus::Accepted, $edited->status, 'and the nested object itself can still be edited');

        // Introducing a nested logistics object through a change is refused as before, by either route.
        foreach ([
            [Operation::add($iri, new Iri(Cargo::contactPersons), new OperationObject(Cargo::Person, '_:p')), Operation::add(new BlankNode('p'), new Iri(Cargo::firstName), OperationObject::literal(Literal::string('Bob')))],
            [Operation::add($person, new Iri(Graph::RDF_TYPE), new OperationObject(Cargo::Piece, Cargo::Piece))],
        ] as $operations) {
            try {
                $holder->change(new Change($iri, 3, $operations));
                self::fail('a change must not introduce a nested logistics object');
            } catch (ChangeFailed $e) {
                self::assertStringContainsString('Invalid resource', $e->getMessage());
            }
        }
    }

    public function testR14002ALanguageTagDoesNotBypassANumericRange(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        $uld = fn(mixed $doors): ResponseInterface => $this->create(['@type' => 'cargo:ULD', 'cargo:numberOfDoors' => $doors]);

        self::assertError($uld(['@value' => 'many', '@language' => 'en']), 400, 'Invalid resource');
        self::assertError($uld('many'), 400, 'Invalid resource');
        self::assertSame(201, $uld(['@value' => '2', '@type' => 'xsd:integer'])->getStatusCode());
        // Localised text where text is expected is fine. (A change cannot carry a language tag at all:
        // spec question 29, so the change applier meets tagged literals only in stored graphs.)
        $created = $this->create(['@type' => 'cargo:Piece', 'cargo:goodsDescription' => ['@value' => 'Bücher', '@language' => 'de']]);
        self::assertSame(201, $created->getStatusCode(), (string) $created->getBody());
    }

    public function testR14003BulkEventsRefuseAMalformedTargetInsteadOfDroppingIt(): void
    {
        $this->server = $this->makeServer(bulkEvents: true);
        $piece = $this->storePiece();
        $this->server->policy->allow(new Iri(self::PARTNER), $piece->iri, [Permission::PostLogisticsEvent]);
        $post = fn(array $targets): ResponseInterface => $this->request('POST', '/logistics-events', self::PARTNER, [], json_encode([
            '@context' => ['cargo' => Cargo::NAMESPACE, 'xsd' => self::XSD],
            '@type' => 'cargo:LogisticsEvent',
            'cargo:eventDate' => ['@type' => 'xsd:dateTime', '@value' => '2026-10-02T11:00:00Z'],
            'cargo:eventFor' => $targets,
        ], JSON_THROW_ON_ERROR));

        self::assertError($post([['@id' => $piece->iri->value], 'not-an-IRI']), 400, 'Invalid resource');
        self::assertError($post([['@id' => $piece->iri->value], ['@type' => 'cargo:Piece']]), 400, 'Invalid resource');
        self::assertNull($this->server->events->lastModified($piece->iri), 'nothing was created for the valid target either');
        self::assertSame(207, $post([['@id' => $piece->iri->value]])->getStatusCode());
    }

    public function testR14004AMalformedIdIsAClientErrorNotAnOmission(): void
    {
        $this->server->policy->addInternal(new Iri(self::HOLDER));
        foreach ([42, ['bad'], '', '   ', self::BASE . '/logistics-objects/bad id', 'https://elsewhere.example/logistics-objects/x'] as $id) {
            $response = $this->create(['@id' => $id, '@type' => 'cargo:Piece']);
            self::assertError($response, 400, 'Invalid body request');
            self::assertSame('', $response->getHeaderLine('Location'), json_encode($id, JSON_THROW_ON_ERROR));
        }
        self::assertSame(201, $this->create(['@id' => self::BASE . '/logistics-objects/chosen', '@type' => 'cargo:Piece'])->getStatusCode());
        self::assertSame(201, $this->create(['@type' => 'cargo:Piece'])->getStatusCode(), 'left out: the server assigns one');
    }
}
