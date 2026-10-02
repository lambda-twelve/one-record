<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;
use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * A set of triples with the lookups the rest of the package needs. Order is
 * not significant; duplicates collapse. Mutable while being built, which is
 * why the model layer wraps it rather than exposing it.
 *
 * @implements IteratorAggregate<int, Triple>
 */
final class Graph implements Countable, IteratorAggregate
{
    public const string RDF_TYPE = Namespaces::RDF . 'type';
    public const string RDF_FIRST = Namespaces::RDF . 'first';
    public const string RDF_REST = Namespaces::RDF . 'rest';
    public const string RDF_NIL = Namespaces::RDF . 'nil';

    /** @var array<string, Triple> keyed by N-Triples form, which makes the set free of duplicates */
    private array $triples = [];

    /** @var array<string, array<string, true>> subject key => triple keys */
    private array $bySubject = [];

    /**
     * @param iterable<Triple> $triples
     */
    public function __construct(iterable $triples = [])
    {
        foreach ($triples as $triple) {
            $this->add($triple);
        }
    }

    public function add(Triple $triple): void
    {
        $key = $triple->toNTriples();
        if (isset($this->triples[$key])) {
            return;
        }
        $this->triples[$key] = $triple;
        $this->bySubject[$triple->subject->toNTriples()][$key] = true;
    }

    public function remove(Triple $triple): bool
    {
        $key = $triple->toNTriples();
        if (!isset($this->triples[$key])) {
            return false;
        }
        unset($this->triples[$key], $this->bySubject[$triple->subject->toNTriples()][$key]);

        return true;
    }

    public function has(Triple $triple): bool
    {
        return isset($this->triples[$triple->toNTriples()]);
    }

    /**
     * @return list<Triple>
     */
    public function about(Iri|BlankNode $subject): array
    {
        $out = [];
        foreach ($this->bySubject[$subject->toNTriples()] ?? [] as $key => $_) {
            $out[] = $this->triples[$key];
        }

        return $out;
    }

    /**
     * @return list<Term>
     */
    public function objects(Iri|BlankNode $subject, Iri|string $predicate): array
    {
        $predicate = $predicate instanceof Iri ? $predicate->value : $predicate;
        $out = [];
        foreach ($this->about($subject) as $triple) {
            if ($triple->predicate->value === $predicate) {
                $out[] = $triple->object;
            }
        }

        return $out;
    }

    public function firstObject(Iri|BlankNode $subject, Iri|string $predicate): ?Term
    {
        return $this->objects($subject, $predicate)[0] ?? null;
    }

    /**
     * @return list<Iri>
     */
    public function typesOf(Iri|BlankNode $subject): array
    {
        $out = [];
        foreach ($this->objects($subject, self::RDF_TYPE) as $type) {
            if ($type instanceof Iri) {
                $out[] = $type;
            }
        }

        return $out;
    }

    /**
     * @return list<Iri|BlankNode>
     */
    public function subjects(): array
    {
        $out = [];
        foreach ($this->bySubject as $keys) {
            if ($keys === []) {
                continue;
            }
            $out[] = $this->triples[array_key_first($keys)]->subject;
        }

        return $out;
    }

    /**
     * Items of an RDF collection (rdf:first / rdf:rest chain) starting at $head.
     *
     * @return list<Term>
     */
    public function listItems(Term $head): array
    {
        $items = [];
        $node = $head;
        while ($node instanceof BlankNode || ($node instanceof Iri && $node->value !== self::RDF_NIL)) {
            $first = $this->firstObject($node, self::RDF_FIRST);
            if ($first === null) {
                break;
            }
            $items[] = $first;
            $node = $this->firstObject($node, self::RDF_REST) ?? new Iri(self::RDF_NIL);
        }

        return $items;
    }

    public function count(): int
    {
        return \count($this->triples);
    }

    /**
     * @return Iterator<int, Triple>
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator(array_values($this->triples));
    }

    /**
     * Triples in a stable order, for diffs and golden files.
     *
     * @return list<Triple>
     */
    public function sorted(): array
    {
        $keys = array_keys($this->triples);
        sort($keys, SORT_STRING);

        return array_map(fn(string $key): Triple => $this->triples[$key], $keys);
    }
}
