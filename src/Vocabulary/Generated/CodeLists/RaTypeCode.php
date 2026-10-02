<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list RaTypeCode: Restricted code list based on cXML code list 1.84 Category Colour. */
final class RaTypeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/RaTypeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** III-Yellow */
    public const string III_YELLOW = 'https://onerecord.iata.org/ns/code-lists/RaTypeCode#III_YELLOW';

    /** II-Yellow */
    public const string II_YELLOW = 'https://onerecord.iata.org/ns/code-lists/RaTypeCode#II_YELLOW';

    /** I-Yellow */
    public const string I_WHITE = 'https://onerecord.iata.org/ns/code-lists/RaTypeCode#I_WHITE';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'III_YELLOW' => self::III_YELLOW,
        'II_YELLOW' => self::II_YELLOW,
        'I_WHITE' => self::I_WHITE,
    ];
}
