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

    public function testR11001AnEmbeddedNodeWithAnIdOfItsOwnIsValidatedLikeAnyOther(): void
    {
        $p = new Iri(self::PIECE);
        foreach (['https://attacker.example/custom-dim-1', 'urn:uuid:7c9e6679-7425-40de-944b-e07fc1f90ae7', 'neone:17'] as $id) {
            $node = new Iri($id);
            $graph = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), $node), new Triple($node, new Iri(Graph::RDF_TYPE), new Iri('https://example.com/FakeDimensionsClass')), new Triple($node, new Iri('https://example.com/fakeProp'), Literal::string('x'))]);
            self::assertSame(['"https://example.com/FakeDimensionsClass" is not a class of the ontology.', '"https://example.com/fakeProp" is not a property of the ontology.'], self::messages($graph), $id);

            $valid = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), $node), new Triple($node, new Iri(Graph::RDF_TYPE), new Iri(Cargo::Dimensions)), new Triple($node, new Iri(Cargo::height), new Iri('internal:h')), new Triple(new Iri('internal:h'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)), new Triple(new Iri('internal:h'), new Iri(Cargo::numericalValue), Literal::string('tall'))]);
            self::assertSame(['https://onerecord.iata.org/ns/cargo#numericalValue expects http://www.w3.org/2001/XMLSchema#double, got http://www.w3.org/2001/XMLSchema#string.'], self::messages($valid), 'and so is what hangs below it: ' . $id);
        }
    }

    public function testR12001ATypedLinkMustNameAClassTheOntologyKnows(): void
    {
        $p = new Iri(self::PIECE);
        $link = static fn(string $property, string $target, string $class): Graph => new Graph([...self::piece(), new Triple($p, new Iri($property), new Iri($target)), new Triple(new Iri($target), new Iri(Graph::RDF_TYPE), new Iri($class))]);

        // The reviewer's probe: a type-only node with an id of its own, of a class nobody knows.
        self::assertSame(['"https://example.com/FakeDimensionsClass" is not a class of the ontology.'], self::messages($link(Cargo::dimensions, 'https://attacker.example/only-type-dim', 'https://example.com/FakeDimensionsClass')));
        // The nearest links that must pass: a logistics object, a code-list member typed with its list.
        self::assertSame([], self::messages($link(Cargo::containedPieces, 'https://1r.example.com/logistics-objects/p2', Cargo::Piece)));
        $unit = new Iri('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM');
        $weight = new Graph([...self::piece(), new Triple($p, new Iri(Cargo::grossWeight), new Iri('internal:w')), new Triple(new Iri('internal:w'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)), new Triple(new Iri('internal:w'), new Iri(Cargo::numericalValue), Literal::double(1.0)), new Triple(new Iri('internal:w'), new Iri(Cargo::unit), $unit), new Triple($unit, new Iri(Graph::RDF_TYPE), new Iri('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode'))]);
        self::assertSame([], self::messages($weight));
        // A typed link of a known class outside the range is still caught by the range.
        self::assertCount(1, self::messages($link(Cargo::dimensions, 'https://1r.example.com/logistics-objects/p2', Cargo::Piece)));
    }

    public function testR13001ACodeListIsAClassForTheRangeCheck(): void
    {
        $p = new Iri(self::PIECE);
        $units = 'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode';
        $currencies = 'https://onerecord.iata.org/ns/code-lists/CurrencyCode';
        $weight = static fn(Iri $unit, ?string $unitType): Graph => new Graph(array_filter([...self::piece(), new Triple($p, new Iri(Cargo::grossWeight), new Iri('internal:w')), new Triple(new Iri('internal:w'), new Iri(Graph::RDF_TYPE), new Iri(Cargo::Value)), new Triple(new Iri('internal:w'), new Iri(Cargo::numericalValue), Literal::double(1.0)), new Triple(new Iri('internal:w'), new Iri(Cargo::unit), $unit), $unitType === null ? null : new Triple($unit, new Iri(Graph::RDF_TYPE), new Iri($unitType))]));

        // The reviewer's probes: a unit typed with the wrong list, and a code list where a class is expected.
        self::assertSame([Cargo::unit . ' expects ' . $units . ', got ' . $currencies . '.'], self::messages($weight(new Iri($currencies . '#EUR'), $currencies)));
        self::assertSame([Cargo::dimensions . ' expects ' . Cargo::Dimensions . ', got ' . $units . '.'], self::messages(new Graph([...self::piece(), new Triple($p, new Iri(Cargo::dimensions), new Iri($units . '#KGM')), new Triple(new Iri($units . '#KGM'), new Iri(Graph::RDF_TYPE), new Iri($units))])));
        // The neighbour: an untyped member of the wrong list is judged by its IRI.
        self::assertSame([Cargo::unit . ' expects ' . $units . ', got a member of ' . $currencies . '.'], self::messages($weight(new Iri($currencies . '#EUR'), null)));
        // What must pass: the right list typed or untyped, a unit from elsewhere, and a member under a
        // property whose range is a class rather than a list (eventCode expects a CodeListElement).
        self::assertSame([], self::messages($weight(new Iri($units . '#KGM'), $units)));
        self::assertSame([], self::messages($weight(new Iri($units . '#KGM'), null)));
        self::assertSame([], self::messages($weight(new Iri('https://vocab.unece.org/rec20#KGM'), null)));
        $e = new Iri('https://1r.example.com/logistics-objects/p1/logistics-events/e1');
        $event = new Graph([new Triple($e, new Iri(Graph::RDF_TYPE), new Iri(Cargo::LogisticsEvent)), new Triple($e, new Iri(Cargo::eventCode), new Iri('https://onerecord.iata.org/ns/code-lists/StatusCode#DEP'))]);
        self::assertSame([], array_map(static fn(GraphViolation $v): string => $v->message, (new GraphValidator(Vocabulary::default()))->validate($event, $e)));
    }
}
