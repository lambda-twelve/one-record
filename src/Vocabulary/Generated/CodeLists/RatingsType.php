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
 * Code list RatingsType: Restricted code list to describe whether a rating is Face, Published or
 * Actual.
 */
final class RatingsType
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/RatingsType';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Actual */
    public const string A = 'https://onerecord.iata.org/ns/code-lists/RatingsType#A';

    /** Published */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/RatingsType#C';

    /** Face */
    public const string F = 'https://onerecord.iata.org/ns/code-lists/RatingsType#F';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'A' => self::A,
        'C' => self::C,
        'F' => self::F,
    ];
}
