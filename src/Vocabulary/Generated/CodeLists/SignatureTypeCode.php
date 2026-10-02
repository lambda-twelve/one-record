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
 * Code list SignatureTypeCode: Restricted code list of governmental action in CITES context Source:
 * CITES.
 */
final class SignatureTypeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/SignatureTypeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Detention */
    public const string DETENTION = 'https://onerecord.iata.org/ns/code-lists/SignatureTypeCode#DETENTION';

    /** Fumigation */
    public const string FUMIGATION = 'https://onerecord.iata.org/ns/code-lists/SignatureTypeCode#FUMIGATION';

    /** Inspection */
    public const string INSPECTION = 'https://onerecord.iata.org/ns/code-lists/SignatureTypeCode#INSPECTION';

    /** Security */
    public const string SECURITY = 'https://onerecord.iata.org/ns/code-lists/SignatureTypeCode#SECURITY';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'DETENTION' => self::DETENTION,
        'FUMIGATION' => self::FUMIGATION,
        'INSPECTION' => self::INSPECTION,
        'SECURITY' => self::SECURITY,
    ];
}
