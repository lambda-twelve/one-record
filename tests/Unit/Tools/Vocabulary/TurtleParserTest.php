<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle\TurtleParser;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle\TurtleSyntaxError;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TurtleParser::class)]
#[CoversClass(TurtleSyntaxError::class)]
#[UsesClass(Graph::class)]
#[UsesClass(Iri::class)]
#[UsesClass(BlankNode::class)]
#[UsesClass(Literal::class)]
final class TurtleParserTest extends TestCase
{
    private const string PREFIXES = <<<'TTL'
        @prefix : <https://example.org/ns#> .
        @prefix ex: <https://example.org/other/> .
        @prefix xsd: <http://www.w3.org/2001/XMLSchema#> .
        @prefix rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> .
        @base <https://example.org/base/> .

        TTL;

    private function parse(string $body): Graph
    {
        return (new TurtleParser())->parse(self::PREFIXES . $body);
    }

    public function testPrefixedNamesFullIrisAndRelativeIris(): void
    {
        $graph = $this->parse(':a ex:p <https://example.org/o> . <rel> rdf:type :T .');

        self::assertCount(2, $graph);
        self::assertTrue($graph->has(new \LambdaTwelve\OneRecord\Rdf\Triple(new Iri('https://example.org/ns#a'), new Iri('https://example.org/other/p'), new Iri('https://example.org/o'))));
        self::assertEquals([new Iri('https://example.org/ns#T')], $graph->typesOf(new Iri('https://example.org/base/rel')));
    }

    public function testPredicateObjectListsWithTrailingSemicolonAndComments(): void
    {
        $graph = $this->parse(<<<'TTL'
            # a comment line
            :s :p1 :o1 , :o2 ; # trailing comment
               :p2 :o3 ;
               .
            TTL);

        self::assertCount(3, $graph);
        self::assertCount(2, $graph->objects(new Iri('https://example.org/ns#s'), 'https://example.org/ns#p1'));
    }

    public function testLiterals(): void
    {
        $graph = $this->parse(<<<'TTL'
            :s :plain "text" ;
               :lang "Kilogram"@en ;
               :typed "3.2"^^xsd:string ;
               :bool true ;
               :int 42 ;
               :neg -7 ;
               :dec 1.5 ;
               :dbl 2.0E1 ;
               :esc "say \"hi\"\tnowé" ;
               :long """multi
            line "quoted" text""" ;
               :single 'single' .
            TTL);
        $s = new Iri('https://example.org/ns#s');
        $get = static function (string $p) use ($graph, $s): Literal {
            $object = $graph->firstObject($s, 'https://example.org/ns#' . $p);
            self::assertInstanceOf(Literal::class, $object, $p);

            return $object;
        };

        self::assertEquals(Literal::string('text'), $get('plain'));
        self::assertEquals(new Literal('Kilogram', null, 'en'), $get('lang'));
        self::assertEquals(new Literal('3.2', Literal::XSD_STRING), $get('typed'));
        self::assertEquals(Literal::boolean(true), $get('bool'));
        self::assertEquals(new Literal('42', Literal::XSD_INTEGER), $get('int'));
        self::assertEquals(new Literal('-7', Literal::XSD_INTEGER), $get('neg'));
        self::assertEquals(new Literal('1.5', Literal::XSD_DECIMAL), $get('dec'));
        self::assertEquals(new Literal('2.0E1', Literal::XSD_DOUBLE), $get('dbl'));
        self::assertSame("say \"hi\"\tnowé", $get('esc')->lexical);
        self::assertSame("multi\nline \"quoted\" text", $get('long')->lexical);
        self::assertSame('single', $get('single')->lexical);
    }

    public function testBlankNodePropertyListsAndCollections(): void
    {
        $graph = $this->parse(<<<'TTL'
            :C rdf:type :Class ;
               :sub [ rdf:type :Restriction ; :on :p ; :all :V ] ;
               :oneOf ( :a :b "c" ) ;
               :empty ( ) .
            [ :standalone true ] .
            TTL);
        $c = new Iri('https://example.org/ns#C');

        $restriction = $graph->firstObject($c, 'https://example.org/ns#sub');
        self::assertInstanceOf(BlankNode::class, $restriction);
        self::assertEquals([new Iri('https://example.org/ns#Restriction')], $graph->typesOf($restriction));
        self::assertEquals(new Iri('https://example.org/ns#p'), $graph->firstObject($restriction, 'https://example.org/ns#on'));

        $list = $graph->firstObject($c, 'https://example.org/ns#oneOf');
        self::assertNotNull($list);
        self::assertEquals([new Iri('https://example.org/ns#a'), new Iri('https://example.org/ns#b'), Literal::string('c')], $graph->listItems($list));

        self::assertEquals(new Iri(Graph::RDF_NIL), $graph->firstObject($c, 'https://example.org/ns#empty'));
        self::assertCount(1, array_filter($graph->subjects(), static fn($s): bool => $s instanceof BlankNode && $graph->firstObject($s, 'https://example.org/ns#standalone') !== null));
    }

    public function testLabelledBlankNodesAndTheAKeyword(): void
    {
        $graph = $this->parse('_:x a :T ; :p _:x .');
        $x = new BlankNode('b_x');

        self::assertEquals([new Iri('https://example.org/ns#T')], $graph->typesOf($x));
        self::assertEquals($x, $graph->firstObject($x, 'https://example.org/ns#p'));
    }

    public function testLocalNamesMayEndAtAStatementDot(): void
    {
        $graph = $this->parse(":s :p :o.\n:s2 :p :o2 .");

        self::assertCount(2, $graph);
        self::assertEquals(new Iri('https://example.org/ns#o'), $graph->firstObject(new Iri('https://example.org/ns#s'), 'https://example.org/ns#p'));
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function syntaxErrors(): iterable
    {
        yield 'undeclared prefix' => [':s nope:p :o .', 'Undeclared prefix'];
        yield 'unterminated string' => [':s :p "open .', 'Unterminated string'];
        yield 'unterminated iri' => [':s :p <https://x .', 'Unterminated IRI'];
        yield 'missing dot' => [':s :p :o', 'Expected "."'];
        yield 'literal subject' => ['"x" :p :o .', 'Expected a prefixed name'];
        yield 'unknown escape' => [':s :p "\q" .', 'Unknown escape'];
        yield 'relative iri without base' => ["@prefix : <https://e/> .\n:s :p <rel> .", 'without a base'];
        yield 'newline in short string' => [":s :p \"a\nb\" .", 'Newline inside'];
    }

    #[DataProvider('syntaxErrors')]
    public function testRejectsMalformedInput(string $body, string $message): void
    {
        $this->expectException(TurtleSyntaxError::class);
        $this->expectExceptionMessage($message);

        $input = str_starts_with($body, '@prefix') ? $body : self::PREFIXES . $body;
        (new TurtleParser())->parse($input);
    }

    public function testErrorsReportLineAndColumn(): void
    {
        try {
            $this->parse(":s :p :o .\n:s :p");
            self::fail('expected a syntax error');
        } catch (TurtleSyntaxError $e) {
            self::assertStringContainsString('line 7', $e->getMessage());
        }
    }

    public function testExposesDeclaredPrefixes(): void
    {
        $parser = new TurtleParser();
        $parser->parse(self::PREFIXES);

        self::assertSame('https://example.org/ns#', $parser->prefixes()['']);
        self::assertSame('https://example.org/other/', $parser->prefixes()['ex']);
    }
}
