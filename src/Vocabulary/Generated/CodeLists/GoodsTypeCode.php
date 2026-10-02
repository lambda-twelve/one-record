<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list GoodsTypeCode: Restricted code list referring to the CITES appendices Source: CITES. */
final class GoodsTypeCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** Species included in Appendix I of CITES */
    public const string I = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeCode#I';

    /** Species included in Appendix II of CITES */
    public const string II = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeCode#II';

    /** Species included in Appendix III of CITES */
    public const string III = 'https://onerecord.iata.org/ns/code-lists/GoodsTypeCode#III';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'I' => self::I,
        'II' => self::II,
        'III' => self::III,
    ];
}
