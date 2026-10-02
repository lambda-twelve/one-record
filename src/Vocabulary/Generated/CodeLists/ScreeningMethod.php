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
 * Code list ScreeningMethod: Restricted code list corresponding to cXML code list 1.102 Screening
 * Methods.
 */
final class ScreeningMethod
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Subjected to Any Other Means */
    public const string AOM = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#AOM';

    /** Cargo Metal Detection */
    public const string CMD = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#CMD';

    /** Explosive Detection Dogs */
    public const string EDD = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#EDD';

    /** Explosive Detection System */
    public const string EDS = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#EDS';

    /** Explosives Trace Detection Equipment - Particles or Vapor */
    public const string ETD = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#ETD';

    /** Physical Inspection and/or Hand Search */
    public const string PHS = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#PHS';

    /** Visualcheck */
    public const string VCK = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#VCK';

    /** X-ray Equipment */
    public const string XRY = 'https://onerecord.iata.org/ns/code-lists/ScreeningMethod#XRY';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'AOM' => self::AOM,
        'CMD' => self::CMD,
        'EDD' => self::EDD,
        'EDS' => self::EDS,
        'ETD' => self::ETD,
        'PHS' => self::PHS,
        'VCK' => self::VCK,
        'XRY' => self::XRY,
    ];
}
