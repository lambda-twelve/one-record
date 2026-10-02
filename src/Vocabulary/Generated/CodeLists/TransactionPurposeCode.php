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
 * Code list TransactionPurposeCode: Restricted code list of purpose-of-transaction-codes Source:
 * CITES.
 */
final class TransactionPurposeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Breeding in captivity or artificial propagation */
    public const string B = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#B';

    /** Educational */
    public const string E = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#E';

    /** Botanical garden */
    public const string G = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#G';

    /** Hunting trophy */
    public const string H = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#H';

    /** Law enforcement / judicial / forensic */
    public const string L = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#L';

    /** Medical (including biomedical research) */
    public const string M = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#M';

    /** Reintroduction or introduction into the wild */
    public const string N = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#N';

    /** Personal */
    public const string P = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#P';

    /** Circus or travelling exhibition */
    public const string Q = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#Q';

    /** Scientific */
    public const string S = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#S';

    /** Commercial */
    public const string T = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#T';

    /** Zoo */
    public const string Z = 'https://onerecord.iata.org/ns/code-lists/TransactionPurposeCode#Z';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'B' => self::B,
        'E' => self::E,
        'G' => self::G,
        'H' => self::H,
        'L' => self::L,
        'M' => self::M,
        'N' => self::N,
        'P' => self::P,
        'Q' => self::Q,
        'S' => self::S,
        'T' => self::T,
        'Z' => self::Z,
    ];
}
