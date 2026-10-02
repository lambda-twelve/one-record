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
 * Code list ChargeCode: Restricted code list corresponding to cXML code list 1.1 Charge Codes Source:
 * CSC Resolutions Manual, 25th Edition, Resolution 600a.
 */
final class ChargeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ChargeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Partial Collect Credit — Partial Prepaid Cash */
    public const string CA = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CA';

    /** Partial Collect Credit — Partial Prepaid Credit */
    public const string CB = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CB';

    /** All Charges Collect */
    public const string CC = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CC';

    /** Partial Collect Credit Card — Partial Prepaid Cash */
    public const string CE = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CE';

    /** All Charges Collect by GBL */
    public const string CG = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CG';

    /** Partial Collect Credit Card — Partial Prepaid Credit */
    public const string CH = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CH';

    /** Destination Collect by MCO */
    public const string CM = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CM';

    /** Destination Collect Cash */
    public const string CP = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CP';

    /** Destination Collect Credit */
    public const string CX = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CX';

    /** All Charges Collect by Credit Card */
    public const string CZ = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#CZ';

    /** No Charge */
    public const string NC = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#NC';

    /** No Weight Charge — Other Charges Prepaid by GBL */
    public const string NG = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#NG';

    /** No Weight Charge — Other Charges Prepaid Cash */
    public const string NP = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#NP';

    /** No Weight Charge — Other Charges Collect */
    public const string NT = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#NT';

    /** No Weight Charge — Other Charges Prepaid Credit */
    public const string NX = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#NX';

    /** No Weight Charge — Other Charges Prepaid by Credit Card */
    public const string NZ = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#NZ';

    /** Partial Prepaid Cash — Partial Collect Cash */
    public const string PC = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PC';

    /** Partial Prepaid Credit — Partial Collect Cash */
    public const string PD = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PD';

    /** Partial Prepaid Credit Card — Partial Collect Cash */
    public const string PE = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PE';

    /** Partial Prepaid Credit Card — Partial Collect Credit Card */
    public const string PF = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PF';

    /** All Charges Prepaid by GBL */
    public const string PG = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PG';

    /** Partial Prepaid Credit Card — Partial Collect Credit */
    public const string PH = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PH';

    /** All Charges Prepaid Cash */
    public const string PP = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PP';

    /** All Charges Prepaid Credit */
    public const string PX = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PX';

    /** All Charges Prepaid by Credit Card */
    public const string PZ = 'https://onerecord.iata.org/ns/code-lists/ChargeCode#PZ';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CA' => self::CA,
        'CB' => self::CB,
        'CC' => self::CC,
        'CE' => self::CE,
        'CG' => self::CG,
        'CH' => self::CH,
        'CM' => self::CM,
        'CP' => self::CP,
        'CX' => self::CX,
        'CZ' => self::CZ,
        'NC' => self::NC,
        'NG' => self::NG,
        'NP' => self::NP,
        'NT' => self::NT,
        'NX' => self::NX,
        'NZ' => self::NZ,
        'PC' => self::PC,
        'PD' => self::PD,
        'PE' => self::PE,
        'PF' => self::PF,
        'PG' => self::PG,
        'PH' => self::PH,
        'PP' => self::PP,
        'PX' => self::PX,
        'PZ' => self::PZ,
    ];
}
