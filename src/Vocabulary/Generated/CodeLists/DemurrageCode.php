<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated\CodeLists;

/** Code list DemurrageCode: Restricted code list based on RP 1654 Source: CSC RP 1654. */
final class DemurrageCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/DemurrageCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** BCC */
    public const string BCC = 'https://onerecord.iata.org/ns/code-lists/DemurrageCode#BCC';

    /** HHH */
    public const string HHH = 'https://onerecord.iata.org/ns/code-lists/DemurrageCode#HHH';

    /** XXX */
    public const string XXX = 'https://onerecord.iata.org/ns/code-lists/DemurrageCode#XXX';

    /** ZZZ */
    public const string ZZZ = 'https://onerecord.iata.org/ns/code-lists/DemurrageCode#ZZZ';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'BCC' => self::BCC,
        'HHH' => self::HHH,
        'XXX' => self::XXX,
        'ZZZ' => self::ZZZ,
    ];
}
