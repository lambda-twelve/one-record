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
 * Code list ModeCode: Restricted Code List of mode codes, UNECE Recommendation No. 19 Source:
 * TRADE/CEFACT/21/19.
 */
final class ModeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ModeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Indicates the transport mode to be Air Transport (4) */
    public const string AIR_TRANSPORT = 'https://onerecord.iata.org/ns/code-lists/ModeCode#AIR_TRANSPORT';

    /** Indicates that the transport mode is a Fixed Transport Installation (7) */
    public const string FIXED_TRANSPORT_INSTALLATION = 'https://onerecord.iata.org/ns/code-lists/ModeCode#FIXED_TRANSPORT_INSTALLATION';

    /** Indicates that the transport mode to be Inland Water Transport (8) */
    public const string INLAND_WATER_TRANSPORT = 'https://onerecord.iata.org/ns/code-lists/ModeCode#INLAND_WATER_TRANSPORT';

    /** Indicates the transport mode to be Mail (5) */
    public const string MAIL = 'https://onerecord.iata.org/ns/code-lists/ModeCode#MAIL';

    /** Indicates the transport mode to be Maritime Transport (1) */
    public const string MARITIME_TRANSPORT = 'https://onerecord.iata.org/ns/code-lists/ModeCode#MARITIME_TRANSPORT';

    /** Indicates a Multimodal Transport (6) */
    public const string MULTIMODAL_TRANSPORT = 'https://onerecord.iata.org/ns/code-lists/ModeCode#MULTIMODAL_TRANSPORT';

    /** Indicates the transport mode to be Rail Transport (2) */
    public const string RAIL_TRANSPORT = 'https://onerecord.iata.org/ns/code-lists/ModeCode#RAIL_TRANSPORT';

    /** Indicates the transport mode to be Road Transport (3) */
    public const string ROAD_TRANSPORT = 'https://onerecord.iata.org/ns/code-lists/ModeCode#ROAD_TRANSPORT';

    /** Indicates that no transport mode is applicable (9) */
    public const string TRANSPORT_MODE_NOT_APPLICABLE = 'https://onerecord.iata.org/ns/code-lists/ModeCode#TRANSPORT_MODE_NOT_APPLICABLE';

    /** Indicates that the Transport Mode is not specified (0) */
    public const string TRANSPORT_MODE_NOT_SPECIFIED = 'https://onerecord.iata.org/ns/code-lists/ModeCode#TRANSPORT_MODE_NOT_SPECIFIED';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'AIR_TRANSPORT' => self::AIR_TRANSPORT,
        'FIXED_TRANSPORT_INSTALLATION' => self::FIXED_TRANSPORT_INSTALLATION,
        'INLAND_WATER_TRANSPORT' => self::INLAND_WATER_TRANSPORT,
        'MAIL' => self::MAIL,
        'MARITIME_TRANSPORT' => self::MARITIME_TRANSPORT,
        'MULTIMODAL_TRANSPORT' => self::MULTIMODAL_TRANSPORT,
        'RAIL_TRANSPORT' => self::RAIL_TRANSPORT,
        'ROAD_TRANSPORT' => self::ROAD_TRANSPORT,
        'TRANSPORT_MODE_NOT_APPLICABLE' => self::TRANSPORT_MODE_NOT_APPLICABLE,
        'TRANSPORT_MODE_NOT_SPECIFIED' => self::TRANSPORT_MODE_NOT_SPECIFIED,
    ];
}
