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
 * Code list ExplosiveCompatibilityGroupCode: Restricted code list based on DGR Table 3.1.A Source: DGR
 * Table 3.1.A Compatibility group for explosives.
 */
final class ExplosiveCompatibilityGroupCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Primary explosive substance (Hazard Division 1.1) */
    public const string A = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#A';

    /**
     * Article containing a primary explosive substance and not containing two or more effective protective
     * features. Some articles, such as detonators for blasting, detonator assemblies for blasting and
     * primers, cap type, are included, even though they do not contain primary explosives (Hazard Division
     * 1.1; 1.2; 1.4)
     */
    public const string B = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#B';

    /**
     * Propellant explosive substance or other deflagrating explosive substance or article containing such
     * explosive substance (Hazard Division 1.1; 1.2; 1.3; 1.4)
     */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#C';

    /**
     * Secondary detonating explosive substance or black powder or article containing a secondary
     * detonating explosive substance, in each case without means of initiation and without a propelling
     * charge or article containing a primary explosive substance and containing two or more effective
     * protective features (Hazard Division 1.1; 1.2; 1.4; 1.5)
     */
    public const string D = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#D';

    /**
     * Article containing a secondary detonating explosive substance, without means of initiation, with a
     * propelling charge (other than one containing a flammable liquid or gel or hypergolic liquids) or
     * without a propelling charge (Hazard Division 1.1; 1.2; 1.4)
     */
    public const string E = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#E';

    /**
     * Article containing a secondary detonating explosive substance, with its own means of initiation,
     * with a propelling charge (other than one containing a flammable liquid or gel or hypergolic liquids)
     * or without a propelling charge (Hazard Division 1.1; 1.2; 1.3; 1.4)
     */
    public const string F = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#F';

    /**
     * Pyrotechnic substance, or article containing a pyrotechnic substance, or article containing both an
     * explosive substance and an illuminating, incendiary, tear- or smoke-producing substance (other than
     * a water -activated article or one containing white phosphorus, phosphide, a pyrophoric substance, a
     * flammable liquid or get or hypergolic liquids) (Hazard Division 1.1; 1.2; 1.3; 1.4)
     */
    public const string G = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#G';

    /** Article containing both an explosive substance and white phosphorus (Hazard Division 1.2; 1.3) */
    public const string H = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#H';

    /**
     * Article containing both an explosive substance and a flammable liquid or gel (Hazard Division 1.1;
     * 1.2; 1.3)
     */
    public const string J = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#J';

    /** Article containing both an explosive substance and a toxic chemical agent (Hazard Division 1.1; 1.3) */
    public const string K = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#K';

    /**
     * Explosive article or substance containing an explosive substance and presenting a special risk (e.g.
     * due to water activation, or the presence of hypergolic liquids, phosphides or a pyrophoric
     * substance) and needing isolation of each type (Hazard Division 1.2; 1.2; 1.3)
     */
    public const string L = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#L';

    /** Article containing only extremely insensitive detonating substances (Hazard Division 1.6) */
    public const string N = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#N';

    /**
     * Article or substance so packed or designed that any hazardous effects arising from accidental
     * functioning are confined within the package unless the package has been degraded by fire, in which
     * case all blast or projection effects are limited to the extent that they do not significantly hinder
     * or prohibit fire fighting or other emergency response efforts in the immediate vicinity of the
     * package (Hazard Division 1.4)
     */
    public const string S = 'https://onerecord.iata.org/ns/code-lists/ExplosiveCompatibilityGroupCode#S';

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
        'J' => self::J,
        'K' => self::K,
        'L' => self::L,
        'N' => self::N,
        'S' => self::S,
    ];
}
