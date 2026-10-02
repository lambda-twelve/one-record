<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Model\ResolvedGraph;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * What publishing a graph did to each object, keyed by the host's local key.
 */
final readonly class PublishResult
{
    public const string CREATED = 'created';
    public const string UPDATED = 'updated';
    public const string UNCHANGED = 'unchanged';

    /**
     * @param array<string, self::CREATED|self::UPDATED|self::UNCHANGED> $outcomes
     */
    public function __construct(
        public ResolvedGraph $graph,
        public array $outcomes,
    ) {}

    public function root(): Iri
    {
        return $this->graph->root()->iri;
    }

    /**
     * @return array<string, Iri>
     */
    public function iris(): array
    {
        return $this->graph->iris();
    }

    public function changedAnything(): bool
    {
        return array_filter($this->outcomes, static fn(string $o): bool => $o !== self::UNCHANGED) !== [];
    }
}
