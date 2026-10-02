<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/**
 * Code list ULDChargeCode: Restricted code list corresponding to cXML code list 1.44 ULD Charge Codes
 * Source: CTCC Documentation.
 */
final class ULDChargeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Pivot Rate per kilogram */
    public const string A = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#A';

    /** First Minimum Charge — minimum weight */
    public const string B = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#B';

    /** First over pivot rate per kilogram */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#C';

    /** Second Minimum Charge — minimum weight */
    public const string D = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#D';

    /** Second over pivot rate per kilogram */
    public const string E = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#E';

    /** Third Minimum Charge — minimum weight */
    public const string F = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#F';

    /** Third over pivot rate per kilogram */
    public const string G = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#G';

    /** Flat Charge — (without weight or with minimum weight) */
    public const string H = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#H';

    /** Flat Charge — maximum weight */
    public const string I = 'https://onerecord.iata.org/ns/code-lists/ULDChargeCode#I';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'A' => self::A,
        'B' => self::B,
        'C' => self::C,
        'D' => self::D,
        'E' => self::E,
        'F' => self::F,
        'G' => self::G,
        'H' => self::H,
        'I' => self::I,
    ];
}
