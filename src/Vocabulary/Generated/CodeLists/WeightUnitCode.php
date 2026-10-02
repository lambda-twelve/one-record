<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list WeightUnitCode: Restricted sub-code list of weight units from MeasurementUnitCode. */
final class WeightUnitCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/WeightUnitCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Kilogram */
    public const string KGM = 'https://onerecord.iata.org/ns/code-lists/WeightUnitCode#KGM';

    /** Pound UK, US (0.45359237 KGM) */
    public const string LBR = 'https://onerecord.iata.org/ns/code-lists/WeightUnitCode#LBR';

    /** Ounce UK, US (28.949523 GRM) */
    public const string ONZ = 'https://onerecord.iata.org/ns/code-lists/WeightUnitCode#ONZ';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'KGM' => self::KGM,
        'LBR' => self::LBR,
        'ONZ' => self::ONZ,
    ];
}
