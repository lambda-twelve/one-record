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
 * Code list SpecialHandlingCode: Open code list corresponding to cXML code lists 1.16 Special Handling
 * Codes, 1.14 Dangerous Goods Codes and 1.103 Security Statuses. Note that the codes from 1.14 and
 * 1.103 have different IRI prefixes Source of DGR codes: Dangerous Goods Regulations, 46th Edition.
 */
final class SpecialHandlingCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = true;

    /** Active Temperature Controlled System */
    public const string ACT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ACT';

    /** Aircraft on Ground */
    public const string AOG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#AOG';

    /** Goods Attached to Air Waybill */
    public const string ATT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ATT';

    /** Live Animal */
    public const string AVI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#AVI';

    /** Outsized */
    public const string BIG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#BIG';

    /** Bulk Unitization Programme, Shipper/Consignee Handled Unit */
    public const string BUP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#BUP';

    /** Cargo Aircraft Only */
    public const string CAO = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#CAO';

    /** Cargo Attendant Accompanying Shipment */
    public const string CAT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#CAT';

    /** Cargo may be loaded in the passenger cabin */
    public const string CIC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#CIC';

    /** Cool Goods */
    public const string COL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#COL';

    /** Company Mail */
    public const string COM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#COM';

    /** Control Room Temperature +15°C to +25°C */
    public const string CRT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#CRT';

    /** Diplomatic Mail */
    public const string DIP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#DIP';

    /** e-freight Consignment with Accompanying Paper Documents */
    public const string EAP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#EAP';

    /** Foodstuffs */
    public const string EAT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#EAT';

    /** e-freight Consignment with No Accompanying Paper Documents */
    public const string EAW = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#EAW';

    /** Lithium ion batteries excepted as per Section II of PI 965 */
    public const string EBI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#EBI';

    /** Lithium metal batteries excepted as per Section II of PI 968 */
    public const string EBM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#EBM';

    /**
     * Consignment established with an electronically concluded cargo contract with no accompanying paper
     * airwaybill
     */
    public const string ECC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ECC';

    /** Consignment established with a paper air waybill contract being printed under an e-AWB agreement */
    public const string ECP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ECP';

    /** Lithium Ion Batteries otherwise excepted from the IATA DGR */
    public const string ELI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ELI';

    /** Lithium Metal Batteries otherwise excepted from the IATA DGR */
    public const string ELM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ELM';

    /** Electronic Monitoring Devices on/in Cargo/Container */
    public const string EMD = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#EMD';

    /** Extended Room Temperature +2°C to +25°C */
    public const string ERT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ERT';

    /** Undeveloped/Unexposed Film */
    public const string FIL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#FIL';

    /** Frozen Goods Subject to Veterinary/Phytosanitary Inspections */
    public const string FRI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#FRI';

    /** Frozen Goods */
    public const string FRO = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#FRO';

    /** Hanging Garments */
    public const string GOH = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#GOH';

    /** Heavy Cargo/150 kilograms and over per piece */
    public const string HEA = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#HEA';

    /** Hatching Eggs */
    public const string HEG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#HEG';

    /** Human Remains in Coffin */
    public const string HUM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#HUM';

    /** Dry Ice */
    public const string ICE = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ICE';

    /** Living Human Organs/Blood */
    public const string LHO = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#LHO';

    /** License Required */
    public const string LIC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#LIC';

    /** Magnetized Material */
    public const string MAG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#MAG';

    /** Mail */
    public const string MAL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#MAL';

    /** Munitions of War */
    public const string MUW = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#MUW';

    /** Cargo Has Not Been Secured Yet for Passenger or All-Cargo Aircraft */
    public const string NSC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#NSC';

    /** Non Stackable Cargo */
    public const string NST = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#NST';

    /** Newspapers, Magazines */
    public const string NWP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#NWP';

    /** Obnoxious Cargo */
    public const string OBX = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#OBX';

    /** Overhang Item */
    public const string OHG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#OHG';

    /** Passenger and Cargo */
    public const string PAC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PAC';

    /**
     * Hunting trophies, skin, hide and all articles made from or containing parts of species listed in the
     * CITES (Convention on International Trade in Endangered Species) appendices
     */
    public const string PEA = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PEA';

    /** Animal products for non-human consumption */
    public const string PEB = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PEB';

    /** Flowers */
    public const string PEF = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PEF';

    /** Meat */
    public const string PEM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PEM';

    /** Fruits and Vegetables */
    public const string PEP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PEP';

    /** Perishable Cargo */
    public const string PER = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PER';

    /** Fish/Seafood */
    public const string PES = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PES';

    /** Goods subject to phytosanitary inspections */
    public const string PHY = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PHY';

    /** Pharmaceuticals */
    public const string PIL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PIL';

    /** Passive Insulated Packaging */
    public const string PIP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#PIP';

    /** Quick Ramp Transfer */
    public const string QRT = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#QRT';

    /** Reserved Air Cargo */
    public const string RAC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RAC';

    /** Fully regulated lithium ion batteries (Class 9, UN 3480) as per Section IA and IB of PI 965 */
    public const string RBI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RBI';

    /** Fully regulated lithium metal batteries (Class 9, UN 3090) as per Section IA and IB of PI 968 */
    public const string RBM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RBM';

    /** Cryogenic Liquids */
    public const string RCL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RCL';

    /** Corrosive */
    public const string RCM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RCM';

    /** Explosives 1.3C */
    public const string RCX = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RCX';

    /** Diagnostic Specimens */
    public const string RDS = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RDS';

    /** Excepted Quantities of Dangerous Goods */
    public const string REQ = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#REQ';

    /** To be reserved for normally forbidden Explosives, Divisions 1.1, 1.2, 1.3, 1.4F, 1.5 and 1.6 */
    public const string REX = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#REX';

    /** Flammable Gas */
    public const string RFG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RFG';

    /** Flammable Liquid */
    public const string RFL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RFL';

    /** Flammable Solid */
    public const string RFS = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RFS';

    /** Dangerous When Wet */
    public const string RFW = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RFW';

    /** Explosives 1.3G */
    public const string RGX = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RGX';

    /** Infectious Substance */
    public const string RIS = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RIS';

    /** Fully Regulated Lithium Ion Batteries (Class 9) */
    public const string RLI = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RLI';

    /** Fully Regulated Lithium Metal Batteries (Class 9) */
    public const string RLM = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RLM';

    /** Miscellaneous Dangerous Goods */
    public const string RMD = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RMD';

    /** Non-Flammable Non-Toxic Gas */
    public const string RNG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RNG';

    /** Organic Peroxide */
    public const string ROP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ROP';

    /** Oxidizer */
    public const string ROX = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#ROX';

    /** Toxic Substance */
    public const string RPB = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RPB';

    /** Toxic Gas */
    public const string RPG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RPG';

    /** Excepted Quantities of Radioactive Material */
    public const string RRE = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RRE';

    /** Radioactive Material Category I-White */
    public const string RRW = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RRW';

    /** Radioactive Material Categories II-Yellow and III-Yellow */
    public const string RRY = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RRY';

    /** Polymeric Beads */
    public const string RSB = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RSB';

    /** Spontaneously Combustible */
    public const string RSC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RSC';

    /** Explosives 1.4B */
    public const string RXB = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RXB';

    /** Explosives 1.4C */
    public const string RXC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RXC';

    /** Explosives 1.4D */
    public const string RXD = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RXD';

    /** Explosives 1.4E */
    public const string RXE = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RXE';

    /** Explosives 1.4G */
    public const string RXG = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RXG';

    /** Explosives 1.4S */
    public const string RXS = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#RXS';

    /** Cargo Secure for All-Cargo Aircraft Only */
    public const string SCO = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SCO';

    /** Save Human Life */
    public const string SHL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SHL';

    /** Secure for Passenger, All-Cargo and All-Mail Aircraft in Accordance with High Risk Requirements */
    public const string SHR = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SHR';

    /** Laboratory Animals */
    public const string SPF = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SPF';

    /** Cargo Secure for Passenger and All-Cargo Aircraft */
    public const string SPX = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SPX';

    /** Surface Transportation */
    public const string SUR = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SUR';

    /** Sporting Weapons */
    public const string SWP = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#SWP';

    /** Valuable Cargo */
    public const string VAL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VAL';

    /** Very Important Cargo */
    public const string VIC = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VIC';

    /** Volume */
    public const string VOL = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VOL';

    /** Vulnerable Cargo */
    public const string VUN = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#VUN';

    /** Shipments of Wet Material not Packed in Watertight Containers */
    public const string WET = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#WET';

    /** Priority Small Package */
    public const string XPS = 'https://onerecord.iata.org/ns/code-lists/SpecialHandlingCode#XPS';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'ACT' => self::ACT,
        'AOG' => self::AOG,
        'ATT' => self::ATT,
        'AVI' => self::AVI,
        'BIG' => self::BIG,
        'BUP' => self::BUP,
        'CAO' => self::CAO,
        'CAT' => self::CAT,
        'CIC' => self::CIC,
        'COL' => self::COL,
        'COM' => self::COM,
        'CRT' => self::CRT,
        'DIP' => self::DIP,
        'EAP' => self::EAP,
        'EAT' => self::EAT,
        'EAW' => self::EAW,
        'EBI' => self::EBI,
        'EBM' => self::EBM,
        'ECC' => self::ECC,
        'ECP' => self::ECP,
        'ELI' => self::ELI,
        'ELM' => self::ELM,
        'EMD' => self::EMD,
        'ERT' => self::ERT,
        'FIL' => self::FIL,
        'FRI' => self::FRI,
        'FRO' => self::FRO,
        'GOH' => self::GOH,
        'HEA' => self::HEA,
        'HEG' => self::HEG,
        'HUM' => self::HUM,
        'ICE' => self::ICE,
        'LHO' => self::LHO,
        'LIC' => self::LIC,
        'MAG' => self::MAG,
        'MAL' => self::MAL,
        'MUW' => self::MUW,
        'NSC' => self::NSC,
        'NST' => self::NST,
        'NWP' => self::NWP,
        'OBX' => self::OBX,
        'OHG' => self::OHG,
        'PAC' => self::PAC,
        'PEA' => self::PEA,
        'PEB' => self::PEB,
        'PEF' => self::PEF,
        'PEM' => self::PEM,
        'PEP' => self::PEP,
        'PER' => self::PER,
        'PES' => self::PES,
        'PHY' => self::PHY,
        'PIL' => self::PIL,
        'PIP' => self::PIP,
        'QRT' => self::QRT,
        'RAC' => self::RAC,
        'RBI' => self::RBI,
        'RBM' => self::RBM,
        'RCL' => self::RCL,
        'RCM' => self::RCM,
        'RCX' => self::RCX,
        'RDS' => self::RDS,
        'REQ' => self::REQ,
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
        'RRE' => self::RRE,
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
        'SCO' => self::SCO,
        'SHL' => self::SHL,
        'SHR' => self::SHR,
        'SPF' => self::SPF,
        'SPX' => self::SPX,
        'SUR' => self::SUR,
        'SWP' => self::SWP,
        'VAL' => self::VAL,
        'VIC' => self::VIC,
        'VOL' => self::VOL,
        'VUN' => self::VUN,
        'WET' => self::WET,
        'XPS' => self::XPS,
    ];
}
