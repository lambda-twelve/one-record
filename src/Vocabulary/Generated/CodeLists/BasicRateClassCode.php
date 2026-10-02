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
 * Code list BasicRateClassCode: Restricted sub-code list corresponding to elements of cXML code list
 * 1.4 Rate Class Codes Source: CSC Resolutions Manual, 25th Edition, Resolution 600a.
 */
final class BasicRateClassCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Specific Commodity Rate */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode#C';

    /** Minimum Charge */
    public const string M = 'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode#M';

    /** Normal Rate */
    public const string N = 'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode#N';

    /** Quantity Rate */
    public const string Q = 'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode#Q';

    /** Unit Load Device Basic Charge or Rate */
    public const string U = 'https://onerecord.iata.org/ns/code-lists/BasicRateClassCode#U';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'C' => self::C,
        'M' => self::M,
        'N' => self::N,
        'Q' => self::Q,
        'U' => self::U,
    ];
}
