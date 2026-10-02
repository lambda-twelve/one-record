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
 * Code list ScreeningExemption: Restricted code list corresponding to cXML code list 1.104 Screening
 * Exemptions.
 */
final class ScreeningExemption
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Bio-Medical Samples */
    public const string BIOM = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#BIOM';

    /** Diplomatic Bags or Diplomatic Mail */
    public const string DIPL = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#DIPL';

    /** Life-Saving Materials (Save Human Life) */
    public const string LFSM = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#LFSM';

    /** Mail */
    public const string MAIL = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#MAIL';

    /** Nuclear Material */
    public const string NUCL = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#NUCL';

    /** Small Undersized Shipments */
    public const string SMUS = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#SMUS';

    /** Transfer or Transshipment */
    public const string TRNS = 'https://onerecord.iata.org/ns/code-lists/ScreeningExemption#TRNS';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'BIOM' => self::BIOM,
        'DIPL' => self::DIPL,
        'LFSM' => self::LFSM,
        'MAIL' => self::MAIL,
        'NUCL' => self::NUCL,
        'SMUS' => self::SMUS,
        'TRNS' => self::TRNS,
    ];
}
