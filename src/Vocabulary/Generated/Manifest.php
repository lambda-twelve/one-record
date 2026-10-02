<?php

/*
 * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.
 *
 * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across
 * the editions listed in Manifest::EDITIONS at their pinned commits.
 */

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary\Generated;

/** Provenance of the generated vocabulary. */
final class Manifest
{
    /**
     * Every edition merged into this vocabulary, oldest first, with the commits the ontologies and the specification text were read from.
     *
     * @var array<string, array{ontologyCommit: string, documentationCommit: string, apiVersion: string, dataModelVersion: string, codeListVersion: string, versionInfo: array{cargo: string, api: string, codeLists: string}, files: array{cargo: string, api: string, codeLists: string}}>
     */
    public const array EDITIONS = [
        '2025-07' => [
            'ontologyCommit' => 'ad5f40d539b29692881672cf91878a4d2dbe59c2',
            'documentationCommit' => 'dfe7c38fbd104c4a85adfe1f6703c1ad87561928',
            'apiVersion' => '2.2.0',
            'dataModelVersion' => '3.2',
            'codeListVersion' => '1.1.0',
            'versionInfo' => [
                'cargo' => '3.2',
                'api' => '2.2.0',
                'codeLists' => '1.1.0',
            ],
            'files' => [
                'cargo' => 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/ad5f40d539b29692881672cf91878a4d2dbe59c2/2025-07-standard/Data-Model/IATA-1R-DM-Ontology.ttl',
                'api' => 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/ad5f40d539b29692881672cf91878a4d2dbe59c2/2025-07-standard/API-Security/ONE-Record-API-Ontology.ttl',
                'codeLists' => 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/ad5f40d539b29692881672cf91878a4d2dbe59c2/2025-07-standard/Data-Model/IATA-1R-CL-Ontology.ttl',
            ],
        ],
        '2026-07' => [
            'ontologyCommit' => 'ad5f40d539b29692881672cf91878a4d2dbe59c2',
            'documentationCommit' => '079490714c16ef7f73e12a6449a9f36e1cf1e06e',
            'apiVersion' => '2.3.0',
            'dataModelVersion' => '3.3',
            'codeListVersion' => '1.1.0',
            'versionInfo' => [
                'cargo' => '3.3.0',
                'api' => '2.3.0',
                'codeLists' => '1.1.0',
            ],
            'files' => [
                'cargo' => 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/ad5f40d539b29692881672cf91878a4d2dbe59c2/2026-07-standard/Data-Model/IATA-1R-DM-Ontology.ttl',
                'api' => 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/ad5f40d539b29692881672cf91878a4d2dbe59c2/2026-07-standard/API-Security/ONE-Record-API-Ontology.ttl',
                'codeLists' => 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/ad5f40d539b29692881672cf91878a4d2dbe59c2/2026-07-standard/Data-Model/IATA-1R-CL-Ontology.ttl',
            ],
        ],
    ];

    public const string LATEST_EDITION = '2026-07';
}
