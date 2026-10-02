<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Model\Builder\ObjectBuilder;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Triple;

/**
 * A set of logistics objects that refer to each other by local key, before
 * any of them has a public URI.
 *
 * A host maps its business data into one of these (a waybill, its shipment,
 * the pieces, the parties), then resolves it with an IriMinter when
 * publishing. Until then every object's subject is `local:<key>` and
 * cross-references are `local:<other key>`, so the graph is plain data that
 * needs no server to build or test.
 */
final class LocalGraph
{
    /** @var array<string, Graph> local key => the object's triples, subject local:<key> */
    private array $objects = [];

    private ?string $root = null;

    public static function create(): self
    {
        return new self();
    }

    /**
     * @return $this
     */
    public function add(string $key, ObjectBuilder|Graph $object): self
    {
        $ref = new LocalRef($key);
        if (isset($this->objects[$key])) {
            throw new ModelException(\sprintf('The graph already has an object with key "%s".', $key));
        }
        $this->objects[$key] = $object instanceof ObjectBuilder ? $object->buildGraph($ref->iri()) : $object;
        $this->root ??= $key;

        return $this;
    }

    /**
     * The object partners are told about (the master waybill, say). Defaults to the first added.
     *
     * @return $this
     */
    public function root(string $key): self
    {
        if (!isset($this->objects[$key])) {
            throw new ModelException(\sprintf('The graph has no object with key "%s" to use as root.', $key));
        }
        $this->root = $key;

        return $this;
    }

    public function rootKey(): string
    {
        return $this->root ?? throw new ModelException('The graph is empty.');
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return array_keys($this->objects);
    }

    public function has(string $key): bool
    {
        return isset($this->objects[$key]);
    }

    public function graphOf(string $key): Graph
    {
        return $this->objects[$key] ?? throw new ModelException(\sprintf('The graph has no object with key "%s".', $key));
    }

    /**
     * Every local reference must point at an object in the graph; dangling
     * references would publish a link to nothing.
     */
    public function validate(): void
    {
        if ($this->objects === []) {
            throw new ModelException('The graph is empty.');
        }
        foreach ($this->objects as $key => $graph) {
            foreach ($graph as $triple) {
                foreach ([$triple->subject, $triple->object] as $term) {
                    if ($term instanceof Iri && LocalRef::isLocal($term)) {
                        $target = LocalRef::keyOf($term);
                        if (!isset($this->objects[$target])) {
                            throw new ModelException(\sprintf('Object "%s" refers to "%s", which the graph does not contain.', $key, $target));
                        }
                    }
                }
            }
        }
    }

    /**
     * Mint a URI for every object (existing URIs win) and rewrite every local
     * reference to the URI its key resolved to.
     *
     * @param array<string, Iri> $existing local key => URI already published under
     */
    public function resolve(IriMinter $minter, array $existing = []): ResolvedGraph
    {
        $this->validate();
        $iris = [];
        foreach ($this->objects as $key => $graph) {
            $types = array_map(static fn(Iri $t): string => $t->value, $graph->typesOf((new LocalRef($key))->iri()));
            sort($types, SORT_STRING);
            $iris[$key] = $existing[$key] ?? $minter->mint($key, $types);
        }

        $objects = [];
        foreach ($this->objects as $key => $graph) {
            $rewritten = new Graph();
            foreach ($graph as $triple) {
                $rewritten->add(new Triple(
                    self::rewrite($triple->subject, $iris),
                    $triple->predicate,
                    self::rewrite($triple->object, $iris),
                ));
            }
            $objects[$key] = new LogisticsObject($iris[$key], $rewritten);
        }

        return new ResolvedGraph($this->rootKey(), $objects);
    }

    /**
     * @template T of \LambdaTwelve\OneRecord\Rdf\Term
     * @param T $term
     * @param array<string, Iri> $iris
     * @return T|Iri
     */
    private static function rewrite(\LambdaTwelve\OneRecord\Rdf\Term $term, array $iris): \LambdaTwelve\OneRecord\Rdf\Term
    {
        if ($term instanceof Iri && LocalRef::isLocal($term)) {
            return $iris[LocalRef::keyOf($term)];
        }

        return $term;
    }
}
