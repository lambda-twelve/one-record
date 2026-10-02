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
 * Code list RateClassCode: Restricted code list corresponding to cXML code list 1.4 Rate Class Codes
 * Source: CSC Resolutions Manual, 25th Edition, Resolution 600a.
 */
final class RateClassCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/RateClassCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Basic Charge */
    public const string B = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#B';

    /** Specific Commodity Rate */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#C';

    /** Unit Load Device Additional Rate */
    public const string E = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#E';

    /** Rate per Kilogram */
    public const string K = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#K';

    /** Minimum Charge */
    public const string M = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#M';

    /** Normal Rate */
    public const string N = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#N';

    /** International Priority Service Rate */
    public const string P = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#P';

    /** Quantity Rate */
    public const string Q = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#Q';

    /** Class Rate Reduction */
    public const string R = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#R';

    /** Class Rate Surcharge */
    public const string S = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#S';

    /** Unit Load Device Basic Charge or Rate */
    public const string U = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#U';

    /** Weight Increase */
    public const string W = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#W';

    /** Unit Load Device Additional Information */
    public const string X = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#X';

    /** Unit Load Device Discount */
    public const string Y = 'https://onerecord.iata.org/ns/code-lists/RateClassCode#Y';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'B' => self::B,
        'C' => self::C,
        'E' => self::E,
        'K' => self::K,
        'M' => self::M,
        'N' => self::N,
        'P' => self::P,
        'Q' => self::Q,
        'R' => self::R,
        'S' => self::S,
        'U' => self::U,
        'W' => self::W,
        'X' => self::X,
        'Y' => self::Y,
    ];
}
