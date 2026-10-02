<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Spec\Edition;
use RuntimeException;

/**
 * Downloads ontology files from GitHub at the edition's pinned commit, keeping
 * a copy per commit so repeated runs (and the CI regeneration check) do not
 * depend on the network once primed. A commit never changes, so the cache
 * never goes stale.
 */
final class GitHubSource implements OntologySource
{
    public function __construct(private readonly string $cacheDir) {}

    public function fetch(Edition $edition, OntologyFile $file): string
    {
        // Editions may share a commit (they live in different folders of the same tree), so the path keeps the folder.
        $path = $this->cacheDir . '/' . $edition->ontologyCommit() . '/' . $file->pathIn($edition);
        if (is_file($path)) {
            return (string) file_get_contents($path);
        }

        $url = $file->rawUrl($edition);
        $context = stream_context_create(['http' => ['timeout' => 60, 'user_agent' => 'lambda-twelve/one-record vocabulary generator']]);
        $content = @file_get_contents($url, false, $context);
        if ($content === false || $content === '') {
            throw new RuntimeException(\sprintf('Could not download %s', $url));
        }

        if (!is_dir(\dirname($path)) && !mkdir(\dirname($path), 0o775, true) && !is_dir(\dirname($path))) {
            throw new RuntimeException(\sprintf('Could not create cache directory %s', \dirname($path)));
        }
        file_put_contents($path, $content);

        return $content;
    }
}
