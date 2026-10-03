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
 * Two phases, because an embedded node is one node however many links reach
 * it (R2-011, R3-001, R4-001). First a correspondence between the old and the
 * new embedded nodes is built over the whole graphs: unchanged content pairs
 * up first, in every slot, so an unchanged branch is never sacrificed to an
 * edit elsewhere; then a slot left with exactly one old and one new node of
 * the same type pairs them for an in-place edit (spec example C3). Only then
 * are operations emitted from that correspondence: plain values as
 * DELETE/ADD pairs, a link deleted where its target no longer corresponds, a
 * link added to the node that represents the target (an existing node edited
 * to match it, or one blank node introduced once for it, C2), and an old
 * node's own triples deleted once when no surviving link reaches it (C4).
 * Logistics events are never part of a change; the spec forbids it.
 */
final class ChangeBuilder
{
    private int $blankCounter = 0;

    private ?Iri $root = null;

    /** @var array<string, Iri|BlankNode> old embedded node => the new node it corresponds to */
    private array $forward = [];

    /** @var array<string, Iri|BlankNode> new embedded node => the old node that represents it */
    private array $backward = [];

    /** @var array<string, BlankNode> new embedded node no old node represents => the blank node introducing it */
    private array $introduced = [];

    /** @var array<string, true> old nodes whose pair's operations were emitted */
    private array $emitted = [];

    /** @var array<string, string> content signatures of old nodes */
    private array $fromSignatures = [];

    /** @var array<string, string> content signatures of new nodes */
    private array $toSignatures = [];

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
        // Servers may list inferred superclasses in @type (NE:ONE does; spec question 21), so only the
        // most specific classes decide whether the type changed. Type triples are never diffed: the
        // spec gives a Change no way to retype an object, and the applier refuses such operations.
        $vocabulary = $this->vocabulary ?? Vocabulary::default();
        if ($vocabulary->mostSpecific($from->types()) !== $vocabulary->mostSpecific($to->types())) {
            throw ChangeException::because('Invalid resource', 'The type of a logistics object cannot be changed with a Change.');
        }

        $this->blankCounter = 0;
        $this->forward = [];
        $this->backward = [];
        $this->introduced = [];
        $this->emitted = [];
        $this->fromSignatures = [];
        $this->toSignatures = [];
        $this->root = $from->iri;

        $this->correspond($from->graph, $to->graph, $from->iri, $to->iri);
        $operations = $this->emit($from->graph, $to->graph, $from->iri, $to->iri);
        $operations = [...$operations, ...$this->deleteUnreachable($from->graph, $from->iri, $operations)];
        if ($operations === []) {
            return null;
        }

        return new Change($from->iri, $revision, $operations, $description);
    }

    // --- Phase 1: correspondence ------------------------------------------

    private function correspond(Graph $fromGraph, Graph $toGraph, Iri $fromRoot, Iri $toRoot): void
    {
        $this->pair($fromRoot, $toRoot);
        $queue = [[$fromRoot, $toRoot]];
        while ($queue !== []) {
            [$old, $new] = array_shift($queue);
            $slots = $this->slots($fromGraph, $toGraph, $old, $new);
            // Unchanged content first, in every slot of this node, so an edit in one slot can never
            // take a node another slot still needs as it is (R4-001).
            foreach ($slots as [$olds, $news]) {
                foreach ($news as $candidate) {
                    if (isset($this->backward[$candidate->toNTriples()])) {
                        continue;
                    }
                    $signature = $this->signatureOf($toGraph, $candidate, $this->toSignatures);
                    foreach ($olds as $existing) {
                        if (!isset($this->forward[$existing->toNTriples()]) && $this->signatureOf($fromGraph, $existing, $this->fromSignatures) === $signature) {
                            $this->pair($existing, $candidate);
                            $queue[] = [$existing, $candidate];
                            break;
                        }
                    }
                }
            }
            // Then one unmatched old against one unmatched new of the same type: an in-place edit (C3).
            foreach ($slots as [$olds, $news]) {
                $unmatchedOld = array_values(array_filter($olds, fn(Iri|BlankNode $n): bool => !isset($this->forward[$n->toNTriples()])));
                $unmatchedNew = array_values(array_filter($news, fn(Iri|BlankNode $n): bool => !isset($this->backward[$n->toNTriples()])));
                if (\count($unmatchedOld) === 1 && \count($unmatchedNew) === 1 && $unmatchedOld[0] instanceof Iri
                    && $this->sameTypes($fromGraph, $unmatchedOld[0], $toGraph, $unmatchedNew[0])) {
                    $this->pair($unmatchedOld[0], $unmatchedNew[0]);
                    $queue[] = [$unmatchedOld[0], $unmatchedNew[0]];
                }
            }
        }
    }

    private function pair(Iri|BlankNode $old, Iri|BlankNode $new): void
    {
        $this->forward[$old->toNTriples()] = $new;
        $this->backward[$new->toNTriples()] = $old;
    }

    /**
     * The embedded objects of two corresponding nodes, per predicate.
     *
     * @return array<string, array{list<Iri|BlankNode>, list<Iri|BlankNode>}>
     */
    private function slots(Graph $fromGraph, Graph $toGraph, Iri|BlankNode $old, Iri|BlankNode $new): array
    {
        $slots = [];
        foreach ($this->predicates($fromGraph, $toGraph, $old, $new) as $predicate) {
            [, $oldEmbedded] = $this->partition($fromGraph, $fromGraph->objects($old, $predicate));
            [, $newEmbedded] = $this->partition($toGraph, $toGraph->objects($new, $predicate));
            if ($oldEmbedded !== [] || $newEmbedded !== []) {
                $slots[$predicate->value] = [$oldEmbedded, $newEmbedded];
            }
        }

        return $slots;
    }

    /**
     * @return list<Iri>
     */
    private function predicates(Graph $fromGraph, Graph $toGraph, Iri|BlankNode $old, Iri|BlankNode $new): array
    {
        $predicates = [];
        foreach ([...$fromGraph->about($old), ...$toGraph->about($new)] as $triple) {
            $predicates[$triple->predicate->value] = $triple->predicate;
        }
        ksort($predicates, SORT_STRING);
        $isRoot = $this->root !== null && $old->equals($this->root);

        return array_values(array_filter($predicates, static fn(Iri $p): bool => $p->value !== Cargo::events && !($isRoot && $p->value === Graph::RDF_TYPE)));
    }

    // --- Phase 2: operations ------------------------------------------------

    /**
     * @return list<Operation>
     */
    private function emit(Graph $fromGraph, Graph $toGraph, Iri|BlankNode $old, Iri|BlankNode $new): array
    {
        $key = $old->toNTriples();
        if (isset($this->emitted[$key])) {
            return [];
        }
        $this->emitted[$key] = true;
        $operations = [];
        $pairs = [];
        foreach ($this->predicates($fromGraph, $toGraph, $old, $new) as $predicate) {
            [$oldPlain, $oldEmbedded] = $this->partition($fromGraph, $fromGraph->objects($old, $predicate));
            [$newPlain, $newEmbedded] = $this->partition($toGraph, $toGraph->objects($new, $predicate));

            // Plain values: set difference on their N-Triples form (numbers normalised).
            $oldKeys = $this->keyed($oldPlain);
            $newKeys = $this->keyed($newPlain);
            foreach (array_diff_key($oldKeys, $newKeys) as $term) {
                $operations[] = Operation::delete($old, $predicate, $this->plainObject($fromGraph, $predicate, $term));
            }
            foreach (array_diff_key($newKeys, $oldKeys) as $term) {
                $operations[] = Operation::add($old, $predicate, $this->plainObject($toGraph, $predicate, $term));
            }

            // Links: one survives when its target corresponds to a node in the new slot.
            $wanted = [];
            foreach ($newEmbedded as $node) {
                $wanted[$node->toNTriples()] = true;
            }
            $covered = [];
            foreach ($oldEmbedded as $node) {
                $target = $this->forward[$node->toNTriples()] ?? null;
                if ($target === null || !isset($wanted[$target->toNTriples()])) {
                    $operations[] = Operation::delete($old, $predicate, $this->nodeObject($fromGraph, $predicate, $node, $node));
                    continue;
                }
                $covered[$target->toNTriples()] = true;
                $pairs[] = [$node, $target];
            }
            foreach ($newEmbedded as $node) {
                if (!isset($covered[$node->toNTriples()])) {
                    $operations = [...$operations, ...$this->linkTo($toGraph, $old, $predicate, $node)];
                }
            }
        }
        foreach ($pairs as [$oldChild, $newChild]) {
            $operations = [...$operations, ...$this->emit($fromGraph, $toGraph, $oldChild, $newChild)];
        }

        return $operations;
    }

    /**
     * Link a subject to the node that represents a new embedded node: the old
     * node edited to match it, the blank node already introduced for it, or a
     * fresh blank node with its triples (spec example C2), introduced once.
     *
     * @return list<Operation>
     */
    private function linkTo(Graph $toGraph, Iri|BlankNode $subject, Iri $predicate, Iri|BlankNode $node): array
    {
        $key = $node->toNTriples();
        $existing = $this->backward[$key] ?? null;
        if ($existing instanceof Iri) {
            return [Operation::add($subject, $predicate, $this->nodeObject($toGraph, $predicate, $node, $existing))];
        }
        if (isset($this->introduced[$key])) {
            return [Operation::add($subject, $predicate, $this->nodeObject($toGraph, $predicate, $node, $this->introduced[$key]))];
        }
        $blank = $this->introduced[$key] = new BlankNode('b' . $this->blankCounter++);
        $operations = [Operation::add($subject, $predicate, $this->nodeObject($toGraph, $predicate, $node, $blank))];
        foreach ($toGraph->about($node) as $triple) {
            if ($triple->predicate->value === Graph::RDF_TYPE) {
                continue;
            }
            $object = $triple->object;
            if (($object instanceof BlankNode || ($object instanceof Iri && LogisticsObject::isEmbeddedId($object))) && $toGraph->about($object) !== []) {
                $operations = [...$operations, ...$this->linkTo($toGraph, $blank, $triple->predicate, $object)];
            } else {
                $operations[] = Operation::add($blank, $triple->predicate, $this->plainObject($toGraph, $triple->predicate, $object));
            }
        }

        return $operations;
    }

    /**
     * An operation object for a link: the class of the node in its graph, the identity of its representation.
     */
    private function nodeObject(Graph $graph, Iri $predicate, Iri|BlankNode $node, Iri|BlankNode $representation): OperationObject
    {
        return new OperationObject($this->datatypeForNode($graph, $predicate, $node), $representation instanceof Iri ? $representation->value : $representation->toNTriples());
    }

    /**
     * Spec example C4, decided on the whole graph: once the link deletions are
     * known, every embedded node of the old graph that no surviving link
     * reaches loses its own triples, once, however many links used to reach
     * it. A node still reached from elsewhere keeps them (R3-001).
     *
     * @param list<Operation> $operations
     * @return list<Operation>
     */
    private function deleteUnreachable(Graph $from, Iri $root, array $operations): array
    {
        $deletedLinks = [];
        foreach ($operations as $operation) {
            if ($operation->kind === OperationKind::Delete && !$operation->object->isLiteral()) {
                $deletedLinks[$operation->subject->toNTriples() . ' ' . $operation->predicate->value . ' ' . $operation->object->value] = true;
            }
        }
        $surviving = new Graph();
        foreach ($from as $triple) {
            $object = $triple->object;
            if ($object instanceof Iri && isset($deletedLinks[$triple->subject->toNTriples() . ' ' . $triple->predicate->value . ' ' . $object->value])) {
                continue;
            }
            $surviving->add($triple);
        }
        $before = $this->reachableEmbedded($from, $root);
        $after = $this->reachableEmbedded($surviving, $root);
        $deletes = [];
        foreach ($before as $key => $node) {
            if (isset($after[$key])) {
                continue;
            }
            foreach ($surviving->about($node) as $triple) {
                if ($triple->predicate->value === Graph::RDF_TYPE) {
                    continue;
                }
                $deletes[] = Operation::delete($node, $triple->predicate, $this->plainObject($from, $triple->predicate, $triple->object));
            }
        }

        return $deletes;
    }

    /**
     * @return array<string, Iri|BlankNode> embedded nodes reachable from the root, by N-Triples key
     */
    private function reachableEmbedded(Graph $graph, Iri $root): array
    {
        $seen = [$root->toNTriples() => true];
        $found = [];
        $queue = [$root];
        while ($queue !== []) {
            $node = array_shift($queue);
            foreach ($graph->about($node) as $triple) {
                $object = $triple->object;
                $key = $object->toNTriples();
                if (($object instanceof BlankNode || ($object instanceof Iri && LogisticsObject::isEmbeddedId($object))) && !isset($seen[$key]) && $graph->about($object) !== []) {
                    $seen[$key] = true;
                    $found[$key] = $object;
                    $queue[] = $object;
                }
            }
        }

        return $found;
    }

    // --- Helpers --------------------------------------------------------------

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

    /**
     * @param array<string, string> $cache
     */
    private function signatureOf(Graph $graph, Iri|BlankNode $node, array &$cache): string
    {
        return $cache[$node->toNTriples()] ??= $this->signature($graph, $node);
    }

    private function signature(Graph $graph, Iri|BlankNode $node): string
    {
        $comparer = $this->comparer ?? new Comparer();
        $sub = new Graph();
        $seen = [];
        $this->collect($graph, $node, $sub, $seen);
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
    private function collect(Graph $graph, Iri|BlankNode $node, Graph $into, array &$seen): void
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
}
