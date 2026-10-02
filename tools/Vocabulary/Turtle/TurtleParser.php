<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;

/**
 * A Turtle reader for exactly what IATA's ontology files use: prefix and base
 * directives, predicate-object lists, blank-node property lists, RDF
 * collections, typed and language-tagged strings (short and long form),
 * integers and booleans, comments. Anything else is a syntax error on
 * purpose: this is a generator-time tool, and a silent misparse would end up
 * as a wrong constant in the shipped vocabulary.
 *
 * Collections are expanded to rdf:first / rdf:rest chains, so OWL constructs
 * such as owl:oneOf and owl:unionOf read as ordinary triples.
 */
final class TurtleParser
{
    private string $input = '';
    private int $pos = 0;
    private int $length = 0;
    private int $line = 1;
    private int $lineStart = 0;
    private ?string $base = null;
    /** @var array<string, string> */
    private array $prefixes = [];
    private int $blankNodeCounter = 0;
    private Graph $graph;

    public function parse(string $turtle): Graph
    {
        $this->input = $turtle;
        $this->pos = 0;
        $this->length = \strlen($turtle);
        $this->line = 1;
        $this->lineStart = 0;
        $this->base = null;
        $this->prefixes = [];
        $this->blankNodeCounter = 0;
        $this->graph = new Graph();

        $this->skipWhitespace();
        while ($this->pos < $this->length) {
            if ($this->peekString('@prefix')) {
                $this->parsePrefix();
            } elseif ($this->peekString('@base')) {
                $this->parseBase();
            } else {
                $this->parseTriples();
                $this->expect('.');
            }
            $this->skipWhitespace();
        }

        return $this->graph;
    }

    /**
     * @return array<string, string>
     */
    public function prefixes(): array
    {
        return $this->prefixes;
    }

    private function parsePrefix(): void
    {
        $this->pos += \strlen('@prefix');
        $this->skipWhitespace();
        $prefix = $this->readPrefixName();
        $this->skipWhitespace();
        $iri = $this->readIriRef();
        $this->skipWhitespace();
        $this->expect('.');
        $this->prefixes[$prefix] = $iri;
    }

    private function parseBase(): void
    {
        $this->pos += \strlen('@base');
        $this->skipWhitespace();
        $this->base = $this->readIriRef();
        $this->skipWhitespace();
        $this->expect('.');
    }

    private function parseTriples(): void
    {
        if ($this->peek() === '[') {
            $subject = $this->parseBlankNodePropertyList();
            $this->skipWhitespace();
            if ($this->peek() !== '.') {
                $this->parsePredicateObjectList($subject);
            }

            return;
        }
        $subject = $this->parseSubject();
        $this->skipWhitespace();
        $this->parsePredicateObjectList($subject);
    }

    private function parseSubject(): Iri|BlankNode
    {
        $term = $this->parseTerm(allowLiteral: false);
        if (!$term instanceof Iri && !$term instanceof BlankNode) {
            throw $this->error('A subject must be an IRI or a blank node');
        }

        return $term;
    }

    private function parsePredicateObjectList(Iri|BlankNode $subject): void
    {
        while (true) {
            $predicate = $this->parsePredicate();
            $this->skipWhitespace();
            $this->parseObjectList($subject, $predicate);
            $this->skipWhitespace();
            if ($this->peek() !== ';') {
                return;
            }
            // Turtle allows a trailing ";" before the final "." or "]".
            while ($this->peek() === ';') {
                $this->pos++;
                $this->skipWhitespace();
            }
            if ($this->peek() === '.' || $this->peek() === ']') {
                return;
            }
        }
    }

    private function parsePredicate(): Iri
    {
        if ($this->peek() === 'a' && $this->isDelimiter($this->peek(1))) {
            $this->pos++;

            return new Iri(Graph::RDF_TYPE);
        }
        $term = $this->parseTerm(allowLiteral: false);
        if (!$term instanceof Iri) {
            throw $this->error('A predicate must be an IRI');
        }

        return $term;
    }

    private function parseObjectList(Iri|BlankNode $subject, Iri $predicate): void
    {
        while (true) {
            $object = $this->parseObject();
            $this->graph->add(new Triple($subject, $predicate, $object));
            $this->skipWhitespace();
            if ($this->peek() !== ',') {
                return;
            }
            $this->pos++;
            $this->skipWhitespace();
        }
    }

    private function parseObject(): Term
    {
        $char = $this->peek();
        if ($char === '[') {
            return $this->parseBlankNodePropertyList();
        }
        if ($char === '(') {
            return $this->parseCollection();
        }

        return $this->parseTerm(allowLiteral: true);
    }

    private function parseBlankNodePropertyList(): BlankNode
    {
        $this->expect('[');
        $node = $this->freshBlankNode();
        $this->skipWhitespace();
        if ($this->peek() !== ']') {
            $this->parsePredicateObjectList($node);
            $this->skipWhitespace();
        }
        $this->expect(']');

        return $node;
    }

    private function parseCollection(): Iri|BlankNode
    {
        $this->expect('(');
        $this->skipWhitespace();
        $items = [];
        while ($this->peek() !== ')') {
            if ($this->pos >= $this->length) {
                throw $this->error('Unterminated collection');
            }
            $items[] = $this->parseObject();
            $this->skipWhitespace();
        }
        $this->expect(')');

        if ($items === []) {
            return new Iri(Graph::RDF_NIL);
        }
        $head = $this->freshBlankNode();
        $node = $head;
        foreach ($items as $index => $item) {
            $this->graph->add(new Triple($node, new Iri(Graph::RDF_FIRST), $item));
            $rest = $index === \count($items) - 1 ? new Iri(Graph::RDF_NIL) : $this->freshBlankNode();
            $this->graph->add(new Triple($node, new Iri(Graph::RDF_REST), $rest));
            if ($rest instanceof BlankNode) {
                $node = $rest;
            }
        }

        return $head;
    }

    private function parseTerm(bool $allowLiteral): Term
    {
        $char = $this->peek();
        if ($char === '<') {
            return new Iri($this->resolve($this->readIriRef()));
        }
        if ($char === '_' && $this->peek(1) === ':') {
            $this->pos += 2;
            $label = $this->readWhile(static fn(string $c): bool => ctype_alnum($c) || $c === '_' || $c === '-' || $c === '.');

            return new BlankNode('b_' . rtrim($label, '.'));
        }
        if ($allowLiteral) {
            if ($char === '"' || $char === "'") {
                return $this->parseStringLiteral();
            }
            if ($this->peekString('true') && $this->isDelimiter($this->peek(4))) {
                $this->pos += 4;

                return Literal::boolean(true);
            }
            if ($this->peekString('false') && $this->isDelimiter($this->peek(5))) {
                $this->pos += 5;

                return Literal::boolean(false);
            }
            if ($char !== '' && (ctype_digit($char) || $char === '-' || $char === '+')) {
                return $this->parseNumericLiteral();
            }
        }

        return $this->parsePrefixedName();
    }

    private function parsePrefixedName(): Iri
    {
        $start = $this->pos;
        $prefix = $this->readWhile(static fn(string $c): bool => ctype_alnum($c) || $c === '_' || $c === '-' || $c === '.');
        if ($this->peek() !== ':') {
            throw $this->error(\sprintf('Expected a prefixed name, found "%s"', substr($this->input, $start, 20)));
        }
        $this->pos++;
        $local = $this->readWhile(static fn(string $c): bool => ctype_alnum($c) || $c === '_' || $c === '-' || $c === '.' || $c === '%' || \ord($c) >= 0x80);
        // A trailing dot ends the statement rather than belonging to the name.
        while (str_ends_with($local, '.')) {
            $local = substr($local, 0, -1);
            $this->pos--;
        }
        if (!isset($this->prefixes[$prefix])) {
            throw $this->error(\sprintf('Undeclared prefix "%s:"', $prefix));
        }

        return new Iri($this->prefixes[$prefix] . $local);
    }

    private function parseStringLiteral(): Literal
    {
        $quote = $this->peek();
        $long = $this->peekString(str_repeat($quote, 3));
        $this->pos += $long ? 3 : 1;
        $value = '';
        while (true) {
            if ($this->pos >= $this->length) {
                throw $this->error('Unterminated string literal');
            }
            $char = $this->input[$this->pos];
            if ($char === '\\') {
                $value .= $this->readEscape();
                continue;
            }
            if ($long) {
                if ($this->peekString(str_repeat($quote, 3))) {
                    $this->pos += 3;
                    break;
                }
            } elseif ($char === $quote) {
                $this->pos++;
                break;
            } elseif ($char === "\n") {
                throw $this->error('Newline inside a single-line string literal');
            }
            if ($char === "\n") {
                $this->line++;
                $this->lineStart = $this->pos + 1;
            }
            $value .= $char;
            $this->pos++;
        }

        if ($this->peek() === '@') {
            $this->pos++;
            $language = $this->readWhile(static fn(string $c): bool => ctype_alnum($c) || $c === '-');
            if ($language === '') {
                throw $this->error('Empty language tag');
            }

            return new Literal($value, null, $language);
        }
        if ($this->peekString('^^')) {
            $this->pos += 2;
            $datatype = $this->parseTerm(allowLiteral: false);
            if (!$datatype instanceof Iri) {
                throw $this->error('A datatype must be an IRI');
            }

            return new Literal($value, $datatype->value);
        }

        return Literal::string($value);
    }

    private function readEscape(): string
    {
        $this->pos++; // the backslash
        $char = $this->peek();
        $this->pos++;

        return match ($char) {
            't' => "\t",
            'n' => "\n",
            'r' => "\r",
            'b' => "\x08",
            'f' => "\f",
            '"' => '"',
            "'" => "'",
            '\\' => '\\',
            'u' => $this->readUnicodeEscape(4),
            'U' => $this->readUnicodeEscape(8),
            default => throw $this->error(\sprintf('Unknown escape sequence "\\%s"', $char)),
        };
    }

    private function readUnicodeEscape(int $digits): string
    {
        $hex = substr($this->input, $this->pos, $digits);
        if (\strlen($hex) !== $digits || !ctype_xdigit($hex)) {
            throw $this->error('Invalid unicode escape');
        }
        $this->pos += $digits;

        $char = mb_chr((int) hexdec($hex), 'UTF-8');

        return $char === false ? throw $this->error('Invalid unicode code point') : $char;
    }

    private function parseNumericLiteral(): Literal
    {
        $text = $this->readWhile(static fn(string $c): bool => ctype_digit($c) || $c === '-' || $c === '+' || $c === '.' || $c === 'e' || $c === 'E');
        // A dot directly before whitespace terminates the statement, not the number.
        while (str_ends_with($text, '.')) {
            $text = substr($text, 0, -1);
            $this->pos--;
        }
        if (preg_match('/^[+-]?\d+$/', $text) === 1) {
            return new Literal($text, Literal::XSD_INTEGER);
        }
        if (preg_match('/^[+-]?\d*\.\d+$/', $text) === 1) {
            return new Literal($text, Literal::XSD_DECIMAL);
        }
        if (preg_match('/^[+-]?(\d+\.?\d*|\.\d+)[eE][+-]?\d+$/', $text) === 1) {
            return new Literal($text, Literal::XSD_DOUBLE);
        }

        throw $this->error(\sprintf('Malformed numeric literal "%s"', $text));
    }

    private function readIriRef(): string
    {
        $this->expect('<');
        $end = strpos($this->input, '>', $this->pos);
        if ($end === false) {
            throw $this->error('Unterminated IRI');
        }
        $iri = substr($this->input, $this->pos, $end - $this->pos);
        $this->pos = $end + 1;

        return $iri;
    }

    private function readPrefixName(): string
    {
        $name = $this->readWhile(static fn(string $c): bool => ctype_alnum($c) || $c === '_' || $c === '-' || $c === '.');
        $this->expect(':');

        return $name;
    }

    private function resolve(string $iri): string
    {
        if ($iri === '' || str_contains($iri, ':')) {
            return $iri;
        }
        if ($this->base === null) {
            throw $this->error(\sprintf('Relative IRI "%s" without a base', $iri));
        }

        return $this->base . $iri;
    }

    private function freshBlankNode(): BlankNode
    {
        return new BlankNode('g' . ++$this->blankNodeCounter);
    }

    /**
     * @param callable(string): bool $accept
     */
    private function readWhile(callable $accept): string
    {
        $start = $this->pos;
        while ($this->pos < $this->length && $accept($this->input[$this->pos])) {
            $this->pos++;
        }

        return substr($this->input, $start, $this->pos - $start);
    }

    private function skipWhitespace(): void
    {
        while ($this->pos < $this->length) {
            $char = $this->input[$this->pos];
            if ($char === "\n") {
                $this->line++;
                $this->pos++;
                $this->lineStart = $this->pos;
            } elseif ($char === ' ' || $char === "\t" || $char === "\r") {
                $this->pos++;
            } elseif ($char === '#') {
                $end = strpos($this->input, "\n", $this->pos);
                $this->pos = $end === false ? $this->length : $end;
            } else {
                return;
            }
        }
    }

    private function expect(string $char): void
    {
        if ($this->peek() !== $char) {
            throw $this->error(\sprintf('Expected "%s", found "%s"', $char, $this->peek() === '' ? 'end of input' : $this->peek()));
        }
        $this->pos++;
    }

    private function peek(int $offset = 0): string
    {
        return $this->input[$this->pos + $offset] ?? '';
    }

    private function peekString(string $text): bool
    {
        return substr($this->input, $this->pos, \strlen($text)) === $text;
    }

    private function isDelimiter(string $char): bool
    {
        return $char === '' || ctype_space($char) || str_contains('.;,[]()<"#', $char);
    }

    private function error(string $message): TurtleSyntaxError
    {
        return TurtleSyntaxError::at($message, $this->line, $this->pos - $this->lineStart + 1);
    }
}
