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
 * Code list ParticipantIdentifier: Open code list corresponding to cXML code list 1.36 Participant
 * Identifiers.
 */
final class ParticipantIdentifier
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = true;

    /** Agent */
    public const string AGT = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#AGT';

    /** Airline */
    public const string AIR = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#AIR';

    /** Airport Authority */
    public const string APT = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#APT';

    /** Broker */
    public const string BRK = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#BRK';

    /** Commissionable Agent */
    public const string CAG = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#CAG';

    /** Consignee */
    public const string CNE = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#CNE';

    /** Customs */
    public const string CTM = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#CTM';

    /** Declarant */
    public const string DCL = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#DCL';

    /** Deconsolidator */
    public const string DEC = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#DEC';

    /** Freight Forwarder */
    public const string FFW = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#FFW';

    /** Ground Handling Agent */
    public const string GHA = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#GHA';

    /** Notify */
    public const string NFY = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#NFY';

    /** Post Office */
    public const string PTT = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#PTT';

    /** Shipper */
    public const string SHP = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#SHP';

    /** Trucker */
    public const string TRK = 'https://onerecord.iata.org/ns/code-lists/ParticipantIdentifier#TRK';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'AGT' => self::AGT,
        'AIR' => self::AIR,
        'APT' => self::APT,
        'BRK' => self::BRK,
        'CAG' => self::CAG,
        'CNE' => self::CNE,
        'CTM' => self::CTM,
        'DCL' => self::DCL,
        'DEC' => self::DEC,
        'FFW' => self::FFW,
        'GHA' => self::GHA,
        'NFY' => self::NFY,
        'PTT' => self::PTT,
        'SHP' => self::SHP,
        'TRK' => self::TRK,
    ];
}
