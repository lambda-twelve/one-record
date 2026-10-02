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
 * Code list SpaceAllocationCode: Restricted code list corresponding to CIMP code list 1.7 Space
 * Allocation Codes.
 */
final class SpaceAllocationCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Action code - Selling Space Allocation Against Allotment */
    public const string CA = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#CA';

    /** Advice Code - Cancellation Noted */
    public const string CN = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#CN';

    /** Status Code - Holding Confirmed */
    public const string HK = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#HK';

    /** Status Code - Holding Wait List */
    public const string HL = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#HL';

    /** Status Code - Have Requested Space Allocation */
    public const string HN = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#HN';

    /** Advice Code - Confirming */
    public const string KK = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#KK';

    /** Advice Code - Wait List */
    public const string LL = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#LL';

    /** Action code - Requesting Space Allocation, if Not Available Will Accept Alternative */
    public const string NA = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#NA';

    /** Action Code - Requesting Space Allocation, for Wait List */
    public const string NL = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#NL';

    /** Action Code - Requesting Space Allocation, Will Not Accept Alternative */
    public const string NN = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#NN';

    /** Action Code - Reporting Sale */
    public const string SS = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#SS';

    /** Advice Code - Unable, Flight Does Not Operate */
    public const string UN = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#UN';

    /** Advice Code - Unable */
    public const string UU = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#UU';

    /** Action Code - Cancel Any Previous Space Allocation */
    public const string XX = 'https://onerecord.iata.org/ns/code-lists/SpaceAllocationCode#XX';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CA' => self::CA,
        'CN' => self::CN,
        'HK' => self::HK,
        'HL' => self::HL,
        'HN' => self::HN,
        'KK' => self::KK,
        'LL' => self::LL,
        'NA' => self::NA,
        'NL' => self::NL,
        'NN' => self::NN,
        'SS' => self::SS,
        'UN' => self::UN,
        'UU' => self::UU,
        'XX' => self::XX,
    ];
}
