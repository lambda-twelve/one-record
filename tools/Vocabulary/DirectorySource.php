<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Spec\Edition;
use RuntimeException;

/**
 * Reads ontology files from a local directory laid out as <dir>/<edition tag>/<file basename>.
 * Used by tests with small hand-written ontologies, and by `--source=` for offline runs.
 */
final class DirectorySource implements OntologySource
{
    public function __construct(private readonly string $directory) {}

    public function fetch(Edition $edition, OntologyFile $file): string
    {
        $path = $this->directory . '/' . $edition->value . '/' . $file->basename();
        if (!is_file($path)) {
            throw new RuntimeException(\sprintf('Ontology file not found: %s', $path));
        }

        return (string) file_get_contents($path);
    }
}
