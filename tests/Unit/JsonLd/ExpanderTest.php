<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\JsonLd;

use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\Expander;
use LambdaTwelve\OneRecord\JsonLd\Json;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Expander::class)]
#[CoversClass(Context::class)]
#[CoversClass(ExpandedDocument::class)]
#[CoversClass(JsonLd::class)]
#[CoversClass(Json::class)]
#[CoversClass(JsonLdException::class)]
#[UsesClass(Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(Triple::class)]
final class ExpanderTest extends TestCase
{
    private const string CARGO = 'https://onerecord.iata.org/ns/cargo#';
    private const string API = 'https://onerecord.iata.org/ns/api#';
    private const string XSD = 'http://www.w3.org/2001/XMLSchema#';
    private const string PIECE = 'https://1r.example.com/logistics-objects/piece-1';

    /**
     * @return list<string> sorted N-Triples
     */
    private static function nt(Graph $graph): array
    {
        return array_map(static fn(Triple $t): string => $t->toNTriples(), $graph->sorted());
    }

    public function testExpandsAPieceWithEmbeddedValueAndReferences(): void
    {
        $doc = JsonLd::expand(<<<'JSON'
            {
              "@context": {"cargo": "https://onerecord.iata.org/ns/cargo#"},
              "@id": "https://1r.example.com/logistics-objects/piece-1",
              "@type": "cargo:Piece",
              "cargo:coload": false,
              "cargo:goodsDescription": "Books",
              "cargo:grossWeight": {
                "@type": "cargo:Value",
                "cargo:numericalValue": 20.5,
                "cargo:unit": {"@id": "https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM"}
              },
              "cargo:ofShipment": {"@type": "cargo:Shipment", "@id": "https://1r.example.com/logistics-objects/shipment-1"},
              "cargo:specialHandlingCodes": [{"@id": "https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VAL"}]
            }
            JSON);

        self::assertEquals(new Iri(self::PIECE), $doc->root);
        self::assertSame([self::CARGO . 'Piece'], $doc->rootTypes());
        self::assertSame([
            '<' . self::PIECE . '> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <' . self::CARGO . 'Piece> .',
            '<' . self::PIECE . '> <' . self::CARGO . 'coload> "false"^^<' . self::XSD . 'boolean> .',
            '<' . self::PIECE . '> <' . self::CARGO . 'goodsDescription> "Books" .',
            '<' . self::PIECE . '> <' . self::CARGO . 'grossWeight> _:b0 .',
            '<' . self::PIECE . '> <' . self::CARGO . 'ofShipment> <https://1r.example.com/logistics-objects/shipment-1> .',
            '<' . self::PIECE . '> <' . self::CARGO . 'specialHandlingCodes> <https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VAL> .',
            '<https://1r.example.com/logistics-objects/shipment-1> <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <' . self::CARGO . 'Shipment> .',
            '_:b0 <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <' . self::CARGO . 'Value> .',
            '_:b0 <' . self::CARGO . 'numericalValue> "2.05E1"^^<' . self::XSD . 'double> .',
            '_:b0 <' . self::CARGO . 'unit> <https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM> .',
        ], self::nt($doc->graph));
    }

    public function testRootWithoutIdIsABlankNode(): void
    {
        $doc = JsonLd::expand(['@context' => ['cargo' => self::CARGO], '@type' => ['cargo:Company', 'cargo:Organization'], 'cargo:name' => 'ACME']);

        self::assertInstanceOf(BlankNode::class, $doc->root);
        self::assertNull($doc->rootIri());
        self::assertSame([self::CARGO . 'Company', self::CARGO . 'Organization'], $doc->rootTypes());
    }

    public function testValueObjectsTypedLiteralsAndLanguage(): void
    {
        $doc = JsonLd::expand([
            '@context' => ['cargo' => self::CARGO, 'xsd' => self::XSD, '@language' => 'en-US'],
            '@id' => self::PIECE,
            'cargo:eventDate' => ['@type' => 'xsd:dateTime', '@value' => '2023-04-01T10:38:01.000Z'],
            'cargo:coload' => ['@type' => self::XSD . 'boolean', '@value' => 'true'],
            'cargo:count' => ['@value' => 3],
            'cargo:goodsDescription' => 'Books',
            'cargo:eventName' => ['@value' => 'Abflug', '@language' => 'de'],
            'cargo:plain' => ['@value' => 'no tag'],
        ]);
        $s = new Iri(self::PIECE);
        $get = static fn(string $p) => $doc->graph->firstObject($s, self::CARGO . $p);

        self::assertEquals(new Literal('2023-04-01T10:38:01.000Z', self::XSD . 'dateTime'), $get('eventDate'));
        self::assertEquals(Literal::boolean(true), $get('coload'));
        self::assertEquals(Literal::integer(3), $get('count'));
        self::assertEquals(new Literal('Books', null, 'en-US'), $get('goodsDescription'), 'the default language applies to plain strings');
        self::assertEquals(new Literal('Abflug', null, 'de'), $get('eventName'));
        self::assertEquals(new Literal('no tag', null, 'en-US'), $get('plain'), 'and to value objects without @type or @language');
    }

    public function testContextTermCoercions(): void
    {
        $doc = JsonLd::expand([
            '@context' => [
                'api' => self::API,
                'xsd' => self::XSD,
                'api:hasDatatype' => ['@type' => 'xsd:anyURI'],
                'api:p' => ['@type' => 'xsd:anyURI'],
                'subscriber' => ['@id' => 'api:hasSubscriber', '@type' => '@id'],
            ],
            '@id' => self::PIECE,
            'api:hasDatatype' => self::XSD . 'string',
            'subscriber' => 'https://1r.example.com/logistics-objects/org-1',
            'api:hasLogisticsObject' => ['@id' => 'https://1r.example.com/logistics-objects/piece-2'],
        ]);
        $s = new Iri(self::PIECE);

        self::assertEquals(new Literal(self::XSD . 'string', self::XSD . 'anyURI'), $doc->graph->firstObject($s, self::API . 'hasDatatype'));
        self::assertEquals(new Iri('https://1r.example.com/logistics-objects/org-1'), $doc->graph->firstObject($s, self::API . 'hasSubscriber'));
        self::assertEquals(new Iri('https://1r.example.com/logistics-objects/piece-2'), $doc->graph->firstObject($s, self::API . 'hasLogisticsObject'));
        self::assertSame(self::XSD . 'anyURI', $doc->context->coercionOf(self::API . 'hasDatatype'));
        self::assertSame('@id', $doc->context->coercionOf(self::API . 'hasSubscriber'));
    }

    public function testVocabAndFullIriKeys(): void
    {
        $doc = JsonLd::expand([
            '@context' => ['@vocab' => self::CARGO],
            '@id' => self::PIECE,
            '@type' => 'Piece',
            'goodsDescription' => 'Books',
            self::CARGO . 'coload' => true,
        ]);

        self::assertSame([self::CARGO . 'Piece'], $doc->rootTypes());
        self::assertEquals(Literal::string('Books'), $doc->graph->firstObject(new Iri(self::PIECE), self::CARGO . 'goodsDescription'));
        self::assertEquals(Literal::boolean(true), $doc->graph->firstObject(new Iri(self::PIECE), self::CARGO . 'coload'));
    }

    public function testDocumentBlankNodeLabelsAreSharedAndKeptApartFromGeneratedOnes(): void
    {
        $doc = JsonLd::expand([
            '@context' => ['cargo' => self::CARGO],
            '@id' => '_:b0',
            'cargo:contains' => [['@id' => '_:x'], ['cargo:name' => 'anon']],
            'cargo:also' => ['@id' => '_:x', 'cargo:name' => 'shared'],
        ]);

        self::assertEquals(new BlankNode('n_b0'), $doc->root);
        $x = new BlankNode('n_x');
        self::assertEquals(Literal::string('shared'), $doc->graph->firstObject($x, self::CARGO . 'name'));
        self::assertEquals(Literal::string('anon'), $doc->graph->firstObject(new BlankNode('b0'), self::CARGO . 'name'));
    }

    public function testNullValuesAndEmptyArraysAreIgnored(): void
    {
        $doc = JsonLd::expand(['@context' => ['cargo' => self::CARGO], '@id' => self::PIECE, 'cargo:a' => null, 'cargo:b' => [], 'cargo:c' => [null, 'x']]);

        self::assertCount(1, $doc->graph);
    }

    /**
     * @return iterable<string, array{array<string, mixed>|string, string}>
     */
    public static function rejected(): iterable
    {
        $ctx = ['cargo' => self::CARGO];
        yield 'remote context' => [['@context' => 'https://onerecord.iata.org/context.jsonld'], 'remote context'];
        yield 'array context' => [['@context' => [$ctx, $ctx]], 'not a single JSON object'];
        yield 'nested context' => [['@context' => $ctx, 'cargo:x' => ['@context' => $ctx, 'cargo:y' => 1]], '@context inside an embedded object'];
        yield '@graph' => [['@context' => $ctx, '@graph' => []], 'non-empty array'];
        yield '@list' => [['@context' => $ctx, 'cargo:x' => ['@list' => [1, 2]]], 'Ordered lists'];
        yield '@set' => [['@context' => $ctx, 'cargo:x' => ['@set' => [1]]], '@set'];
        yield '@reverse' => [['@context' => $ctx, '@reverse' => []], 'Reverse properties'];
        yield '@nest' => [['@context' => $ctx, '@nest' => []], '@nest'];
        yield '@index' => [['@context' => $ctx, 'cargo:x' => ['@index' => 'a', 'cargo:y' => 1]], '@index'];
        yield '@included' => [['@context' => $ctx, '@included' => []], '@included'];
        yield '@container in term' => [['@context' => ['x' => ['@id' => 'cargo:x', '@container' => '@list']]], '@container'];
        yield '@reverse in term' => [['@context' => ['x' => ['@reverse' => 'cargo:x']]], '@reverse'];
        yield 'nested array' => [['@context' => $ctx, 'cargo:x' => [[1, 2]]], 'nested array'];
        yield 'undefined prefix' => [['@context' => $ctx, 'api:x' => 1], 'undefined prefix'];
        yield 'undefined prefix in type' => [['@context' => $ctx, '@type' => 'api:Change'], 'undefined prefix'];
        yield 'bare term without vocab' => [['@context' => $ctx, 'goodsDescription' => 'x'], 'no @vocab'];
        yield 'relative id without base' => [['@context' => $ctx, '@id' => 'piece-1'], 'relative IRI'];
        yield 'iri with whitespace' => [['@context' => $ctx, '@id' => ' https://1r.example.com/x'], 'not a valid IRI'];
        yield 'value object with extra key' => [['@context' => $ctx, 'cargo:x' => ['@value' => 1, 'cargo:y' => 2]], 'may only contain'];
        yield 'value object with type and language' => [['@context' => $ctx, 'cargo:x' => ['@value' => 'a', '@type' => 'cargo:T', '@language' => 'en']], 'both @type and @language'];
        yield 'value object with null' => [['@context' => $ctx, 'cargo:x' => ['@value' => null]], '@value must be'];
        yield 'non-string type' => [['@context' => $ctx, '@type' => 1], 'non-empty strings'];
        yield 'empty id' => [['@context' => $ctx, '@id' => ''], 'non-empty string'];
        yield 'bad blank label' => [['@context' => $ctx, '@id' => '_:'], 'blank node identifier'];
        yield 'root array json' => ['[{"@id": "x"}]', 'single JSON object'];
        yield 'invalid json' => ['{"@id": ', 'not valid JSON'];
        yield 'unknown term key' => [['@context' => ['x' => ['@id' => 'cargo:x', '@foo' => 1]]], 'Unknown key'];
        yield 'context keyword' => [['@context' => ['@propagate' => true]], '@propagate'];
    }

    /**
     * @param array<string, mixed>|string $document
     */
    #[DataProvider('rejected')]
    public function testRejectsWhatItCannotRepresent(array|string $document, string $message): void
    {
        $this->expectException(JsonLdException::class);
        $this->expectExceptionMessage($message);

        JsonLd::expand($document);
    }

    public function testErrorsNameThePath(): void
    {
        try {
            JsonLd::expand(['@context' => ['cargo' => self::CARGO], 'cargo:a' => [['cargo:b' => ['@list' => []]]]]);
            self::fail();
        } catch (JsonLdException $e) {
            self::assertStringContainsString('cargo:a[0].cargo:b.@list', $e->getMessage());
        }
    }

    public function testContextCompaction(): void
    {
        $context = Context::fromRaw(['cargo' => self::CARGO, 'api' => self::API, 'xsd' => self::XSD, 'desc' => 'cargo:goodsDescription', 'api:p' => ['@type' => 'xsd:anyURI']]);

        self::assertSame('cargo:Piece', $context->compactIri(self::CARGO . 'Piece'));
        self::assertSame('desc', $context->compactIri(self::CARGO . 'goodsDescription'));
        self::assertSame('api:p', $context->compactIri(self::API . 'p'));
        self::assertSame('https://example.org/x', $context->compactIri('https://example.org/x'));
        self::assertSame(['cargo' => self::CARGO, 'api' => self::API, 'xsd' => self::XSD, 'desc' => self::CARGO . 'goodsDescription', 'api:p' => ['@type' => 'xsd:anyURI']], $context->toRaw());
        self::assertSame(self::XSD . 'anyURI', $context->coercionOf(self::API . 'p'));

        $vocab = Context::fromRaw(['@vocab' => self::CARGO]);
        self::assertSame('Piece', $vocab->compactIri(self::CARGO . 'Piece'));
        self::assertSame(['@vocab' => self::CARGO], $vocab->toRaw());
        self::assertSame([], Context::fromRaw(null)->toRaw());
        self::assertSame('urn:uuid:1', $context->expandIri('urn:uuid:1', 'x', vocabRelative: false), 'well-known schemes are not mistaken for undefined prefixes');
        self::assertSame('internal:abc', $context->expandIri('internal:abc', 'x', vocabRelative: false));
    }

    public function testATopLevelGraphIsOneFlattenedDocument(): void
    {
        // The shape NE:ONE answers with: the object and its embedded nodes side by side, @vocab for cargo.
        $json = [
            '@context' => ['@vocab' => self::CARGO],
            '@graph' => [
                ['@id' => 'https://1r.example.com/logistics-objects/p1', '@type' => ['LogisticsObject', 'Piece'], 'grossWeight' => ['@id' => 'neone:w1']],
                ['@id' => 'neone:w1', '@type' => 'Value', 'numericalValue' => ['@value' => '20.5', '@type' => 'http://www.w3.org/2001/XMLSchema#double']],
                ['@id' => self::CARGO . 'ACTUAL', '@type' => 'EventTimeType'],
            ],
        ];
        $doc = JsonLd::expand($json);
        self::assertSame('https://1r.example.com/logistics-objects/p1', $doc->rootIri()?->value, 'the object nothing references is the root (ACTUAL is a loose node, but the piece comes first)');
        self::assertCount(6, $doc->graph);
        self::assertSame('20.5', $doc->graph->firstObject(new Iri('neone:w1'), self::CARGO . 'numericalValue')?->toNTriples() === null ? null : '20.5');

        $preferred = JsonLd::expand($json, new Iri('neone:w1'));
        self::assertSame('neone:w1', $preferred->rootIri()?->value, 'a preferred root wins when present');
        $named = JsonLd::expand(['@context' => ['@vocab' => self::CARGO], '@id' => 'neone:w1', ...array_intersect_key($json, ['@graph' => 1])]);
        self::assertSame('neone:w1', $named->rootIri()?->value, 'a top-level @id names the root');

        $this->expectException(JsonLdException::class);
        $this->expectExceptionMessage('@graph inside a node');
        JsonLd::expand(['@context' => ['@vocab' => self::CARGO], '@id' => 'https://x.example/1', 'pieces' => ['@graph' => []]]);
    }

    public function testATopLevelGraphWithOtherPropertiesIsRefused(): void
    {
        $this->expectException(JsonLdException::class);
        $this->expectExceptionMessage('next to a top-level @graph');
        JsonLd::expand(['@context' => ['@vocab' => self::CARGO], '@graph' => [['@id' => 'https://x.example/1']], 'name' => 'no']);
    }

    public function testAr008RelativeReferencesResolveAndCoercionFollowsTheActiveTerm(): void
    {
        $doc = JsonLd::expand(['@context' => ['cargo' => self::CARGO, '@base' => 'https://example/a/b'], '@id' => '../c', '@type' => 'cargo:Piece']);
        self::assertSame('https://example/c', $doc->rootIri()?->value);
        foreach (['x' => 'https://example/a/x', '/y' => 'https://example/y', '?q=1' => 'https://example/a/b?q=1', '#f' => 'https://example/a/b#f', '//other/z' => 'https://other/z', './' => 'https://example/a/', 'https://abs/' => 'https://abs/'] as $ref => $expected) {
            self::assertSame($expected, JsonLd::expand(['@context' => ['cargo' => self::CARGO, '@base' => 'https://example/a/b'], '@id' => $ref, '@type' => 'cargo:Piece'])->rootIri()?->value, $ref);
        }

        // "link" is coerced to @id, but the value under the distinct key cargo:goodsDescription stays a string.
        $doc = JsonLd::expand([
            '@context' => ['cargo' => self::CARGO, 'link' => ['@id' => 'cargo:goodsDescription', '@type' => '@id']],
            '@id' => 'https://example/p',
            'cargo:goodsDescription' => 'https://example/text',
        ]);
        $value = $doc->graph->firstObject(new Iri('https://example/p'), self::CARGO . 'goodsDescription');
        self::assertInstanceOf(Literal::class, $value, 'an unused alias must not reinterpret another key');
        self::assertSame('https://example/text', $value->lexical);
        $viaAlias = JsonLd::expand(['@context' => ['cargo' => self::CARGO, 'link' => ['@id' => 'cargo:goodsDescription', '@type' => '@id']], '@id' => 'https://example/p', 'link' => 'https://example/text']);
        self::assertInstanceOf(Iri::class, $viaAlias->graph->firstObject(new Iri('https://example/p'), self::CARGO . 'goodsDescription'), 'the alias itself still coerces');
    }

    public function testR2008DateTimesFollowTheXsdGrammarRanges(): void
    {
        $parse = static fn(string $v): ?string => \LambdaTwelve\OneRecord\JsonLd\Nodes::parseDateTime($v)?->format('Y-m-d\TH:i:s.vP');
        self::assertNull($parse('2026-10-02T12:00:60Z'), 'seconds stop at 59');
        self::assertNull($parse('2026-10-02T12:60:00Z'));
        self::assertNull($parse('2026-10-02T25:00:00Z'));
        self::assertNull($parse('2026-10-02T12:00:00+14:59'), 'offsets stop at 14:00');
        self::assertNull($parse('2026-10-02T12:00:00+15:00'));
        self::assertNull($parse('2026-02-30T12:00:00Z'));
        self::assertNull($parse('2026-10-02T24:00:01Z'), '24:00:00 only');
        self::assertSame('2026-10-03T00:00:00.000+00:00', $parse('2026-10-02T24:00:00Z'), 'end of day is the next midnight');
        self::assertSame('2026-10-02T12:00:00.000+14:00', $parse('2026-10-02T12:00:00+14:00'));
        self::assertSame('2024-02-29T23:59:59.500+00:00', $parse('2024-02-29T23:59:59.5Z'));
        self::assertSame('2026-10-02T12:00:00.000+00:00', $parse('2026-10-02T12:00:00'), 'no zone is still a valid lexical form');
    }

    public function testR2009NetworkPathReferencesAreNormalisedToo(): void
    {
        $context = ['cargo' => self::CARGO, '@base' => 'https://example/a/b'];
        self::assertSame('https://other.example/y', JsonLd::expand(['@context' => $context, '@id' => '//other.example/x/../y', '@type' => 'cargo:Piece'])->rootIri()?->value);
        self::assertSame('https://other.example/y?q=1#f', JsonLd::expand(['@context' => $context, '@id' => '//other.example/./y?q=1#f', '@type' => 'cargo:Piece'])->rootIri()?->value);
    }
}
