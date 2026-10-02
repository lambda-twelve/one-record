<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\JsonLd;

use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\Json;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\Writer;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Writer::class)]
#[CoversClass(JsonLd::class)]
#[UsesClass(Context::class)]
#[UsesClass(Json::class)]
#[UsesClass(Comparer::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Diff::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\Expander::class)]
#[UsesClass(\LambdaTwelve\OneRecord\JsonLd\ExpandedDocument::class)]
#[UsesClass(Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
#[UsesClass(Triple::class)]
final class WriterTest extends TestCase
{
    private const string CARGO = 'https://onerecord.iata.org/ns/cargo#';
    private const string XSD = 'http://www.w3.org/2001/XMLSchema#';

    public function testWritesSortedCompactedJsonWithEmbeddedAndReferencedNodes(): void
    {
        $piece = new Iri('https://1r.example.com/logistics-objects/piece-1');
        $shipment = new Iri('https://1r.example.com/logistics-objects/shipment-1');
        $value = new BlankNode('v');
        $graph = new Graph([
            new Triple($piece, new Iri(Graph::RDF_TYPE), new Iri(self::CARGO . 'Piece')),
            new Triple($piece, new Iri(self::CARGO . 'goodsDescription'), Literal::string('Books')),
            new Triple($piece, new Iri(self::CARGO . 'coload'), Literal::boolean(false)),
            new Triple($piece, new Iri(self::CARGO . 'grossWeight'), $value),
            new Triple($value, new Iri(Graph::RDF_TYPE), new Iri(self::CARGO . 'Value')),
            new Triple($value, new Iri(self::CARGO . 'numericalValue'), Literal::double(20.5)),
            new Triple($value, new Iri(self::CARGO . 'unit'), new Iri('https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM')),
            new Triple($piece, new Iri(self::CARGO . 'ofShipment'), $shipment),
            new Triple($shipment, new Iri(Graph::RDF_TYPE), new Iri(self::CARGO . 'Shipment')),
            new Triple($piece, new Iri(self::CARGO . 'eventDate'), new Literal('2023-04-01T10:38:01.000Z', self::XSD . 'dateTime')),
            new Triple($piece, new Iri(self::CARGO . 'tags'), Literal::string('b')),
            new Triple($piece, new Iri(self::CARGO . 'tags'), Literal::string('a')),
        ]);

        $written = JsonLd::compact($graph, $piece);

        self::assertSame([
            '@context' => ['cargo' => self::CARGO, 'api' => 'https://onerecord.iata.org/ns/api#'],
            '@id' => $piece->value,
            '@type' => 'cargo:Piece',
            'cargo:coload' => false,
            'cargo:eventDate' => ['@type' => self::XSD . 'dateTime', '@value' => '2023-04-01T10:38:01.000Z'],
            'cargo:goodsDescription' => 'Books',
            'cargo:grossWeight' => [
                '@type' => 'cargo:Value',
                'cargo:numericalValue' => 20.5,
                'cargo:unit' => ['@id' => 'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM'],
            ],
            'cargo:ofShipment' => ['@id' => $shipment->value, '@type' => 'cargo:Shipment'],
            'cargo:tags' => ['a', 'b'],
        ], $written);
        self::assertStringContainsString('"cargo:numericalValue": 20.5', JsonLd::compactToJson($graph, $piece));
    }

    public function testSharedBlankNodesKeepALabelAndCyclesBecomeReferences(): void
    {
        $a = new Iri('https://1r.example.com/a');
        $b = new Iri('https://1r.example.com/b');
        $shared = new BlankNode('s');
        $graph = new Graph([
            new Triple($a, new Iri(self::CARGO . 'linksTo'), $b),
            new Triple($b, new Iri(self::CARGO . 'linksTo'), $a),
            new Triple($a, new Iri(self::CARGO . 'x'), $shared),
            new Triple($b, new Iri(self::CARGO . 'x'), $shared),
            new Triple($shared, new Iri(self::CARGO . 'name'), Literal::string('shared')),
        ]);

        $written = (new Writer())->write($graph, $a, Context::oneRecord(), includeContext: false);

        self::assertSame([
            '@id' => $a->value,
            'cargo:linksTo' => [
                '@id' => $b->value,
                'cargo:linksTo' => ['@id' => $a->value],
                'cargo:x' => ['@id' => '_:s', 'cargo:name' => 'shared'],
            ],
            'cargo:x' => ['@id' => '_:s'],
        ], $written);
        self::assertTrue((new Comparer())->isomorphic($graph, JsonLd::expand(['@context' => Context::oneRecord()->toRaw(), ...$written])->graph));
    }

    public function testLiteralsThatCannotRoundTripNativelyStayTyped(): void
    {
        $s = new Iri('https://1r.example.com/a');
        $graph = new Graph([
            new Triple($s, new Iri(self::CARGO . 'a'), new Literal('20.0', self::XSD . 'double')),
            new Triple($s, new Iri(self::CARGO . 'b'), Literal::double(20.0)),
            new Triple($s, new Iri(self::CARGO . 'c'), new Literal('007', self::XSD . 'integer')),
            new Triple($s, new Iri(self::CARGO . 'd'), new Literal('1', self::XSD . 'boolean')),
            new Triple($s, new Iri(self::CARGO . 'e'), new Literal('x', null, 'de')),
            new Triple($s, new Iri(self::CARGO . 'f'), Literal::integer(-3)),
        ]);
        $written = (new Writer())->write($graph, $s, new Context(['cargo' => self::CARGO, 'xsd' => self::XSD]), includeContext: false);

        self::assertSame(['@type' => 'xsd:double', '@value' => '20.0'], $written['cargo:a']);
        self::assertSame(20.0, $written['cargo:b']);
        self::assertSame(['@type' => 'xsd:integer', '@value' => '007'], $written['cargo:c']);
        self::assertSame(['@type' => 'xsd:boolean', '@value' => '1'], $written['cargo:d']);
        self::assertSame(['@language' => 'de', '@value' => 'x'], $written['cargo:e']);
        self::assertSame(-3, $written['cargo:f']);

        $typed = (new Writer(nativeLiterals: false))->write($graph, $s, new Context(['cargo' => self::CARGO, 'xsd' => self::XSD]), includeContext: false);
        self::assertSame(['@type' => 'xsd:integer', '@value' => '-3'], $typed['cargo:f']);
    }

    public function testHonoursContextCoercionsAndDefaultLanguage(): void
    {
        $s = new Iri('https://1r.example.com/a');
        $context = Context::fromRaw(['api' => 'https://onerecord.iata.org/ns/api#', 'xsd' => self::XSD, '@language' => 'en-US', 'api:p' => ['@type' => 'xsd:anyURI'], 'api:ref' => ['@type' => '@id']]);
        $graph = new Graph([
            new Triple($s, new Iri('https://onerecord.iata.org/ns/api#p'), new Literal(self::CARGO . 'grossWeight', self::XSD . 'anyURI')),
            new Triple($s, new Iri('https://onerecord.iata.org/ns/api#ref'), new Iri('https://1r.example.com/b')),
            new Triple($s, new Iri('https://onerecord.iata.org/ns/api#hasTitle'), new Literal('Not found', null, 'en-US')),
            new Triple($s, new Iri('https://onerecord.iata.org/ns/api#hasCode'), Literal::string('404')),
        ]);
        $written = (new Writer())->write($graph, $s, $context, includeContext: false);

        self::assertSame(self::CARGO . 'grossWeight', $written['api:p']);
        self::assertSame('https://1r.example.com/b', $written['api:ref']);
        self::assertSame('Not found', $written['api:hasTitle'], 'a literal in the default language is a plain string');
        self::assertSame(['@type' => 'xsd:string', '@value' => '404'], $written['api:hasCode'], 'an untagged string must not pick up the default language on re-read');

        $reexpanded = JsonLd::expand(['@context' => $context->toRaw(), ...$written]);
        self::assertTrue((new Comparer())->isomorphic($graph, $reexpanded->graph), (new Comparer())->compare($graph, $reexpanded->graph)->describe());
    }

    public function testBlankRootHasNoIdAndEmptyContextIsOmitted(): void
    {
        $graph = new Graph([new Triple(new BlankNode('r'), new Iri(self::CARGO . 'name'), Literal::string('x'))]);

        self::assertSame(['https://onerecord.iata.org/ns/cargo#name' => 'x'], (new Writer())->write($graph, new BlankNode('r'), new Context()));
    }

    public function testAr007WhatTheWriterEmitsReadsBackAsTheSameGraph(): void
    {
        $comparer = new Comparer();
        // A typed alias for a cargo property.
        $aliased = ['@context' => ['cargo' => 'https://onerecord.iata.org/ns/cargo#', 'name' => ['@id' => 'cargo:goodsDescription', '@type' => 'http://www.w3.org/2001/XMLSchema#string']], '@id' => 'https://example/p', 'name' => 'Books'];
        $doc = JsonLd::expand($aliased);
        $written = (new Writer())->write($doc->graph, $doc->root, $doc->context);
        $context = $written['@context'];
        self::assertIsArray($context);
        self::assertIsArray($context['name']);
        self::assertSame('cargo:goodsDescription', $context['name']['@id'] ?? null, 'the definition keeps its @id');
        self::assertTrue($comparer->isomorphic($doc->graph, JsonLd::expand($written)->graph));

        // @vocab for cargo with an absolute root: the @id must not become the relative "Piece".
        $vocab = ['@context' => ['@vocab' => 'https://onerecord.iata.org/ns/cargo#'], '@id' => 'https://onerecord.iata.org/ns/cargo#Piece', 'goodsDescription' => 'Books'];
        $doc = JsonLd::expand($vocab);
        $written = (new Writer())->write($doc->graph, $doc->root, $doc->context);
        self::assertSame('https://onerecord.iata.org/ns/cargo#Piece', $written['@id']);
        self::assertTrue($comparer->isomorphic($doc->graph, JsonLd::expand($written)->graph));
    }

    public function testAr009AReferencedBlankRootKeepsItsIdentity(): void
    {
        $comparer = new Comparer();
        foreach ([
            ['@id' => '_:x', 'https://example/p' => ['@id' => '_:x']],
            ['@id' => '_:x', 'https://example/p' => ['@id' => '_:y', 'https://example/p' => ['@id' => '_:x']]],
        ] as $json) {
            $doc = JsonLd::expand($json);
            $written = (new Writer())->write($doc->graph, $doc->root, $doc->context);
            self::assertArrayHasKey('@id', $written, 'a root that is referenced needs a label');
            self::assertTrue($comparer->isomorphic($doc->graph, JsonLd::expand($written)->graph), json_encode($written, JSON_THROW_ON_ERROR));
        }
        // An unreferenced blank root still needs no label.
        $doc = JsonLd::expand(['https://example/p' => 'x']);
        self::assertArrayNotHasKey('@id', (new Writer())->write($doc->graph, $doc->root, $doc->context));
    }
}
