<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list VolumeUnitCode: Restricted sub-code list of volume units from MeasurementUnitCode. */
final class VolumeUnitCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Cubic Centimetre */
    public const string CMQ = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode#CMQ';

    /** Cubic Foot */
    public const string FTQ = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode#FTQ';

    /** Liquid Gallon (3.78541 DM3) */
    public const string GLL = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode#GLL';

    /** Cubic Inch */
    public const string INQ = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode#INQ';

    /** Litre (1 DM3) */
    public const string LTR = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode#LTR';

    /** Cubic Metre */
    public const string MTQ = 'https://onerecord.iata.org/ns/code-lists/VolumeUnitCode#MTQ';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CMQ' => self::CMQ,
        'FTQ' => self::FTQ,
        'GLL' => self::GLL,
        'INQ' => self::INQ,
        'LTR' => self::LTR,
        'MTQ' => self::MTQ,
    ];
}
