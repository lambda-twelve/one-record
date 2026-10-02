<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;
use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * An RDF literal: a lexical form with a datatype IRI, or a language-tagged
 * string. The lexical form is kept exactly as received; comparisons that need
 * value equality (20.0 versus 2.0E1) normalise explicitly, so nothing is lost
 * between what a partner sent and what is stored.
 */
final readonly class Literal implements Term
{
    public const string XSD_STRING = Namespaces::XSD . 'string';
    public const string XSD_BOOLEAN = Namespaces::XSD . 'boolean';
    public const string XSD_INTEGER = Namespaces::XSD . 'integer';
    public const string XSD_DOUBLE = Namespaces::XSD . 'double';
    public const string XSD_DECIMAL = Namespaces::XSD . 'decimal';
    public const string XSD_DATETIME = Namespaces::XSD . 'dateTime';
    public const string XSD_ANYURI = Namespaces::XSD . 'anyURI';
    public const string RDF_LANG_STRING = Namespaces::RDF . 'langString';

    public string $datatype;

    public function __construct(
        public string $lexical,
        ?string $datatype = null,
        public ?string $language = null,
    ) {
        if ($language !== null && $datatype !== null && $datatype !== self::RDF_LANG_STRING) {
            throw new InvalidArgumentException('A language-tagged literal cannot carry another datatype.');
        }
        $this->datatype = $language !== null ? self::RDF_LANG_STRING : ($datatype ?? self::XSD_STRING);
    }

    public static function string(string $value): self
    {
        return new self($value);
    }

    public static function boolean(bool $value): self
    {
        return new self($value ? 'true' : 'false', self::XSD_BOOLEAN);
    }

    public static function integer(int $value): self
    {
        return new self((string) $value, self::XSD_INTEGER);
    }

    public static function double(float $value): self
    {
        return new self(self::formatDouble($value), self::XSD_DOUBLE);
    }

    public static function dateTime(DateTimeInterface $value): self
    {
        $utc = DateTimeImmutable::createFromInterface($value)->setTimezone(new DateTimeZone('UTC'));

        return new self($utc->format('Y-m-d\TH:i:s.v\Z'), self::XSD_DATETIME);
    }

    public function isString(): bool
    {
        return $this->datatype === self::XSD_STRING;
    }

    public function toNTriples(): string
    {
        $escaped = '"' . addcslashes($this->lexical, "\"\\\n\r\t") . '"';
        if ($this->language !== null) {
            return $escaped . '@' . $this->language;
        }

        return $this->isString() ? $escaped : $escaped . '^^<' . $this->datatype . '>';
    }

    public function equals(Term $other): bool
    {
        return $other instanceof self
            && $other->lexical === $this->lexical
            && $other->datatype === $this->datatype
            && $other->language === $this->language;
    }

    /**
     * JSON-LD's canonical form for doubles (the JSON-LD 1.1 API, section on
     * value conversion): "%1.15E" trimmed of surplus zeros, so that 20.0 and
     * 2.0E1 normalise to the same lexical form.
     */
    public static function formatDouble(float $value): string
    {
        if (is_nan($value) || is_infinite($value)) {
            throw new InvalidArgumentException('NaN and infinity have no JSON representation.');
        }
        $formatted = \sprintf('%1.15E', $value);
        [$mantissa, $exponent] = explode('E', $formatted);
        $mantissa = rtrim($mantissa, '0');
        if (str_ends_with($mantissa, '.')) {
            $mantissa .= '0';
        }

        return $mantissa . 'E' . (int) $exponent;
    }
}
