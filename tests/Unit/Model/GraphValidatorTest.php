<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Model;

use LambdaTwelve\OneRecord\Model\GraphValidator;
use LambdaTwelve\OneRecord\Model\GraphViolation;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

/**
 * The one validator behind creation, changes and events (review 10).
 */
#[CoversClass(GraphValidator::class)]
#[CoversClass(GraphViolation::class)]
final class GraphValidatorTest extends TestCase
{
    private const string PIECE = 'https://1r.example.com/logistics-objects/p1';

    /** @return list<Triple> */
    private static function piece(): array
    {
        $p = new Iri(self::PIECE);

        return [new Triple($p, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)), new Triple($p, new Iri(Cargo::goodsDescription), Literal::string('Books'))];
    }

    /**
     * @param list<string> $tolerated
     * @return list<string>
     */
    private static function messages(Graph $graph, array $tolerated = []): array
    {
        return array_map(static fn(GraphViolation $v): string => $v->message, (new GraphValidator(Vocabulary::default()))->validate($graph, new Iri(self::PIECE), $tolerated));
    }

    public function testAWellFormedObjectWithEmbeddedNodesHasNoViolations(): void
    {
        $p = new Iri(self::PIECE);
        $d = new Iri('internal:d');
        $v = new BlankNode('v');
        $graph = new Graph([...self::piece(),
            new Triple($p, new Iri(Cargo::dimensions), $d),
            new Triple($d, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)),
            new Triple($d, new Iri(Cargo::width), $v),
            new Triple($v, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple($v, new Iri(Cargo::numericalValue), new Literal('2', 'http://www.w3.org/2001/XMLSchema#int')),
            new Triple($v, new Iri(Cargo::unit), new Iri('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#MTR')),
            new Triple($p, new Iri(Cargo::containedPieces), new Iri('https://1r.example.com/logistics-objects/p2')),
        ]);
        self::assertSame([], self::messages($graph), 'a derived integer, a code-list reference and an external reference are all fine');
    }

    public function testEmbeddedNodesMustBeTypedWithAnOntologyClassThatIsNotALogisticsObject(): void
    {
        $p = new Iri(self::PIECE);
        $untyped = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), new Iri('internal:d')), new Triple(new Iri('internal:d'), new Iri('https://example.com/fakeProp'), Literal::string('injected'))]);
        self::assertSame(['An embedded object must declare its @type.'], self::messages($untyped), 'an untyped node is a violation, not a node nobody checks (R10-001)');

        $fake = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), new Iri('internal:d')), new Triple(new Iri('internal:d'), new Iri(Graph::RDF_TYPE), new Iri('https://example.com/FakeClass')), new Triple(new Iri('internal:d'), new Iri('https://example.com/fakeProp'), Literal::string('injected'))]);
        self::assertSame(['"https://example.com/FakeClass" is not a class of the ontology.', '"https://example.com/fakeProp" is not a property of the ontology.'], self::messages($fake));

        $embeddedPiece = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), new BlankNode('x')), new Triple(new BlankNode('x'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece))]);
        $messages = self::messages($embeddedPiece);
        self::assertCount(2, $messages, 'the wrong range at the link, and a logistics object embedded (R10-002)');
        self::assertStringContainsString('logistics object class', $messages[1], 'a Piece has its own URI');
    }

    public function testObjectPropertyRangesAreCheckedWhenTheTargetsClassesAreKnown(): void
    {
        $p = new Iri(self::PIECE);
        $person = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), new Iri('internal:x')), new Triple(new Iri('internal:x'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Person)), new Triple(new Iri('internal:x'), new Iri(Cargo::firstName), Literal::string('Alice'))]);
        $messages = self::messages($person);
        self::assertCount(2, $messages, 'the wrong range at the link, and a logistics object embedded (R10-002)');
        self::assertStringContainsString('dimensions expects', $messages[0]);
        self::assertStringContainsString('logistics object class', $messages[1]);

        // A subclass of the range is within it; a reference whose classes the graph does not know is not judged.
        $s = new Iri('https://1r.example.com/logistics-objects/s1');
        $subclass = new Graph([new Triple($s, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Shipment)), new Triple($s, new Iri(Cargo::pieces), new Iri('https://1r.example.com/logistics-objects/p2')), new Triple(new Iri('https://1r.example.com/logistics-objects/p2'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::PieceDg)), new Triple($s, new Iri(Cargo::pieces), new Iri('https://1r.example.com/logistics-objects/p3'))]);
        self::assertSame([], array_map(static fn(GraphViolation $v): string => $v->message, (new GraphValidator(Vocabulary::default()))->validate($subclass, $s)));

        // Creation over HTTP may keep a nested logistics object embedded (spec question 33); nothing else may.
        $nested = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::contactPersons), new BlankNode('x')), new Triple(new BlankNode('x'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Person)), new Triple(new BlankNode('x'), new Iri(Cargo::firstName), Literal::string('Alice'))]);
        self::assertSame([], array_map(static fn(GraphViolation $v): string => $v->message, (new GraphValidator(Vocabulary::default()))->validate($nested, new Iri(self::PIECE), [], nestedLogisticsObjects: true)));
        self::assertCount(1, self::messages($nested));
    }

    public function testPropertyKindsRangesAndGrammarsApplyToTheRootToo(): void
    {
        $p = new Iri(self::PIECE);
        $graph = new Graph([
            new Triple($p, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Piece)),
            new Triple($p, new Iri(Cargo::goodsDescription), new Iri('https://example.com/not-a-literal')),
            new Triple($p, new Iri(Cargo::dimensions), Literal::string('not a node')),
            new Triple($p, new Iri(Cargo::coload), new Literal('maybe', Literal::XSD_BOOLEAN)),
            new Triple($p, new Iri(Cargo::grossWeight), new Iri('internal:w')),
            new Triple(new Iri('internal:w'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)),
            new Triple(new Iri('internal:w'), new Iri(Cargo::numericalValue), Literal::string('twenty')),
        ]);
        self::assertSame([
            'https://onerecord.iata.org/ns/cargo#goodsDescription takes a literal value.',
            'https://onerecord.iata.org/ns/cargo#dimensions takes an object or reference, not a literal.',
            '"maybe" is not a valid http://www.w3.org/2001/XMLSchema#boolean.',
            'https://onerecord.iata.org/ns/cargo#numericalValue expects http://www.w3.org/2001/XMLSchema#double, got http://www.w3.org/2001/XMLSchema#string.',
        ], self::messages($graph));
    }

    public function testApiPropertiesAreTheServersUnlessTolerated(): void
    {
        $p = new Iri(self::PIECE);
        $graph = new Graph([...self::piece(), new Triple($p, new Iri(Api::hasRevision), Literal::integer(3))]);
        self::assertSame(['https://onerecord.iata.org/ns/api#hasRevision is set by the server, not through a document.'], self::messages($graph));
        self::assertSame([], self::messages($graph, [Api::hasRevision]));

        $untypedRoot = new Graph([new Triple($p, new Iri(Cargo::goodsDescription), Literal::string('Books'))]);
        self::assertSame(['The object declares no @type.'], self::messages($untypedRoot));
        $fakeRoot = new Graph([new Triple($p, new Iri(Graph::RDF_TYPE), new Iri('https://example.com/Thing'))]);
        self::assertSame(['"https://example.com/Thing" is not a class of the ontology.'], self::messages($fakeRoot));
    }
}
