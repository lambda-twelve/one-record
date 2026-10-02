<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\Writer;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;

/**
 * A logistics object: everything said about one URI, including its embedded
 * objects (blank nodes, or nodes under the `internal:` prefix once stored).
 *
 * Immutable. Revision, last-modified and the other things a server knows
 * about a stored object are not part of the object itself; they live on the
 * stored revision record and in HTTP headers.
 */
final readonly class LogisticsObject
{
    public function __construct(
        public Iri $iri,
        public Graph $graph,
    ) {
        if (LocalRef::isLocal($iri)) {
            throw new ModelException(\sprintf('"%s" is a local key, not a published URI; resolve the graph first.', $iri->value));
        }
    }

    /**
     * @param string|array<string, mixed> $json
     */
    public static function fromJsonLd(string|array $json, ?Iri $expectedIri = null): self
    {
        $document = JsonLd::expand($json);
        $root = $document->root;
        if ($root instanceof BlankNode) {
            if ($expectedIri === null) {
                throw new ModelException('The document has no @id; give the IRI the object is published under.');
            }
            $root = $expectedIri;
            $graph = self::rename($document->graph, $document->root, $root);
        } else {
            if ($expectedIri !== null && !$root->equals($expectedIri)) {
                throw new ModelException(\sprintf('The document is about "%s", not "%s".', $root->value, $expectedIri->value));
            }
            $graph = $document->graph;
        }

        return new self($root, $graph);
    }

    /**
     * @return list<string> class IRIs, sorted
     */
    public function types(): array
    {
        $types = array_map(static fn(Iri $t): string => $t->value, $this->graph->typesOf($this->iri));
        sort($types, SORT_STRING);

        return $types;
    }

    /**
     * The one class to put in the `Type` header: the most specific of the
     * declared types, or the first declared type when they are unrelated.
     */
    public function mostSpecificType(?Vocabulary $vocabulary = null): ?string
    {
        $vocabulary ??= Vocabulary::default();
        $specific = $vocabulary->mostSpecific($this->types());
        $known = array_values(array_filter($specific, static fn(string $t): bool => $vocabulary->isClass($t)));

        return $known[0] ?? $specific[0] ?? null;
    }

    /**
     * @return list<Term>
     */
    public function values(string $propertyIri): array
    {
        return $this->graph->objects($this->iri, $propertyIri);
    }

    public function value(string $propertyIri): ?Term
    {
        return $this->graph->firstObject($this->iri, $propertyIri);
    }

    public function literal(string $propertyIri): ?string
    {
        $value = $this->value($propertyIri);

        return $value instanceof Literal ? $value->lexical : null;
    }

    /**
     * @return list<Iri|BlankNode> the nodes embedded in this object, in a stable order
     */
    public function embeddedNodes(): array
    {
        $nodes = [];
        foreach ($this->graph->subjects() as $subject) {
            if (!$subject->equals($this->iri) && self::isEmbeddedId($subject)) {
                $nodes[$subject->toNTriples()] = $subject;
            }
        }
        ksort($nodes, SORT_STRING);

        return array_values($nodes);
    }

    public static function isEmbeddedId(Iri|BlankNode $node): bool
    {
        return $node instanceof BlankNode || $node->startsWith(Namespaces::EMBEDDED);
    }

    public function withIri(Iri $iri): self
    {
        return new self($iri, self::rename($this->graph, $this->iri, $iri));
    }

    public function withGraph(Graph $graph): self
    {
        return new self($this->iri, $graph);
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(?Context $context = null): array
    {
        return (new Writer())->write($this->graph, $this->iri, $context ?? Context::oneRecord());
    }

    public function toJson(?Context $context = null, bool $pretty = true): string
    {
        return JsonLd::compactToJson($this->graph, $this->iri, $context ?? Context::oneRecord(), $pretty);
    }

    /**
     * Same URI and isomorphic graphs (embedded ids compared as blank nodes).
     */
    public function isSameAs(self $other, ?Comparer $comparer = null): bool
    {
        return $this->iri->equals($other->iri) && ($comparer ?? new Comparer())->isomorphic($this->graph, $other->graph);
    }

    private static function rename(Graph $graph, Iri|BlankNode $from, Iri $to): Graph
    {
        $renamed = new Graph();
        foreach ($graph as $triple) {
            $subject = $triple->subject->equals($from) ? $to : $triple->subject;
            $object = $triple->object->equals($from) ? $to : $triple->object;
            $renamed->add(new Triple($subject, $triple->predicate, $object));
        }

        return $renamed;
    }
}
