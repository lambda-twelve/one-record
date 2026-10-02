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
 * Code list RegulatedEntityCategoryCode: Full detailed descriptions for RA, KC & AC are contained in
 * Cargo Services Conference Recommended Practice 1630 CARGO SECURITY Restricted code list of regulated
 * entity categories, partially corresponding to cXML code list 1.100 Customs, Security and Regulatory
 * Control Information Identifiers.
 */
final class RegulatedEntityCategoryCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/RegulatedEntityCategoryCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Aircraft Operator */
    public const string AO = 'https://onerecord.iata.org/ns/code-lists/RegulatedEntityCategoryCode#AO';

    /** Known Consignor (consignor for both passenger and all cargo aircraft only) */
    public const string KC = 'https://onerecord.iata.org/ns/code-lists/RegulatedEntityCategoryCode#KC';

    /** Regulated Agent */
    public const string RA = 'https://onerecord.iata.org/ns/code-lists/RegulatedEntityCategoryCode#RA';

    /** Regulated Carrier */
    public const string RC = 'https://onerecord.iata.org/ns/code-lists/RegulatedEntityCategoryCode#RC';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'AO' => self::AO,
        'KC' => self::KC,
        'RA' => self::RA,
        'RC' => self::RC,
    ];
}
