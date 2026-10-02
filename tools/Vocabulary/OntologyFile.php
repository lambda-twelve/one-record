<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Spec\Edition;

/**
 * The three ontology files IATA publishes per edition, and where they live in
 * the repository (https://github.com/IATA-Cargo/ONE-Record).
 */
enum OntologyFile: string
{
    case Cargo = 'Data-Model/IATA-1R-DM-Ontology.ttl';
    case CodeLists = 'Data-Model/IATA-1R-CL-Ontology.ttl';
    case Api = 'API-Security/ONE-Record-API-Ontology.ttl';

    public function pathIn(Edition $edition): string
    {
        return $edition->ontologyFolder() . '/' . $this->value;
    }

    public function rawUrl(Edition $edition): string
    {
        return 'https://raw.githubusercontent.com/IATA-Cargo/ONE-Record/' . $edition->ontologyCommit() . '/' . $this->pathIn($edition);
    }

    public function basename(): string
    {
        return basename($this->value);
    }
}
