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
 * Code list AircraftPossibilityCode: Restricted code list corresponding to cXML code list 1.46
 * Aircraft Possibility Codes.
 */
final class AircraftPossibilityCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Pure freighter flight carrying Loose Load Cargo */
    public const string BBF = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#BBF';

    /** Mixed configuration (Combi) aircraft carrying Loose Load Cargo on the passenger deck */
    public const string BBQ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#BBQ';

    /** Truck carrying Loose Load Cargo */
    public const string BBV = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#BBV';

    /** Pure freighter flight carrying Containerized Cargo (ULDs) */
    public const string LLF = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LLF';

    /** Passenger flight operated by wide-bodied aircraft carrying Containerized (ULDs) */
    public const string LLJ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LLJ';

    /** Mixed configuration (Combi) aircraft carrying Containerized Cargo (ULDs) on the passenger deck */
    public const string LLQ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LLQ';

    /** Truck carrying Containerized Cargo (ULDs) */
    public const string LLV = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LLV';

    /** Pure freighter flight carrying Containerized (ULDs)/Palletized Cargo */
    public const string LPF = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LPF';

    /** Passenger flight operated by wide-bodied aircraft carrying Containerized (ULDs)/ Palletized Cargo */
    public const string LPJ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LPJ';

    /**
     * Mixed configuration (Combi) aircraft carrying Containerized (ULDs)/Palletized Cargo on the passenger
     * deck
     */
    public const string LPQ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LPQ';

    /** Truck carrying Containerized (ULDs)/Palletized Cargo */
    public const string LPV = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#LPV';

    /** Pure freighter flight carrying Palletized Cargo */
    public const string PPF = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#PPF';

    /** Passenger flight operated by wide-bodied aircraft carrying Palletized Cargo */
    public const string PPJ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#PPJ';

    /** Mixed configuration aircraft carrying Palletized Cargo on the passenger deck */
    public const string PPQ = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#PPQ';

    /** Truck carrying Palletized Cargo */
    public const string PPV = 'https://onerecord.iata.org/ns/code-lists/AircraftPossibilityCode#PPV';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'BBF' => self::BBF,
        'BBQ' => self::BBQ,
        'BBV' => self::BBV,
        'LLF' => self::LLF,
        'LLJ' => self::LLJ,
        'LLQ' => self::LLQ,
        'LLV' => self::LLV,
        'LPF' => self::LPF,
        'LPJ' => self::LPJ,
        'LPQ' => self::LPQ,
        'LPV' => self::LPV,
        'PPF' => self::PPF,
        'PPJ' => self::PPJ,
        'PPQ' => self::PPQ,
        'PPV' => self::PPV,
    ];
}
