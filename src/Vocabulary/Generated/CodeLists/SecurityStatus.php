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
 * Code list SecurityStatus: Restricted code list corresponding to cXML code list 1.103 Security
 * Statuses.
 */
final class SecurityStatus
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/SecurityStatus';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Cargo Has Not Been Secured Yet for Passenger or All-Cargo Aircraft */
    public const string NSC = 'https://onerecord.iata.org/ns/code-lists/SecurityStatus#NSC';

    /** Cargo Secure for All-Cargo Aircraft Only */
    public const string SCO = 'https://onerecord.iata.org/ns/code-lists/SecurityStatus#SCO';

    /** Secure for Passenger, All-Cargo and All-Mail Aircraft in Accordance with High Risk Requirements */
    public const string SHR = 'https://onerecord.iata.org/ns/code-lists/SecurityStatus#SHR';

    /** Cargo Secure for Passenger and All-Cargo Aircraft */
    public const string SPX = 'https://onerecord.iata.org/ns/code-lists/SecurityStatus#SPX';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'NSC' => self::NSC,
        'SCO' => self::SCO,
        'SHR' => self::SHR,
        'SPX' => self::SPX,
    ];
}
