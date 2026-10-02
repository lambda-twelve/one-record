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
 * Code list ULDLoadingIndicator: Restricted code list corresponding to cXML code list 1.47 ULD Loading
 * Indicators.
 */
final class ULDLoadingIndicator
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** ULD Height below 160 centimetres */
    public const string L = 'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator#L';

    /** Main Deck Loading only */
    public const string M = 'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator#M';

    /** Nose Door Loading only */
    public const string N = 'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator#N';

    /** ULD Height above 244 centimetres */
    public const string R = 'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator#R';

    /** ULD Height between 160 centimetres and 244 centimetres */
    public const string U = 'https://onerecord.iata.org/ns/code-lists/ULDLoadingIndicator#U';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'L' => self::L,
        'M' => self::M,
        'N' => self::N,
        'R' => self::R,
        'U' => self::U,
    ];
}
