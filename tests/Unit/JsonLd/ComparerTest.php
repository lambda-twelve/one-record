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
}
