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

    /**
     * The one dateTime value of a property, or null when the property is absent.
     * A value that is present but not an xsd:dateTime is an error: an expiry
     * that fails to parse must never read as "no expiry" (AR-016).
     *
     * @throws JsonLdException
     */
    public static function dateTime(Graph $graph, Iri|BlankNode $node, string $predicate): ?DateTimeImmutable
    {
        $terms = $graph->objects($node, $predicate);
        if ($terms === []) {
            return null;
        }
        if (\count($terms) > 1) {
            throw JsonLdException::at(self::compact($predicate), 'Expected one dateTime value, got ' . \count($terms));
        }
        $term = $terms[0];
        if (!$term instanceof Literal) {
            throw JsonLdException::at(self::compact($predicate), 'Expected a dateTime literal, got a node');
        }

        return self::parseDateTime($term->lexical) ?? throw JsonLdException::at(self::compact($predicate), \sprintf('"%s" is not an xsd:dateTime (YYYY-MM-DDThh:mm:ss[.fff](Z|±hh:mm))', $term->lexical));
    }

    /**
     * Strict xsd:dateTime lexical form; PHP's free-form date grammar would
     * accept "tomorrow" and "0", which no partner means.
     */
    public static function parseDateTime(string $lexical): ?DateTimeImmutable
    {
        // XML Schema 1.1 dateTime: ranges checked here, because PHP would normalise 12:00:60 to 12:01:00
        // and 2026-02-30 to March, and an offset of +14:59 is outside the datatype (R2-008).
        if (preg_match('/^(-?\d{4,})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d+)?(Z|([+-])(\d{2}):(\d{2}))?$/', $lexical, $m) !== 1) {
            return null;
        }
        [$year, $month, $day, $hour, $minute, $second] = [(int) $m[1], (int) $m[2], (int) $m[3], (int) $m[4], (int) $m[5], (int) $m[6]];
        $fraction = $m[7] ?? '';
        if (!checkdate($month, $day, $year) || $minute > 59 || $second > 59) {
            return null;
        }
        $endOfDay = $hour === 24;
        if ($hour > 24 || ($endOfDay && ($minute !== 0 || $second !== 0 || ltrim($fraction, '.0') !== ''))) {
            return null;
        }
        $zone = $m[8] ?? '';
        if ($zone !== '' && $zone !== 'Z') {
            $offsetHours = (int) ($m[10] ?? '0');
            $offsetMinutes = (int) ($m[11] ?? '0');
            if ($offsetHours > 14 || $offsetMinutes > 59 || ($offsetHours === 14 && $offsetMinutes !== 0)) {
                return null;
            }
        }
        try {
            // A zoneless value has no timezone in XSD; UTC is the only reading that does not depend on the host.
            $parsed = new DateTimeImmutable(\sprintf('%s-%02d-%02dT%02d:%02d:%02d%s%s', $m[1], $month, $day, $endOfDay ? 0 : $hour, $minute, $second, $fraction, $zone === '' ? 'Z' : $zone));
        } catch (Exception) {
            return null;
        }

        // 24:00:00 is the end of the day, which is midnight starting the next one.
        return $endOfDay ? $parsed->modify('+1 day') : $parsed;
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
