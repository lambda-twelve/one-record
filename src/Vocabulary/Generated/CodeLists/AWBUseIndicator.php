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
 * Code list AWBUseIndicator: Restricted code list to describe Revenue, Service and Void AWBs based on
 * CASS 2.0.
 */
final class AWBUseIndicator
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/AWBUseIndicator';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Revenue AWB */
    public const string R = 'https://onerecord.iata.org/ns/code-lists/AWBUseIndicator#R';

    /** Service AWB */
    public const string S = 'https://onerecord.iata.org/ns/code-lists/AWBUseIndicator#S';

    /** Void AWB */
    public const string V = 'https://onerecord.iata.org/ns/code-lists/AWBUseIndicator#V';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'R' => self::R,
        'S' => self::S,
        'V' => self::V,
    ];
}
