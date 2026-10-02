<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list PackageMarkCode: Open code list of indicators of how a package is marked. */
final class PackageMarkCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/PackageMarkCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = true;

    /** Serial Shipping Container Code-18 / EAN-18 */
    public const string SSCC_18 = 'https://onerecord.iata.org/ns/code-lists/PackageMarkCode#SSCC_18';

    /** Universal Product Code */
    public const string UPC = 'https://onerecord.iata.org/ns/code-lists/PackageMarkCode#UPC';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'SSCC_18' => self::SSCC_18,
        'UPC' => self::UPC,
    ];
}
