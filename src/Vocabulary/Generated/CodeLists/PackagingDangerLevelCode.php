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
 * Code list PackagingDangerLevelCode: Restricted code lists for indication of the relative degree of
 * danger presented by substances within a class or division.
 */
final class PackagingDangerLevelCode
{
    public const string IRI = 'https://onerecord.iata.org/ns/code-lists/PackagingDangerLevelCode';

    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */
    public const bool OPEN = false;

    /** High danger */
    public const string I = 'https://onerecord.iata.org/ns/code-lists/PackagingDangerLevelCode#I';

    /** Medium danger */
    public const string II = 'https://onerecord.iata.org/ns/code-lists/PackagingDangerLevelCode#II';

    /** Low danger */
    public const string III = 'https://onerecord.iata.org/ns/code-lists/PackagingDangerLevelCode#III';

    /** @var array<string, string> code => IRI */
    public const array ALL = [
        'I' => self::I,
        'II' => self::II,
        'III' => self::III,
    ];
}
