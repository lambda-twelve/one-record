<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\PropertyKind;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use LogicException;

/**
 * Computes the api:Change that turns one version of a logistics object into
 * another: what a host sends when it republishes updated data, and what a
 * partner sends to request a correction.
 *
 * Plain values and references become DELETE/ADD pairs. Embedded objects are
 * matched by content: an unchanged one produces nothing; a changed one is
 * edited in place through its embedded id when the slot holds exactly one
 * old and one new object (spec example C3), otherwise the old object is
 * deleted with its triples (C4) and the new one added as a blank node (C2).
 * Logistics events are never part of a change; the spec forbids it.
 */
final class ChangeBuilder
{
    private int $blankCounter = 0;

    public function __construct(
        private readonly ?Vocabulary $vocabulary = null,
        private readonly ?Comparer $comparer = null,
    ) {}

    /**
     * @param int $revision the current revision of $from, which the change applies to
     * @return ?Change null when the two versions are the same
     */
    public function diff(LogisticsObject $from, LogisticsObject $to, int $revision, ?string $description = null): ?Change
    {
        if (!$from->iri->equals($to->iri)) {
            throw new ModelException(\sprintf('Cannot diff "%s" against "%s": a change applies to one logistics object.', $from->iri->value, $to->iri->value));
        }
        if ($from->types() !== $to->types()) {
            throw ChangeException::because('Invalid resource', 'The type of a logistics object cannot be changed with a Change.');
        }

        $this->blankCounter = 0;
        $operations = $this->diffNode($from->graph, $to->graph, $from->iri, $to->iri, $from->iri);
        if ($operations === []) {
            return null;
        }

        return new Change($from->iri, $revision, $operations, $description);
    }

    /**
     * @return list<Operation>
     */
    private function diffNode(Graph $fromGraph, Graph $toGraph, Iri|BlankNode $fromNode, Iri|BlankNode $toNode, Iri|BlankNode $subject): array
    {
        $operations = [];
        $predicates = [];
        foreach ([...$fromGraph->about($fromNode), ...$toGraph->about($toNode)] as $triple) {
            $predicates[$triple->predicate->value] = $triple->predicate;
        }
        ksort($predicates, SORT_STRING);

        foreach ($predicates as $predicate) {
            if ($predicate->value === Cargo::events) {
                continue;
            }
            [$oldPlain, $oldEmbedded] = $this->partition($fromGraph, $fromGraph->objects($fromNode, $predicate));
            [$newPlain, $newEmbedded] = $this->partition($toGraph, $toGraph->objects($toNode, $predicate));

            // Plain values: set difference on their N-Triples form (numbers normalised).
            $oldKeys = $this->keyed($oldPlain);
            $newKeys = $this->keyed($newPlain);
            foreach (array_diff_key($oldKeys, $newKeys) as $term) {
                $operations[] = Operation::delete($subject, $predicate, $this->plainObject($fromGraph, $predicate, $term));
            }
            foreach (array_diff_key($newKeys, $oldKeys) as $term) {
                $operations[] = Operation::add($subject, $predicate, $this->plainObject($toGraph, $predicate, $term));
            }

            // Embedded objects: pair identical content, then edit in place or replace.
            $oldBySignature = [];
            foreach ($oldEmbedded as $node) {
                $oldBySignature[$this->signature($fromGraph, $node)][] = $node;
            }
            $unmatchedNew = [];
            foreach ($newEmbedded as $node) {
                $signature = $this->signature($toGraph, $node);
                if (isset($oldBySignature[$signature]) && $oldBySignature[$signature] !== []) {
                    array_shift($oldBySignature[$signature]);
                    continue;
                }
                $unmatchedNew[] = $node;
            }
            $unmatchedOld = [];
            foreach ($oldBySignature as $leftovers) {
                foreach ($leftovers as $leftover) {
                    $unmatchedOld[] = $leftover;
                }
            }

            if (\count($unmatchedOld) === 1 && \count($unmatchedNew) === 1 && $unmatchedOld[0] instanceof Iri
                && $this->sameTypes($fromGraph, $unmatchedOld[0], $toGraph, $unmatchedNew[0])) {
                $operations = [...$operations, ...$this->diffNode($fromGraph, $toGraph, $unmatchedOld[0], $unmatchedNew[0], $unmatchedOld[0])];
                continue;
            }
            foreach ($unmatchedOld as $node) {
                $operations = [...$operations, ...$this->deleteSubtree($fromGraph, $subject, $predicate, $node)];
            }
            foreach ($unmatchedNew as $node) {
                $operations = [...$operations, ...$this->addSubtree($toGraph, $subject, $predicate, $node)];
            }
        }

        return $operations;
    }

    /**
     * @param list<Term> $terms
     * @return array{list<Term>, list<Iri|BlankNode>}
     */
    private function partition(Graph $graph, array $terms): array
    {
        $plain = [];
        $embedded = [];
        foreach ($terms as $term) {
            if (($term instanceof BlankNode || ($term instanceof Iri && LogisticsObject::isEmbeddedId($term))) && $graph->about($term) !== []) {
                $embedded[] = $term;
            } else {
                $plain[] = $term;
            }
        }

        return [$plain, $embedded];
    }

    /**
     * @param list<Term> $terms
     * @return array<string, Term>
     */
    private function keyed(array $terms): array
    {
        $comparer = $this->comparer ?? new Comparer();
        $keyed = [];
        foreach ($terms as $term) {
            $key = $term instanceof Literal ? $comparer->normaliseLiteral($term)->toNTriples() : $term->toNTriples();
            $keyed[$key] = $term;
        }

        return $keyed;
    }

    private function plainObject(Graph $graph, Iri $predicate, Term $term): OperationObject
    {
        if ($term instanceof Literal) {
            return OperationObject::literal($term);
        }
        if (!$term instanceof Iri && !$term instanceof BlankNode) {
            throw new LogicException('Unexpected term ' . $term::class);
        }

        return new OperationObject($this->datatypeForNode($graph, $predicate, $term), $term instanceof Iri ? $term->value : $term->toNTriples());
    }

    /**
     * The "datatype" of a node-valued operation object is the class of the
     * node: its declared type when the graph has one, else the property's
     * range from the ontology, else cargo:LogisticsObject.
     */
    private function datatypeForNode(Graph $graph, Iri $predicate, Iri|BlankNode $node): string
    {
        $types = array_map(static fn(Iri $t): string => $t->value, $graph->typesOf($node));
        if ($types !== []) {
            $specific = ($this->vocabulary ?? Vocabulary::default())->mostSpecific($types);
            sort($specific, SORT_STRING);

            return $specific[0];
        }
        $info = ($this->vocabulary ?? Vocabulary::default())->property($predicate->value);
        if ($info !== null && $info->kind === PropertyKind::Object && $info->ranges !== []) {
            return $info->ranges[0];
        }

        return Cargo::LogisticsObject;
    }

    private function signature(Graph $graph, Iri|BlankNode $node): string
    {
        $comparer = $this->comparer ?? new Comparer();
        $sub = new Graph();
        $this->collect($graph, $node, $sub, []);
        // Re-root at a fixed blank label so identical content under different ids hashes the same.
        $rerooted = new Graph();
        $marker = new BlankNode('root');
        foreach ($sub as $triple) {
            $rerooted->add(new Triple(
                $triple->subject->equals($node) ? $marker : $triple->subject,
                $triple->predicate,
                $triple->object->equals($node) ? $marker : $triple->object,
            ));
        }

        return implode("\n", array_map(static fn(Triple $t): string => $t->toNTriples(), $comparer->canonical($rerooted)->sorted()));
    }

    /**
     * @param array<string, true> $seen
     */
    private function collect(Graph $graph, Iri|BlankNode $node, Graph $into, array $seen): void
    {
        $seen[$node->toNTriples()] = true;
        foreach ($graph->about($node) as $triple) {
            $into->add($triple);
            $object = $triple->object;
            if (($object instanceof BlankNode || ($object instanceof Iri && LogisticsObject::isEmbeddedId($object))) && !isset($seen[$object->toNTriples()])) {
                $this->collect($graph, $object, $into, $seen);
            }
        }
    }

    private function sameTypes(Graph $a, Iri|BlankNode $nodeA, Graph $b, Iri|BlankNode $nodeB): bool
    {
        $typesA = array_map(static fn(Iri $t): string => $t->value, $a->typesOf($nodeA));
        $typesB = array_map(static fn(Iri $t): string => $t->value, $b->typesOf($nodeB));
        sort($typesA, SORT_STRING);
        sort($typesB, SORT_STRING);

        return $typesA === $typesB;
    }

    /**
     * Delete the link and every triple of the embedded object (spec example C4).
     *
     * @return list<Operation>
     */
    private function deleteSubtree(Graph $graph, Iri|BlankNode $subject, Iri $predicate, Iri|BlankNode $node): array
    {
        $operations = [Operation::delete($subject, $predicate, new OperationObject($this->datatypeForNode($graph, $predicate, $node), $node instanceof Iri ? $node->value : $node->toNTriples()))];
        foreach ($graph->about($node) as $triple) {
            if ($triple->predicate->value === Graph::RDF_TYPE) {
                continue;
            }
            $object = $triple->object;
            if (($object instanceof BlankNode || ($object instanceof Iri && LogisticsObject::isEmbeddedId($object))) && $graph->about($object) !== []) {
                $operations = [...$operations, ...$this->deleteSubtree($graph, $node, $triple->predicate, $object)];
            } else {
                $operations[] = Operation::delete($node, $triple->predicate, $this->plainObject($graph, $triple->predicate, $object));
            }
        }

        return $operations;
    }

    /**
     * Add the link as a fresh blank node and every triple below it (spec example C2).
     *
     * @return list<Operation>
     */
    private function addSubtree(Graph $graph, Iri|BlankNode $subject, Iri $predicate, Iri|BlankNode $node): array
    {
        $blank = new BlankNode('b' . $this->blankCounter++);
        $operations = [Operation::add($subject, $predicate, new OperationObject($this->datatypeForNode($graph, $predicate, $node), $blank->toNTriples()))];
        foreach ($graph->about($node) as $triple) {
            if ($triple->predicate->value === Graph::RDF_TYPE) {
                continue;
            }
            $object = $triple->object;
            if (($object instanceof BlankNode || ($object instanceof Iri && LogisticsObject::isEmbeddedId($object))) && $graph->about($object) !== []) {
                $operations = [...$operations, ...$this->addSubtree($graph, $blank, $triple->predicate, $object)];
            } else {
                $operations[] = Operation::add($blank, $triple->predicate, $this->plainObject($graph, $triple->predicate, $object));
            }
        }

        return $operations;
    }
}
