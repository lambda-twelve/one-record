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
 * Code list RadioactiveMaterialClassification: Restricted code list based on DGR 10.3.3 Source: DGR
 * 10.3.3 Nomenclature of radioactive material classification.
 */
final class RadioactiveMaterialClassification
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/RadioactiveMaterialClassification';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Low Dispersible */
    public const string LOW_DISPERSIBLE = 'https://onerecord.iata.org/ns/code-lists/RadioactiveMaterialClassification#LOW_DISPERSIBLE';

    /** Physical Chemical Form */
    public const string PHYSICAL_CHEMICAL_FORM = 'https://onerecord.iata.org/ns/code-lists/RadioactiveMaterialClassification#PHYSICAL_CHEMICAL_FORM';

    /** Special Form */
    public const string SPECIAL_FORM = 'https://onerecord.iata.org/ns/code-lists/RadioactiveMaterialClassification#SPECIAL_FORM';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'LOW_DISPERSIBLE' => self::LOW_DISPERSIBLE,
        'PHYSICAL_CHEMICAL_FORM' => self::PHYSICAL_CHEMICAL_FORM,
        'SPECIAL_FORM' => self::SPECIAL_FORM,
    ];
}
