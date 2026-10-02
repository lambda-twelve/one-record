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
 * Code list ChargeIdentifier: Restricted code list corresponding to cXML code list 1.33 Charge
 * Identifiers.
 */
final class ChargeIdentifier
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** CASS Net Amount */
    public const string CN = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#CN';

    /** Commission */
    public const string CO = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#CO';

    /** Charge Summary Total */
    public const string CT = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#CT';

    /** Insurance */
    public const string IN = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#IN';

    /** CASS Invoice Amount */
    public const string NI = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#NI';

    /** Total Other Charges Due Agent */
    public const string OA = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#OA';

    /** Total Other Charges Due Carrier */
    public const string OC = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#OC';

    /** Sales Incentive */
    public const string SI = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#SI';

    /** Taxes */
    public const string TX = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#TX';

    /** Valuation Charge */
    public const string VC = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#VC';

    /** Total Weight Charge */
    public const string WT = 'https://onerecord.iata.org/ns/code-lists/ChargeIdentifier#WT';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CN' => self::CN,
        'CO' => self::CO,
        'CT' => self::CT,
        'IN' => self::IN,
        'NI' => self::NI,
        'OA' => self::OA,
        'OC' => self::OC,
        'SI' => self::SI,
        'TX' => self::TX,
        'VC' => self::VC,
        'WT' => self::WT,
    ];
}
