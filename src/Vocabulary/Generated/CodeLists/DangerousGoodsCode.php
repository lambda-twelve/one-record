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
 * Code list DangerousGoodsCode: Restricted code list corresponding to cXML code list 1.14 Dangerous
 * Goods Codes Source: Dangerous Goods Regulations, 46th Edition.
 */
final class DangerousGoodsCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Cargo Aircraft Only */
    public const string CAO = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#CAO';

    /** Lithium ion batteries excepted as per Section II of PI 965 */
    public const string EBI = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#EBI';

    /** Lithium metal batteries excepted as per Section II of PI 968 */
    public const string EBM = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#EBM';

    /** Lithium Ion Batteries otherwise excepted from the IATA DGR */
    public const string ELI = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#ELI';

    /** Lithium Metal Batteries otherwise excepted from the IATA DGR */
    public const string ELM = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#ELM';

    /** Dry Ice */
    public const string ICE = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#ICE';

    /** Magnetized Material */
    public const string MAG = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#MAG';

    /** Fully regulated lithium ion batteries (Class 9, UN 3480) as per Section IA and IB of PI 965 */
    public const string RBI = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RBI';

    /** Fully regulated lithium metal batteries (Class 9, UN 3090) as per Section IA and IB of PI 968 */
    public const string RBM = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RBM';

    /** Cryogenic Liquids */
    public const string RCL = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RCL';

    /** Corrosive */
    public const string RCM = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RCM';

    /** Explosives 1.3C */
    public const string RCX = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RCX';

    /** To be reserved for normally forbidden Explosives, Divisions 1.1, 1.2, 1.3, 1.4F, 1.5 and 1.6 */
    public const string REX = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#REX';

    /** Flammable Gas */
    public const string RFG = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RFG';

    /** Flammable Liquid */
    public const string RFL = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RFL';

    /** Flammable Solid */
    public const string RFS = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RFS';

    /** Dangerous When Wet */
    public const string RFW = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RFW';

    /** Explosives 1.3G */
    public const string RGX = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RGX';

    /** Infectious Substance */
    public const string RIS = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RIS';

    /** Fully Regulated Lithium Ion Batteries (Class 9) */
    public const string RLI = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RLI';

    /** Fully Regulated Lithium Metal Batteries (Class 9) */
    public const string RLM = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RLM';

    /** Miscellaneous Dangerous Goods */
    public const string RMD = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RMD';

    /** Non-Flammable Non-Toxic Gas */
    public const string RNG = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RNG';

    /** Organic Peroxide */
    public const string ROP = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#ROP';

    /** Oxidizer */
    public const string ROX = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#ROX';

    /** Toxic Substance */
    public const string RPB = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RPB';

    /** Toxic Gas */
    public const string RPG = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RPG';

    /** Radioactive Material Category I-White */
    public const string RRW = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RRW';

    /** Radioactive Material Categories II-Yellow and III-Yellow */
    public const string RRY = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RRY';

    /** Polymeric Beads */
    public const string RSB = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RSB';

    /** Spontaneously Combustible */
    public const string RSC = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RSC';

    /** Explosives 1.4B */
    public const string RXB = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RXB';

    /** Explosives 1.4C */
    public const string RXC = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RXC';

    /** Explosives 1.4D */
    public const string RXD = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RXD';

    /** Explosives 1.4E */
    public const string RXE = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RXE';

    /** Explosives 1.4G */
    public const string RXG = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RXG';

    /** Explosives 1.4S */
    public const string RXS = 'https://onerecord.iata.org/ns/code-lists/DangerousGoodsCode#RXS';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'CAO' => self::CAO,
        'EBI' => self::EBI,
        'EBM' => self::EBM,
        'ELI' => self::ELI,
        'ELM' => self::ELM,
        'ICE' => self::ICE,
        'MAG' => self::MAG,
        'RBI' => self::RBI,
        'RBM' => self::RBM,
        'RCL' => self::RCL,
        'RCM' => self::RCM,
        'RCX' => self::RCX,
        'REX' => self::REX,
        'RFG' => self::RFG,
        'RFL' => self::RFL,
        'RFS' => self::RFS,
        'RFW' => self::RFW,
        'RGX' => self::RGX,
        'RIS' => self::RIS,
        'RLI' => self::RLI,
        'RLM' => self::RLM,
        'RMD' => self::RMD,
        'RNG' => self::RNG,
        'ROP' => self::ROP,
        'ROX' => self::ROX,
        'RPB' => self::RPB,
        'RPG' => self::RPG,
        'RRW' => self::RRW,
        'RRY' => self::RRY,
        'RSB' => self::RSB,
        'RSC' => self::RSC,
        'RXB' => self::RXB,
        'RXC' => self::RXC,
        'RXD' => self::RXD,
        'RXE' => self::RXE,
        'RXG' => self::RXG,
        'RXS' => self::RXS,
    ];
}
