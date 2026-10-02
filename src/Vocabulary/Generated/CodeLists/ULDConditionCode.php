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
 * Code list ULDConditionCode: Restricted code list corresponding to cXML code list 1.21 ULD Condition
 * Codes.
 */
final class ULDConditionCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ULDConditionCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Damaged But Still Serviceable */
    public const string DAM = 'https://onerecord.iata.org/ns/code-lists/ULDConditionCode#DAM';

    /** Serviceable */
    public const string SER = 'https://onerecord.iata.org/ns/code-lists/ULDConditionCode#SER';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'DAM' => self::DAM,
        'SER' => self::SER,
    ];
}
