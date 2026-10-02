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
 * Code list TransportMeansServiceType: Restricted code list of possible transport means in transport
 * legs in carrier bookings.
 */
final class TransportMeansServiceType
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/TransportMeansServiceType';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Transport leg performed by freighter aircraft */
    public const string FREIGHTER = 'https://onerecord.iata.org/ns/code-lists/TransportMeansServiceType#FREIGHTER';

    /** Transport leg performed by mixed configuration combi aircraft */
    public const string MIXED_CONFIGURATION_COMBI = 'https://onerecord.iata.org/ns/code-lists/TransportMeansServiceType#MIXED_CONFIGURATION_COMBI';

    /** Transport leg performed by passenger aircraft */
    public const string PASSENGER = 'https://onerecord.iata.org/ns/code-lists/TransportMeansServiceType#PASSENGER';

    /** Transport leg performed by truck */
    public const string TRUCK = 'https://onerecord.iata.org/ns/code-lists/TransportMeansServiceType#TRUCK';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'FREIGHTER' => self::FREIGHTER,
        'MIXED_CONFIGURATION_COMBI' => self::MIXED_CONFIGURATION_COMBI,
        'PASSENGER' => self::PASSENGER,
        'TRUCK' => self::TRUCK,
    ];
}
