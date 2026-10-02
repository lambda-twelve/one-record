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
 * Code list DensityGroupCode: Restricted code list corresponding to cXML code list 2 Density Group
 * Codes.
 */
final class DensityGroupCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** 160kg per mc or 10 lbs per cf */
    public const string _0 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#0';

    /** 300 kg per mc or 18.6 lbs per cf */
    public const string _1 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#1';

    /** 950 kg per mc or 59.3 lbs per cf */
    public const string _10 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#10';

    /** 90 kg per mc or 5.6 lbs per cf */
    public const string _2 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#2';

    /** 120 kg per mc or 7.5 lbs per cf */
    public const string _3 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#3';

    /** 220 kg per mc or 13.8 lbs per cf */
    public const string _4 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#4';

    /** 60 kg per mc or 3.8 lbs per cf */
    public const string _5 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#5';

    /** 250 kg per mc or 15.6 lbs per cf */
    public const string _6 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#6';

    /** 400 kg per mc or 25 lbs per cf */
    public const string _8 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#8';

    /** 600 kg per mc or 37.5 lbs per cf */
    public const string _9 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#9';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        '0' => self::_0,
        '1' => self::_1,
        '10' => self::_10,
        '2' => self::_2,
        '3' => self::_3,
        '4' => self::_4,
        '5' => self::_5,
        '6' => self::_6,
        '8' => self::_8,
        '9' => self::_9,
    ];
}
