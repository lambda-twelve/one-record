<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Model;

use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\Uuid5EmbeddedIdMinter;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LogisticsObject::class)]
final class EmbeddedIdsTest extends TestCase
{
    public function testEveryEmbeddedNodeGetsAServerMintedIdWhateverIdItCameWith(): void
    {
        $p = new Iri('https://1r.example.com/logistics-objects/p1');
        $theirs = new Iri('https://company.example/dims/101');
        $other = new Iri('https://1r.example.com/logistics-objects/p2');
        $unit = new Iri('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM');
        $object = new LogisticsObject($p, new Graph([
            new Triple($p, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($p, new Iri(Cargo::dimensions), $theirs),
            new Triple($theirs, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($theirs, new Iri(Cargo::height), new BlankNode('h')),
            new Triple(new BlankNode('h'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple(new BlankNode('h'), new Iri(Cargo::numericalValue), Literal::double(1.5)),
            new Triple(new BlankNode('h'), new Iri(Cargo::unit), $unit),
            new Triple($p, new Iri(Cargo::containedPieces), $other),
        ]));

        $stored = $object->withEmbeddedIds(new Uuid5EmbeddedIdMinter());

        $dims = $stored->graph->firstObject($p, Cargo::dimensions);
        self::assertInstanceOf(Iri::class, $dims);
        self::assertStringStartsWith('internal:', $dims->value, 'the client\'s id for an embedded node is replaced by the server\'s (R11-001)');
        self::assertSame([], $stored->graph->about($theirs), 'nothing is left under the old id');
        $height = $stored->graph->firstObject($dims, Cargo::height);
        self::assertInstanceOf(Iri::class, $height);
        self::assertStringStartsWith('internal:', $height->value, 'blank nodes as before');
        self::assertTrue($stored->graph->has(new Triple($p, new Iri(Cargo::containedPieces), $other)), 'a reference without triples of its own is a reference');
        self::assertTrue($stored->graph->has(new Triple($height, new Iri(Cargo::unit), $unit)), 'a code-list member too');
        self::assertCount(2, $stored->embeddedNodes());
        self::assertTrue($stored->withEmbeddedIds(new Uuid5EmbeddedIdMinter())->isSameAs($stored), 'minting is idempotent');
        self::assertTrue($object->withEmbeddedIds(new Uuid5EmbeddedIdMinter())->isSameAs($stored), 'and deterministic');
    }
}
