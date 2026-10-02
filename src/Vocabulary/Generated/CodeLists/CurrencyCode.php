<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list CurrencyCode: Open code list of currency codes based on ISO 4217 Source: ISO 4217. */
final class CurrencyCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/CurrencyCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = true;

    /** @var array<string, string> code => IRI */
    public const array ALL = [
    ];
}
