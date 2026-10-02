<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * Reading typed values out of an expanded API document, leniently: partners
 * write IRIs as {"@id"} references, as xsd:anyURI literals or as plain
 * strings, and dates with or without milliseconds. Writing helpers produce
 * the shapes IATA's examples use.
 */
final class Nodes
{
    private function __construct() {}

    public static function iri(Graph $graph, Iri|BlankNode $node, string $predicate): ?Iri
    {
        foreach ($graph->objects($node, $predicate) as $term) {
            $iri = self::asIri($term);
            if ($iri !== null) {
                return $iri;
            }
        }

        return null;
    }

    /**
     * @return list<Iri>
     */
    public static function iris(Graph $graph, Iri|BlankNode $node, string $predicate): array
    {
        $out = [];
        foreach ($graph->objects($node, $predicate) as $term) {
            $iri = self::asIri($term);
            if ($iri !== null) {
                $out[$iri->value] = $iri;
            }
        }
        ksort($out, SORT_STRING);

        return array_values($out);
    }

    public static function string(Graph $graph, Iri|BlankNode $node, string $predicate): ?string
    {
        $term = $graph->firstObject($node, $predicate);

        return match (true) {
            $term instanceof Literal => $term->lexical,
            $term instanceof Iri => $term->value,
            default => null,
        };
    }

    /**
     * @return list<string>
     */
    public static function strings(Graph $graph, Iri|BlankNode $node, string $predicate): array
    {
        $out = [];
        foreach ($graph->objects($node, $predicate) as $term) {
            if ($term instanceof Literal) {
                $out[] = $term->lexical;
            } elseif ($term instanceof Iri) {
                $out[] = $term->value;
            }
        }
        sort($out, SORT_STRING);

        return $out;
    }

    public static function bool(Graph $graph, Iri|BlankNode $node, string $predicate): ?bool
    {
        $term = $graph->firstObject($node, $predicate);
        if (!$term instanceof Literal) {
            return null;
        }

        return match ($term->lexical) {
            'true', '1' => true,
            'false', '0' => false,
            default => null,
        };
    }

    public static function int(Graph $graph, Iri|BlankNode $node, string $predicate): ?int
    {
        $term = $graph->firstObject($node, $predicate);
        if (!$term instanceof Literal || preg_match('/^[+-]?\d+$/', $term->lexical) !== 1) {
            return null;
        }

        return (int) $term->lexical;
    }

    public static function dateTime(Graph $graph, Iri|BlankNode $node, string $predicate): ?DateTimeImmutable
    {
        $term = $graph->firstObject($node, $predicate);
        if (!$term instanceof Literal) {
            return null;
        }

        return self::parseDateTime($term->lexical);
    }

    public static function parseDateTime(string $lexical): ?DateTimeImmutable
    {
        try {
            return new DateTimeImmutable($lexical);
        } catch (Exception) {
            return null;
        }
    }

    /**
     * @return list<Iri|BlankNode>
     */
    public static function nodes(Graph $graph, Iri|BlankNode $node, string $predicate): array
    {
        $out = [];
        foreach ($graph->objects($node, $predicate) as $term) {
            if ($term instanceof Iri || $term instanceof BlankNode) {
                $out[] = $term;
            }
        }

        return $out;
    }

    public static function node(Graph $graph, Iri|BlankNode $node, string $predicate): Iri|BlankNode|null
    {
        return self::nodes($graph, $node, $predicate)[0] ?? null;
    }

    private static function asIri(Term $term): ?Iri
    {
        if ($term instanceof Iri) {
            return $term;
        }
        if ($term instanceof Literal && Context::isAbsoluteIri($term->lexical) && preg_match('/\s/', $term->lexical) !== 1) {
            try {
                return new Iri($term->lexical);
            } catch (InvalidArgumentException) {
                return null;
            }
        }

        return null;
    }

    /**
     * @return array{'@type': string, '@value': string}
     */
    public static function dateTimeValue(DateTimeInterface $value): array
    {
        return ['@type' => Namespaces::XSD . 'dateTime', '@value' => Literal::dateTime($value)->lexical];
    }

    /**
     * @return array{'@type': string, '@value': string}
     */
    public static function anyUri(string $iri): array
    {
        return ['@type' => Namespaces::XSD . 'anyURI', '@value' => $iri];
    }

    /**
     * @return array{'@type': string, '@value': string}
     */
    public static function positiveInteger(int $value): array
    {
        return ['@type' => Namespaces::XSD . 'positiveInteger', '@value' => (string) $value];
    }

    /**
     * @return array{'@id': string}
     */
    public static function ref(Iri|string $iri): array
    {
        return ['@id' => $iri instanceof Iri ? $iri->value : $iri];
    }

    /**
     * @return array<string, string>
     */
    public static function context(): array
    {
        return ['cargo' => Namespaces::CARGO, 'api' => Namespaces::API, 'xsd' => Namespaces::XSD];
    }

    /**
     * Compacts an api: or cargo: IRI for @id references to individuals (api:REQUEST_PENDING).
     */
    public static function compact(string $iri): string
    {
        foreach (['api' => Namespaces::API, 'cargo' => Namespaces::CARGO, 'xsd' => Namespaces::XSD] as $prefix => $namespace) {
            if (str_starts_with($iri, $namespace)) {
                return $prefix . ':' . substr($iri, \strlen($namespace));
            }
        }

        return $iri;
    }
}
