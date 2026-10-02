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
 * Code list GoodsTypeExtensionCode: Restricted code list referring to the CITES source codes Source:
 * CITES.
 */
final class GoodsTypeExtensionCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Artificially propagated plant */
    public const string A = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#A';

    /** Bred in captivity */
    public const string C = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#C';

    /** Captive-bred animal or artificially propagated plant */
    public const string D = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#D';

    /** Born in captivity */
    public const string F = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#F';

    /** Confiscated or seized */
    public const string I = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#I';

    /** Pre-Convention */
    public const string O = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#O';

    /** Ranched animal */
    public const string R = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#R';

    /** Unknown */
    public const string U = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#U';

    /** Wild */
    public const string W = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#W';

    /** Marine environment */
    public const string X = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeExtensionCode#X';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'A' => self::A,
        'C' => self::C,
        'D' => self::D,
        'F' => self::F,
        'I' => self::I,
        'O' => self::O,
        'R' => self::R,
        'U' => self::U,
        'W' => self::W,
        'X' => self::X,
    ];
}
