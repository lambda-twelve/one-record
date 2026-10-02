<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * A local graph after URIs were minted: the objects to publish, keyed by the
 * local key the host knows them by, so it can record where each one went.
 */
final readonly class ResolvedGraph
{
    /**
     * @param array<string, LogisticsObject> $objects local key => object
     */
    public function __construct(
        public string $rootKey,
        public array $objects,
    ) {
        if (!isset($objects[$rootKey])) {
            throw new ModelException(\sprintf('The resolved graph has no object for its root key "%s".', $rootKey));
        }
    }

    public function root(): LogisticsObject
    {
        return $this->objects[$this->rootKey];
    }

    public function get(string $key): LogisticsObject
    {
        return $this->objects[$key] ?? throw new ModelException(\sprintf('No object with key "%s".', $key));
    }

    /**
     * @return array<string, Iri> local key => URI
     */
    public function iris(): array
    {
        return array_map(static fn(LogisticsObject $o): Iri => $o->iri, $this->objects);
    }
}
