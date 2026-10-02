<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\JsonLd;

use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\Diff;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Comparer::class)]
#[CoversClass(Diff::class)]
#[UsesClass(JsonLd::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Expander::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\ExpandedDocument::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Context::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Json::class)]
#[UsesClass(Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(Triple::class)]
final class ComparerTest extends TestCase
{
    private const string CTX = '{"cargo": "https://onerecord.iata.org/ns/cargo#", "xsd": "http://www.w3.org/2001/XMLSchema#"}';

    private static function graph(string $body): Graph
    {
        return JsonLd::expand('{"@context": ' . self::CTX . ', ' . $body . '}')->graph;
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function equivalentDocuments(): iterable
    {
        yield 'key order' => [
            '"@id": "https://x/1", "cargo:a": "1", "cargo:b": "2"',
            '"cargo:b": "2", "@id": "https://x/1", "cargo:a": "1"',
        ];
        yield 'single value vs one-element list' => [
            '"@id": "https://x/1", "cargo:a": "1"',
            '"@id": "https://x/1", "cargo:a": ["1"]',
        ];
        yield 'list order' => [
            '"@id": "https://x/1", "cargo:a": ["1", "2"]',
            '"@id": "https://x/1", "cargo:a": ["2", "1"]',
        ];
        yield 'native vs typed boolean' => [
            '"@id": "https://x/1", "cargo:a": true',
            '"@id": "https://x/1", "cargo:a": {"@type": "xsd:boolean", "@value": "true"}',
        ];
        yield 'native vs typed integer with leading zeros' => [
            '"@id": "https://x/1", "cargo:a": 7',
            '"@id": "https://x/1", "cargo:a": {"@type": "xsd:integer", "@value": "007"}',
        ];
        yield 'double lexical forms' => [
            '"@id": "https://x/1", "cargo:a": 20.0',
            '"@id": "https://x/1", "cargo:a": {"@type": "xsd:double", "@value": "20.0"}',
        ];
        yield 'compact vs full IRI keys and types' => [
            '"@id": "https://x/1", "@type": "cargo:Piece", "cargo:a": "1"',
            '"@id": "https://x/1", "@type": "https://onerecord.iata.org/ns/cargo#Piece", "https://onerecord.iata.org/ns/cargo#a": "1"',
        ];
        yield 'blank node labels' => [
            '"@id": "https://x/1", "cargo:w": {"@id": "_:a", "cargo:v": 1}',
            '"@id": "https://x/1", "cargo:w": {"@id": "_:zz", "cargo:v": 1}',
        ];
        yield 'labelled vs unlabelled blank node' => [
            '"@id": "https://x/1", "cargo:w": {"@id": "_:a", "cargo:v": 1}',
            '"@id": "https://x/1", "cargo:w": {"cargo:v": 1}',
        ];
        yield 'embedded object ids from different servers' => [
            '"@id": "https://x/1", "cargo:w": {"@id": "internal:7fc8", "cargo:v": 1}',
            '"@id": "https://x/1", "cargo:w": {"@id": "internal:other-id", "cargo:v": 1}',
        ];
        yield 'two identical siblings' => [
            '"@id": "https://x/1", "cargo:w": [{"cargo:v": 1}, {"cargo:v": 1}]',
            '"@id": "https://x/1", "cargo:w": [{"cargo:v": 1}, {"cargo:v": 1}]',
        ];
        yield 'siblings in different order' => [
            '"@id": "https://x/1", "cargo:w": [{"cargo:v": 1, "cargo:n": "a"}, {"cargo:v": 2, "cargo:n": "b"}]',
            '"@id": "https://x/1", "cargo:w": [{"cargo:v": 2, "cargo:n": "b"}, {"cargo:v": 1, "cargo:n": "a"}]',
        ];
        yield 'deep nesting' => [
            '"@id": "https://x/1", "cargo:a": {"cargo:b": {"cargo:c": {"cargo:d": "deep"}}}',
            '"@id": "https://x/1", "cargo:a": {"cargo:b": {"cargo:c": {"cargo:d": "deep"}}}',
        ];
    }

    #[DataProvider('equivalentDocuments')]
    public function testRecognisesEquivalentDocuments(string $left, string $right): void
    {
        $comparer = new Comparer();
        $diff = $comparer->compare(self::graph($left), self::graph($right));

        self::assertTrue($diff->isEqual(), $diff->describe());
        self::assertSame('The graphs are isomorphic.', $diff->describe());
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function differentDocuments(): iterable
    {
        yield 'different literal' => ['"@id": "https://x/1", "cargo:a": "1"', '"@id": "https://x/1", "cargo:a": "2"'];
        yield 'string vs integer' => ['"@id": "https://x/1", "cargo:a": "1"', '"@id": "https://x/1", "cargo:a": 1'];
        yield 'different subject' => ['"@id": "https://x/1", "cargo:a": "1"', '"@id": "https://x/2", "cargo:a": "1"'];
        yield 'embedded vs referenced' => [
            '"@id": "https://x/1", "cargo:w": {"cargo:v": 1}',
            '"@id": "https://x/1", "cargo:w": {"@id": "https://x/w"}',
        ];
        yield 'extra triple' => ['"@id": "https://x/1", "cargo:a": "1"', '"@id": "https://x/1", "cargo:a": "1", "cargo:b": "2"'];
        yield 'language tag' => ['"@id": "https://x/1", "cargo:a": "x"', '"@id": "https://x/1", "cargo:a": {"@value": "x", "@language": "en"}'];
        yield 'siblings differ inside' => [
            '"@id": "https://x/1", "cargo:w": [{"cargo:v": 1}, {"cargo:v": 2}]',
            '"@id": "https://x/1", "cargo:w": [{"cargo:v": 1}, {"cargo:v": 3}]',
        ];
        yield 'shared vs separate blank nodes' => [
            '"@id": "https://x/1", "cargo:a": {"@id": "_:s", "cargo:v": 1}, "cargo:b": {"@id": "_:s"}',
            '"@id": "https://x/1", "cargo:a": {"cargo:v": 1}, "cargo:b": {"cargo:v": 1}',
        ];
    }

    #[DataProvider('differentDocuments')]
    public function testDetectsDifferences(string $left, string $right): void
    {
        $diff = (new Comparer())->compare(self::graph($left), self::graph($right));

        self::assertFalse($diff->isEqual());
        self::assertNotSame('', $diff->describe());
    }

    public function testDiffListsTheOffendingTriples(): void
    {
        $diff = (new Comparer())->compare(self::graph('"@id": "https://x/1", "cargo:a": "1"'), self::graph('"@id": "https://x/1", "cargo:a": "2"'));

        self::assertCount(1, $diff->onlyInLeft);
        self::assertCount(1, $diff->onlyInRight);
        self::assertSame("- <https://x/1> <https://onerecord.iata.org/ns/cargo#a> \"1\" .\n+ <https://x/1> <https://onerecord.iata.org/ns/cargo#a> \"2\" .", $diff->describe());
    }

    public function testOptions(): void
    {
        $strict = new Comparer(blankNodePrefixes: [], normaliseNumbers: false);
        self::assertFalse($strict->isomorphic(self::graph('"@id": "https://x/1", "cargo:a": 20.0'), self::graph('"@id": "https://x/1", "cargo:a": {"@type": "xsd:double", "@value": "20.0"}')));
        self::assertFalse($strict->isomorphic(self::graph('"@id": "https://x/1", "cargo:w": {"@id": "internal:a", "cargo:v": 1}'), self::graph('"@id": "https://x/1", "cargo:w": {"@id": "internal:b", "cargo:v": 1}')));

        $neone = new Comparer(blankNodePrefixes: ['internal:', 'neone:']);
        self::assertTrue($neone->isomorphic(self::graph('"@id": "https://x/1", "cargo:w": {"@id": "internal:a", "cargo:v": 1}'), self::graph('"@id": "https://x/1", "cargo:w": {"@id": "neone:21312", "cargo:v": 1}')));

        $noLang = new Comparer(ignoreLanguageTags: true);
        self::assertTrue($noLang->isomorphic(self::graph('"@id": "https://x/1", "cargo:a": "x"'), self::graph('"@id": "https://x/1", "cargo:a": {"@value": "x", "@language": "en-US"}')));
    }

    public function testCanonicalFormIsStableAcrossLabelsAndOrder(): void
    {
        $comparer = new Comparer();
        $a = $comparer->canonical(self::graph('"@id": "https://x/1", "cargo:w": [{"@id": "_:p", "cargo:v": 1}, {"@id": "_:q", "cargo:v": 2}]'));
        $b = $comparer->canonical(self::graph('"@id": "https://x/1", "cargo:w": [{"@id": "_:q", "cargo:v": 2}, {"@id": "_:p", "cargo:v": 1}]'));

        self::assertSame(
            array_map(static fn(Triple $t): string => $t->toNTriples(), $a->sorted()),
            array_map(static fn(Triple $t): string => $t->toNTriples(), $b->sorted()),
        );
    }

    public function testAr010SymmetricTreesWithSwappedBlankLabelsAreIsomorphic(): void
    {
        // r p _:a . r p _:b . _:a q _:c . _:b q _:d . _:c q "x" . _:d q "x" ; then c and d renamed into each other.
        $build = static function (string $c, string $d): Graph {
            $g = new Graph();
            $r = new Iri('https://x.example/r');
            $p = new Iri('https://x.example/p');
            $q = new Iri('https://x.example/q');
            foreach ([['a', $c], ['b', $d]] as [$parent, $child]) {
                $g->add(new Triple($r, $p, new BlankNode($parent)));
                $g->add(new Triple(new BlankNode($parent), $q, new BlankNode($child)));
                $g->add(new Triple(new BlankNode($child), $q, Literal::string('x')));
            }

            return $g;
        };
        $comparer = new Comparer();
        self::assertTrue($comparer->isomorphic($build('c', 'd'), $build('d', 'c')), 'a bijective relabelling is the same graph');
        self::assertTrue($comparer->compare($build('c', 'd'), $build('d', 'c'))->isEqual());

        // Nearby non-isomorphic: one leaf says "y".
        $other = $build('c', 'd');
        $other->remove(new Triple(new BlankNode('d'), new Iri('https://x.example/q'), Literal::string('x')));
        $other->add(new Triple(new BlankNode('d'), new Iri('https://x.example/q'), Literal::string('y')));
        self::assertFalse($comparer->isomorphic($build('c', 'd'), $other));

        // A two-node blank cycle rooted at a blank node, relabelled.
        $cycle = static function (string $m, string $n): Graph {
            $g = new Graph();
            $p = new Iri('https://x.example/p');
            $g->add(new Triple(new BlankNode($m), $p, new BlankNode($n)));
            $g->add(new Triple(new BlankNode($n), $p, new BlankNode($m)));
            $g->add(new Triple(new BlankNode($m), new Iri('https://x.example/q'), Literal::string('m')));

            return $g;
        };
        self::assertTrue($comparer->isomorphic($cycle('m', 'n'), $cycle('zz', 'aa')));
    }

    public function testAr011DecimalsCompareExactlyAndDoublesStayDoubles(): void
    {
        $comparer = new Comparer();
        $decimal = static fn(string $v): Literal => new Literal($v, Literal::XSD_DECIMAL);
        self::assertFalse($comparer->normaliseLiteral($decimal('9007199254740992'))->equals($comparer->normaliseLiteral($decimal('9007199254740993'))), 'adjacent decimals beyond 2^53 differ');
        self::assertFalse($comparer->normaliseLiteral($decimal('0.12345678901234567890'))->equals($comparer->normaliseLiteral($decimal('0.12345678901234567891'))));
        self::assertTrue($comparer->normaliseLiteral($decimal('1.50'))->equals($comparer->normaliseLiteral($decimal('01.5'))), 'lexical variants of one value');
        self::assertTrue($comparer->normaliseLiteral($decimal('-0.0'))->equals($comparer->normaliseLiteral($decimal('0'))));
        self::assertSame(Literal::XSD_DECIMAL, $comparer->normaliseLiteral($decimal('1.5'))->datatype, 'a decimal does not become a double');
        self::assertSame('not a number', $comparer->normaliseLiteral($decimal('not a number'))->lexical, 'a non-decimal lexical is left alone');
        self::assertTrue($comparer->normaliseLiteral(new Literal('2.0E1', Literal::XSD_DOUBLE))->equals($comparer->normaliseLiteral(new Literal('20.0', Literal::XSD_DOUBLE))));
    }
}
