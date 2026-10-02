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
 * Code list PrepaidCollectIndicator: Restricted code list corresponding to cXML code list 1.5
 * Prepaid/Collect Indicators.
 */
final class PrepaidCollectIndicator
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/PrepaidCollectIndicator';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Collect Indicator */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/PrepaidCollectIndicator#C';

    /** Prepaid Indicator */
    public const string P = 'https://onerecord.iata.org/ns/code-lists/PrepaidCollectIndicator#P';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'C' => self::C,
        'P' => self::P,
    ];
}
