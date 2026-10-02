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
 * Code list SignatoryRole: Restricted code list indicating the role of the signatory in CITES context
 * Source: CITES.
 */
final class SignatoryRole
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/SignatoryRole';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Applicant */
    public const string APPLICANT = 'https://onerecord.iata.org/ns/code-lists/SignatoryRole#APPLICANT';

    /** Examining Authority */
    public const string EXAMINING_AUTHORITY = 'https://onerecord.iata.org/ns/code-lists/SignatoryRole#EXAMINING_AUTHORITY';

    /** Issuing Authority */
    public const string ISSUING_AUTHORITY = 'https://onerecord.iata.org/ns/code-lists/SignatoryRole#ISSUING_AUTHORITY';

    /** Management Authority */
    public const string MANAGEMENT_AUTHORITY = 'https://onerecord.iata.org/ns/code-lists/SignatoryRole#MANAGEMENT_AUTHORITY';

    /** Permit Issuer */
    public const string PERMIT_ISSUER = 'https://onerecord.iata.org/ns/code-lists/SignatoryRole#PERMIT_ISSUER';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'APPLICANT' => self::APPLICANT,
        'EXAMINING_AUTHORITY' => self::EXAMINING_AUTHORITY,
        'ISSUING_AUTHORITY' => self::ISSUING_AUTHORITY,
        'MANAGEMENT_AUTHORITY' => self::MANAGEMENT_AUTHORITY,
        'PERMIT_ISSUER' => self::PERMIT_ISSUER,
    ];
}
