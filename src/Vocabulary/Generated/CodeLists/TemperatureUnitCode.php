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
 * Code list TemperatureUnitCode: Restricted sub-code list of temperature units from
 * MeasurementUnitCode.
 */
final class TemperatureUnitCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/TemperatureUnitCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Degree Celsius */
    public const string CEL = 'https://onerecord.iata.org/ns/code-lists/TemperatureUnitCode#CEL';

    /** Degree Fahrenheit */
    public const string FAH = 'https://onerecord.iata.org/ns/code-lists/TemperatureUnitCode#FAH';

    /** Kelvin */
    public const string KEL = 'https://onerecord.iata.org/ns/code-lists/TemperatureUnitCode#KEL';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CEL' => self::CEL,
        'FAH' => self::FAH,
        'KEL' => self::KEL,
    ];
}
