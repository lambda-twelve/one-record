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
 * Code list ShipmentSecurityStatus: Restricted code list indicating whether a shipment is secured or
 * not secured.
 */
final class ShipmentSecurityStatus
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ShipmentSecurityStatus';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Screened */
    public const string NCR = 'https://onerecord.iata.org/ns/code-lists/ShipmentSecurityStatus#NCR';

    /** Not Screened */
    public const string SCR = 'https://onerecord.iata.org/ns/code-lists/ShipmentSecurityStatus#SCR';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'NCR' => self::NCR,
        'SCR' => self::SCR,
    ];
}
