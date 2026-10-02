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
 * Code list StatusCode: Restricted code list corresponding to cXML code list 1.18 Status Codes,
 * including DIS discrepancy codes.
 */
final class StatusCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/StatusCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** The consignment has arrived on a scheduled flight at this location */
    public const string ARR = 'https://onerecord.iata.org/ns/code-lists/StatusCode#ARR';

    /**
     * The arrival documentation has been physically delivered to the consignee or the consignee’s agent
     * on this date at this location
     */
    public const string AWD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#AWD';

    /** The arrival documentation has been physically received from a scheduled flight at this location */
    public const string AWR = 'https://onerecord.iata.org/ns/code-lists/StatusCode#AWR';

    /**
     * The consignment has been booked for transport between these locations on this scheduled date and
     * this flight
     */
    public const string BKD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#BKD';

    /** The consignment has been cleared by the Customs authorities on this date at this location */
    public const string CCD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#CCD';

    /** The consignment has been reported to the Customs authorities on this date at this location */
    public const string CRC = 'https://onerecord.iata.org/ns/code-lists/StatusCode#CRC';

    /**
     * The consignment has been physically delivered to the consignee’s door on this date at this
     * location
     */
    public const string DDL = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DDL';

    /**
     * The consignment has physically departed this location on this scheduled date and flight for
     * transport to the arrival location
     */
    public const string DEP = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DEP';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Definitely Loaded
     */
    public const string DIS_DFLD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_DFLD';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Found Mail Document
     */
    public const string DIS_FDAV = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_FDAV';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Found Air Waybill
     */
    public const string DIS_FDAW = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_FDAW';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Found Cargo
     */
    public const string DIS_FDCA = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_FDCA';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Found Mailbag
     */
    public const string DIS_FDMB = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_FDMB';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Missing Mail Document
     */
    public const string DIS_MSAV = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_MSAV';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Missing Air Waybill
     */
    public const string DIS_MSAW = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_MSAW';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Missing Cargo
     */
    public const string DIS_MSCA = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_MSCA';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Missing Mailbag
     */
    public const string DIS_MSMB = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_MSMB';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Offloaded
     */
    public const string DIS_OFLD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_OFLD';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Overcarried
     */
    public const string DIS_OVCD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_OVCD';

    /**
     * An apparent error has occurred, on this date at this location, with the handling of the consignment
     * or its documentation: Shortshipped
     */
    public const string DIS_SSPD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DIS_SSPD';

    /**
     * The consignment has been physically delivered to the consignee or the Consignee’s agent on this
     * date at this location
     */
    public const string DLV = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DLV';

    /** Documents Received by Handling Party */
    public const string DOC = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DOC';

    /**
     * The consignment has been physically picked up from the shipper’s door on this date at this
     * location
     */
    public const string DPU = 'https://onerecord.iata.org/ns/code-lists/StatusCode#DPU';

    /** Freight Into Warehouse Control */
    public const string FIW = 'https://onerecord.iata.org/ns/code-lists/StatusCode#FIW';

    /**
     * The consignment is on hand on this date at this location pending “ready for carriage”
     * determination
     */
    public const string FOH = 'https://onerecord.iata.org/ns/code-lists/StatusCode#FOH';

    /** Freight Out of Warehouse Control */
    public const string FOW = 'https://onerecord.iata.org/ns/code-lists/StatusCode#FOW';

    /**
     * The consignment has been manifested for this flight on this scheduled date for transport between
     * these locations
     */
    public const string MAN = 'https://onerecord.iata.org/ns/code-lists/StatusCode#MAN';

    /**
     * The consignee or the consignee’s agent has been notified, on this date at this location, of the
     * arrival of the consignment
     */
    public const string NFD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#NFD';

    /** Other Customs, Security and Regulatory Control Information */
    public const string OCI = 'https://onerecord.iata.org/ns/code-lists/StatusCode#OCI';

    /** Other Service Information */
    public const string OSI = 'https://onerecord.iata.org/ns/code-lists/StatusCode#OSI';

    /**
     * The consignment has been prepared for loading on this flight for transport between these locations
     * on this scheduled date
     */
    public const string PRE = 'https://onerecord.iata.org/ns/code-lists/StatusCode#PRE';

    /**
     * The consignment has been physically received from a given flight or surface transport of the given
     * airline
     */
    public const string RCF = 'https://onerecord.iata.org/ns/code-lists/StatusCode#RCF';

    /**
     * The consignment has been physically received from the shipper or the shipper’s agent and is
     * considered by the carrier as ready for carriage on this date at this location
     */
    public const string RCS = 'https://onerecord.iata.org/ns/code-lists/StatusCode#RCS';

    /** The consignment has been physically received from this carrier on this date at this location */
    public const string RCT = 'https://onerecord.iata.org/ns/code-lists/StatusCode#RCT';

    /** The consignment has been physically transferred to this carrier on this date at this location */
    public const string TFD = 'https://onerecord.iata.org/ns/code-lists/StatusCode#TFD';

    /** The consignment has been transferred to Customs/Government control */
    public const string TGC = 'https://onerecord.iata.org/ns/code-lists/StatusCode#TGC';

    /**
     * The consignment has been manifested and/or will be physically transferred to this carrier at this
     * location
     */
    public const string TRM = 'https://onerecord.iata.org/ns/code-lists/StatusCode#TRM';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'ARR' => self::ARR,
        'AWD' => self::AWD,
        'AWR' => self::AWR,
        'BKD' => self::BKD,
        'CCD' => self::CCD,
        'CRC' => self::CRC,
        'DDL' => self::DDL,
        'DEP' => self::DEP,
        'DIS_DFLD' => self::DIS_DFLD,
        'DIS_FDAV' => self::DIS_FDAV,
        'DIS_FDAW' => self::DIS_FDAW,
        'DIS_FDCA' => self::DIS_FDCA,
        'DIS_FDMB' => self::DIS_FDMB,
        'DIS_MSAV' => self::DIS_MSAV,
        'DIS_MSAW' => self::DIS_MSAW,
        'DIS_MSCA' => self::DIS_MSCA,
        'DIS_MSMB' => self::DIS_MSMB,
        'DIS_OFLD' => self::DIS_OFLD,
        'DIS_OVCD' => self::DIS_OVCD,
        'DIS_SSPD' => self::DIS_SSPD,
        'DLV' => self::DLV,
        'DOC' => self::DOC,
        'DPU' => self::DPU,
        'FIW' => self::FIW,
        'FOH' => self::FOH,
        'FOW' => self::FOW,
        'MAN' => self::MAN,
        'NFD' => self::NFD,
        'OCI' => self::OCI,
        'OSI' => self::OSI,
        'PRE' => self::PRE,
        'RCF' => self::RCF,
        'RCS' => self::RCS,
        'RCT' => self::RCT,
        'TFD' => self::TFD,
        'TGC' => self::TGC,
        'TRM' => self::TRM,
    ];
}
