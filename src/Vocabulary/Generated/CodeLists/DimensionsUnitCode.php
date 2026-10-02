<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list DimensionsUnitCode: Restricted sub-code list of length units from MeasurementUnitCode. */
final class DimensionsUnitCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/DimensionsUnitCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Centimetre */
    public const string CMT = 'https://onerecord.iata.org/ns/code-lists/DimensionsUnitCode#CMT';

    /** Foot */
    public const string FOT = 'https://onerecord.iata.org/ns/code-lists/DimensionsUnitCode#FOT';

    /** Inch */
    public const string INH = 'https://onerecord.iata.org/ns/code-lists/DimensionsUnitCode#INH';

    /** Metre */
    public const string MTR = 'https://onerecord.iata.org/ns/code-lists/DimensionsUnitCode#MTR';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CMT' => self::CMT,
        'FOT' => self::FOT,
        'INH' => self::INH,
        'MTR' => self::MTR,
    ];
}
