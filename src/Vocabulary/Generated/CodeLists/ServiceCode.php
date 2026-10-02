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
 * Code list ServiceCode: Restricted code list corresponding to cXML code list 1.38 Service Codes
 * Source: CSC Resolutions Manual, 25th Edition, Recommended Practice 1600d.
 */
final class ServiceCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ServiceCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Airport-to-Airport */
    public const string A = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#A';

    /** Service Shipment */
    public const string B = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#B';

    /** Company Material */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#C';

    /** Door-to-Door Service */
    public const string D = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#D';

    /** Airport-to-Door */
    public const string E = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#E';

    /** Flight Specific */
    public const string F = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#F';

    /** Door-to-Airport */
    public const string G = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#G';

    /** Company Mail */
    public const string H = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#H';

    /** Diplomatic Mail */
    public const string I = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#I';

    /** Priority Service */
    public const string J = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#J';

    /** Small Package Service */
    public const string P = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#P';

    /** Substitute Truck */
    public const string S = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#S';

    /** Charter */
    public const string T = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#T';

    /** Express Shipments */
    public const string X = 'https://onerecord.iata.org/ns/code-lists/ServiceCode#X';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'A' => self::A,
        'B' => self::B,
        'C' => self::C,
        'D' => self::D,
        'E' => self::E,
        'F' => self::F,
        'G' => self::G,
        'H' => self::H,
        'I' => self::I,
        'J' => self::J,
        'P' => self::P,
        'S' => self::S,
        'T' => self::T,
        'X' => self::X,
    ];
}
