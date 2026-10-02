<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Spec;

/**
 * An endorsed release of the ONE Record standard: one API version, one data
 * model version and one code-list version, published together by IATA as a
 * tag of https://github.com/IATA-Cargo/ONE-Record.
 *
 * Adding a new edition is one case here plus one case in ApiVersion and, when
 * the data model changed, DataModelVersion. Everything else (negotiation,
 * server information, the vocabulary generator, the compliance matrix) reads
 * from these enums, so nothing else needs to know a version string.
 */
enum Edition: string
{
    case E2025_07 = '2025-07';
    case E2026_07 = '2026-07';

    /**
     * The commit each tag points at, so generated files and fixtures are
     * reproducible even if a tag were ever moved.
     */
    public function commit(): string
    {
        return match ($this) {
            self::E2025_07 => 'dfe7c38fbd104c4a85adfe1f6703c1ad87561928',
            self::E2026_07 => '079490714c16ef7f73e12a6449a9f36e1cf1e06e',
        };
    }

    public function apiVersion(): ApiVersion
    {
        return match ($this) {
            self::E2025_07 => ApiVersion::V2_2_0,
            self::E2026_07 => ApiVersion::V2_3_0,
        };
    }

    public function dataModelVersion(): DataModelVersion
    {
        return match ($this) {
            self::E2025_07 => DataModelVersion::V3_2,
            self::E2026_07 => DataModelVersion::V3_3,
        };
    }

    public function codeListVersion(): string
    {
        return match ($this) {
            self::E2025_07, self::E2026_07 => '1.1.0',
        };
    }

    /**
     * Folder of the ontology files inside the IATA repository at this edition.
     */
    public function ontologyFolder(): string
    {
        return $this->value . '-standard';
    }

    public static function latest(): self
    {
        $cases = self::cases();

        return $cases[\count($cases) - 1];
    }
}
