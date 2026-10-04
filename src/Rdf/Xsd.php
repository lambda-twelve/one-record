<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * What the SDK knows about XML Schema datatypes: which derive from which
 * (an xsd:int is an xsd:integer, which is an xsd:decimal), and each one's
 * lexical grammar with its bounds. The builder and the change applier share
 * satisfies(), the range rule (R8-002). lexicallyValid() is applied by the
 * change applier to every literal a change adds (R7-007); the builder trusts
 * the lexical form of a Literal a caller supplies, so a successful build
 * says the property accepts a value of that datatype, not that the supplied
 * text is within the datatype's grammar or bounds (D9-001).
 */
final class Xsd
{
    private const string NS = Namespaces::XSD;

    /** Derived type => the type it restricts (XSD 1.1 part 2, built-in derived types). */
    private const array PARENT = [
        self::NS . 'long' => self::NS . 'integer',
        self::NS . 'int' => self::NS . 'long',
        self::NS . 'short' => self::NS . 'int',
        self::NS . 'byte' => self::NS . 'short',
        self::NS . 'nonNegativeInteger' => self::NS . 'integer',
        self::NS . 'positiveInteger' => self::NS . 'nonNegativeInteger',
        self::NS . 'unsignedLong' => self::NS . 'nonNegativeInteger',
        self::NS . 'unsignedInt' => self::NS . 'unsignedLong',
        self::NS . 'unsignedShort' => self::NS . 'unsignedInt',
        self::NS . 'unsignedByte' => self::NS . 'unsignedShort',
        self::NS . 'nonPositiveInteger' => self::NS . 'integer',
        self::NS . 'negativeInteger' => self::NS . 'nonPositiveInteger',
        self::NS . 'integer' => self::NS . 'decimal',
    ];

    /** Bounded integer types: [minimum, maximum] as digit strings. */
    private const array BOUNDS = [
        self::NS . 'long' => ['-9223372036854775808', '9223372036854775807'],
        self::NS . 'int' => ['-2147483648', '2147483647'],
        self::NS . 'short' => ['-32768', '32767'],
        self::NS . 'byte' => ['-128', '127'],
        self::NS . 'unsignedLong' => ['0', '18446744073709551615'],
        self::NS . 'unsignedInt' => ['0', '4294967295'],
        self::NS . 'unsignedShort' => ['0', '65535'],
        self::NS . 'unsignedByte' => ['0', '255'],
        self::NS . 'nonNegativeInteger' => ['0', null],
        self::NS . 'positiveInteger' => ['1', null],
        self::NS . 'nonPositiveInteger' => [null, '0'],
        self::NS . 'negativeInteger' => [null, '-1'],
    ];

    /**
     * Whether a value of $datatype satisfies a property whose range is one of
     * $ranges: the range itself, a type it is derived from, or, for the
     * numeric ranges, a type whose values are numbers of that kind (any
     * integer or decimal type where a double or float is expected).
     *
     * @param list<string> $ranges
     */
    public static function satisfies(string $datatype, array $ranges): bool
    {
        if ($ranges === []) {
            return true;
        }
        $ancestry = self::ancestry($datatype);
        foreach ($ranges as $range) {
            if (\in_array($range, $ancestry, true)) {
                return true;
            }
            if (($range === self::NS . 'double' || $range === self::NS . 'float') && \in_array(self::NS . 'decimal', $ancestry, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * The datatype and every type it restricts, nearest first.
     *
     * @return non-empty-list<string>
     */
    public static function ancestry(string $datatype): array
    {
        $chain = [$datatype];
        while (isset(self::PARENT[$datatype])) {
            $datatype = self::PARENT[$datatype];
            $chain[] = $datatype;
        }

        return $chain;
    }

    public static function isIntegerType(string $datatype): bool
    {
        return \in_array(self::NS . 'integer', self::ancestry($datatype), true);
    }

    /**
     * The lexical grammar of each datatype the SDK knows, bounds included;
     * unknown datatypes are not checked.
     */
    public static function lexicallyValid(Literal $literal): bool
    {
        $lexical = $literal->lexical;
        $datatype = $literal->datatype;
        if (self::isIntegerType($datatype)) {
            if (preg_match('/^[+-]?\d+$/', $lexical) !== 1) {
                return false;
            }
            [$min, $max] = self::BOUNDS[$datatype] ?? [null, null];

            return ($min === null || self::compareIntegers($lexical, $min) >= 0) && ($max === null || self::compareIntegers($lexical, $max) <= 0);
        }

        return match ($datatype) {
            self::NS . 'boolean' => \in_array($lexical, ['true', 'false', '1', '0'], true),
            self::NS . 'decimal' => preg_match('/^[+-]?(\d+(\.\d*)?|\.\d+)$/', $lexical) === 1,
            self::NS . 'double', self::NS . 'float' => preg_match('/^([+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?|[+-]?INF|NaN)$/', $lexical) === 1,
            self::NS . 'dateTime' => self::dateTimeValid($lexical),
            self::NS . 'date' => preg_match('/^(-?\d{4,})-(\d{2})-(\d{2})(Z|[+-](\d{2}):(\d{2}))?$/', $lexical, $m) === 1 && checkdate((int) $m[2], (int) $m[3], abs((int) $m[1])) && self::offsetValid($m[5] ?? '', $m[6] ?? ''),
            self::NS . 'anyURI' => preg_match('/[\s<>"{}|\\^`]/', $lexical) !== 1,
            default => true,
        };
    }

    /**
     * XSD dateTime: calendar date, 24:00:00 allowed as the end of the day, offsets to 14:00.
     */
    private static function dateTimeValid(string $lexical): bool
    {
        if (preg_match('/^(-?\d{4,})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d+)?(Z|([+-])(\d{2}):(\d{2}))?$/', $lexical, $m) !== 1) {
            return false;
        }
        if (!checkdate((int) $m[2], (int) $m[3], abs((int) $m[1])) || (int) $m[5] > 59 || (int) $m[6] > 59) {
            return false;
        }
        $hour = (int) $m[4];
        if ($hour > 24 || ($hour === 24 && ((int) $m[5] !== 0 || (int) $m[6] !== 0 || ltrim($m[7] ?? '', '.0') !== ''))) {
            return false;
        }
        return self::offsetValid($m[10] ?? '', $m[11] ?? '');
    }

    /**
     * A timezone offset of at most 14:00 (XSD part 2, timezoneOffset); empty means none given.
     */
    private static function offsetValid(string $hours, string $minutes): bool
    {
        if ($hours === '') {
            return true;
        }
        $h = (int) $hours;
        $m = (int) $minutes;

        return $h <= 14 && $m <= 59 && !($h === 14 && $m !== 0);
    }

    /**
     * Compares two integer lexicals of any length: -1, 0 or 1.
     */
    private static function compareIntegers(string $a, string $b): int
    {
        $normalise = static function (string $n): array {
            $negative = str_starts_with($n, '-');
            $digits = ltrim(ltrim($n, '+-'), '0');
            if ($digits === '') {
                return [false, '0'];
            }

            return [$negative, $digits];
        };
        [$negA, $digitsA] = $normalise($a);
        [$negB, $digitsB] = $normalise($b);
        if ($negA !== $negB) {
            return $negA ? -1 : 1;
        }
        $magnitude = \strlen($digitsA) <=> \strlen($digitsB);
        if ($magnitude === 0) {
            $magnitude = strcmp($digitsA, $digitsB) <=> 0;
        }

        return $negA ? -$magnitude : $magnitude;
    }
}
