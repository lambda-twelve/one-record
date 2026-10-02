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
 * Code list MovementIndicator: NOT FINAL YET - Open code list corresponding to cXML code list 1.92
 * Movement Indicators.
 */
final class MovementIndicator
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Actual Arrival (Touchdown) */
    public const string AA = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AA';

    /** Actual On-block */
    public const string AB = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AB';

    /** Actual Departure (Take off) */
    public const string AD = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AD';

    /** Actual gate in time - Relates to gate passing of trucks */
    public const string AG = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AG';

    /** Actual gate out time - Relates t gate passing of trucks */
    public const string AH = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AH';

    /** Actual end of unloading time */
    public const string AK = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AK';

    /** Actual end of loading time */
    public const string AL = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AL';

    /** Actual Off-block */
    public const string AO = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AO';

    /** Actual driver reporting time */
    public const string AR = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#AR';

    /** Cancellation */
    public const string CN = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#CN';

    /** Doc Arrival */
    public const string DA = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#DA';

    /** Delayed */
    public const string DL = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#DL';

    /** Diversion */
    public const string DV = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#DV';

    /** Estimated Arrival (Touchdown) */
    public const string EA = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#EA';

    /** Estimated On-block */
    public const string EB = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#EB';

    /** Estimated Departure (Take off) */
    public const string ED = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#ED';

    /** Estimated end of unloading time */
    public const string EK = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#EK';

    /** Estimated end of loading time */
    public const string EL = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#EL';

    /** Estimated Off-block */
    public const string EO = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#EO';

    /** Estimated driver reporting time */
    public const string ER = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#ER';

    /** Force Return */
    public const string FR = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#FR';

    /** Next Information */
    public const string NI = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#NI';

    /**
     * Pre-announcement of the truck - to enable to pre-announce data (driver name, license plates, etc.)
     * to GHA at departure station
     */
    public const string PA = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#PA';

    /** Return to RAMP */
    public const string RR = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#RR';

    /** Scheduled Arrival */
    public const string SA = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#SA';

    /** Scheduled Departure */
    public const string SD = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#SD';

    /** Scheduled end of unloading time */
    public const string SK = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#SK';

    /** Scheduled end of loading time */
    public const string SL = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#SL';

    /** Scheduled latest driver reporting time for collection and/or delivery */
    public const string SR = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#SR';

    /** Scheduled earliest driver reporting time for collection and/or delivery */
    public const string SS = 'https://onerecord.iata.org/ns/code-lists/MovementIndicator#SS';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'AA' => self::AA,
        'AB' => self::AB,
        'AD' => self::AD,
        'AG' => self::AG,
        'AH' => self::AH,
        'AK' => self::AK,
        'AL' => self::AL,
        'AO' => self::AO,
        'AR' => self::AR,
        'CN' => self::CN,
        'DA' => self::DA,
        'DL' => self::DL,
        'DV' => self::DV,
        'EA' => self::EA,
        'EB' => self::EB,
        'ED' => self::ED,
        'EK' => self::EK,
        'EL' => self::EL,
        'EO' => self::EO,
        'ER' => self::ER,
        'FR' => self::FR,
        'NI' => self::NI,
        'PA' => self::PA,
        'RR' => self::RR,
        'SA' => self::SA,
        'SD' => self::SD,
        'SK' => self::SK,
        'SL' => self::SL,
        'SR' => self::SR,
        'SS' => self::SS,
    ];
}
