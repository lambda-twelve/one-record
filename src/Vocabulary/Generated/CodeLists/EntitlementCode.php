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
 * Code list EntitlementCode: Restricted code list corresponding to cXML code list 1.3 Entitlement
 * Codes Source: CSC Resolutions Manual, 25th Edition, Resolution 600a.
 */
final class EntitlementCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/EntitlementCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Other Charges due Agent */
    public const string A = 'https://onerecord.iata.org/ns/code-lists/EntitlementCode#A';

    /** Other Charges due Carrier */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/EntitlementCode#C';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'A' => self::A,
        'C' => self::C,
    ];
}
