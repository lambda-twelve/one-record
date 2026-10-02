<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model\Builder;

use DateTimeInterface;
use LambdaTwelve\OneRecord\Model\LocalRef;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;

/**
 * The value shapes every mapper needs, so hosts never assemble ONE Record
 * JSON by hand: measured quantities, money, code-list members, codes from
 * lists ONE Record does not publish, dates, references.
 */
final class Values
{
    private function __construct() {}

    /**
     * cargo:Value: a number with a unit from the MeasurementUnitCode list
     * (`MeasurementUnitCode::KGM`), or any unit IRI.
     */
    public static function quantity(float|int $number, string $unitIri): Embedded
    {
        return Embedded::of(Cargo::Value)
            ->set(Cargo::numericalValue, (float) $number)
            ->set(Cargo::unit, new Iri($unitIri));
    }

    /**
     * cargo:CurrencyValue. CurrencyCode is an open list with no published
     * members, so the IRI follows the code-list pattern:
     * .../code-lists/CurrencyCode#EUR (see spec question 11).
     */
    public static function money(float|int $amount, string $currencyCode): Embedded
    {
        if (preg_match('/^[A-Z]{3}$/', $currencyCode) !== 1) {
            throw new ModelException(\sprintf('"%s" is not an ISO 4217 currency code.', $currencyCode));
        }

        return Embedded::of(Cargo::CurrencyValue)
            ->set(Cargo::numericalValue, (float) $amount)
            ->set(Cargo::currencyUnit, new Iri(Namespaces::codeList('CurrencyCode', $currencyCode)));
    }

    /**
     * A member of a ONE Record code list by list name and code, checked
     * against the published members unless the list is open.
     */
    public static function code(string $list, string $code, ?Vocabulary $vocabulary = null): Iri
    {
        $vocabulary ??= Vocabulary::default();
        $info = $vocabulary->codeList(Namespaces::CODE_LISTS . $list);
        if ($info === null) {
            throw new ModelException(\sprintf('"%s" is not a ONE Record code list.', $list));
        }
        if (!$info->open && !$info->hasCode($code)) {
            throw new ModelException(\sprintf('"%s" is not a published code of the closed list %s.', $code, $list));
        }

        return new Iri($info->iriOf($code));
    }

    /**
     * cargo:CodeListElement, for codes from lists ONE Record does not publish
     * (ISO countries, IATA airports, HS codes).
     */
    public static function codeListElement(string $code, string $listName, ?string $listVersion = null, ?string $description = null): Embedded
    {
        return Embedded::of(Cargo::CodeListElement)
            ->set(Cargo::code, $code)
            ->set(Cargo::codeListName, $listName)
            ->setIf(Cargo::codeListVersion, $listVersion)
            ->setIf(Cargo::codeDescription, $description);
    }

    public static function dateTime(DateTimeInterface $value): Literal
    {
        return Literal::dateTime($value);
    }

    /**
     * xsd:date, for properties whose range is a date rather than a date-time.
     */
    public static function date(DateTimeInterface $value): Literal
    {
        return new Literal($value->format('Y-m-d'), Namespaces::xsd('date'));
    }

    public static function iri(string $iri): Iri
    {
        return new Iri($iri);
    }

    /**
     * A named individual of the ontology (cargo:ACTUAL, cargo:MASTER, ...).
     */
    public static function individual(string $iri, ?Vocabulary $vocabulary = null): Iri
    {
        if (($vocabulary ?? Vocabulary::default())->individual($iri) === null) {
            throw new ModelException(\sprintf('"%s" is not a named individual of the ontology.', $iri));
        }

        return new Iri($iri);
    }

    public static function ref(string $localKey): LocalRef
    {
        return LocalRef::to($localKey);
    }

    /**
     * Normalise what a mapper passes into a term: PHP scalars become typed
     * literals, dates become xsd:dateTime, Iri/Literal/LocalRef/Embedded pass through.
     */
    public static function term(mixed $value): Literal|Iri|LocalRef|Embedded
    {
        return match (true) {
            $value instanceof Literal, $value instanceof Iri, $value instanceof LocalRef, $value instanceof Embedded => $value,
            \is_string($value) => Literal::string($value),
            \is_bool($value) => Literal::boolean($value),
            \is_int($value) => Literal::integer($value),
            \is_float($value) => Literal::double($value),
            $value instanceof DateTimeInterface => Literal::dateTime($value),
            default => throw new ModelException(\sprintf('Cannot use a value of type %s in a logistics object; pass a scalar, DateTimeInterface, Iri, Literal, LocalRef or Embedded.', get_debug_type($value))),
        };
    }
}
