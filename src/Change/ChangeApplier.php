<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Api\Error;
use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\Model\EmbeddedIdMinter;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\Uuid5EmbeddedIdMinter;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Cargo;
use LambdaTwelve\OneRecord\Vocabulary\PropertyKind;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;

/**
 * Applies an accepted api:Change to a logistics object, as the spec's
 * "Update a Logistics Object" rules require: all or nothing, deletes before
 * adds, revision checked, hasLogisticsObject checked, no edits to events,
 * subjects limited to the object and its embedded objects, new embedded
 * objects given stable ids, orphaned embedded objects removed, and every
 * added value checked against the ontology.
 *
 * Failures raise ChangeRejected with api:Error objects; the server records
 * those on the ChangeRequest and marks it REQUEST_FAILED.
 */
final class ChangeApplier
{
    private readonly Vocabulary $vocabulary;
    private readonly EmbeddedIdMinter $minter;
    private readonly Comparer $comparer;

    public function __construct(?Vocabulary $vocabulary = null, ?EmbeddedIdMinter $minter = null, ?Comparer $comparer = null)
    {
        $this->vocabulary = $vocabulary ?? Vocabulary::default();
        $this->minter = $minter ?? new Uuid5EmbeddedIdMinter();
        $this->comparer = $comparer ?? new Comparer();
    }

    /**
     * @param int $currentRevision the stored revision of $current
     */
    public function apply(LogisticsObject $current, int $currentRevision, Change $change): ChangeResult
    {
        if (!$change->logisticsObject->equals($current->iri)) {
            throw new ChangeRejected([Error::of('Invalid resource', '400', 'api:hasLogisticsObject does not match the logistics object being changed.', null, $change->logisticsObject->value)]);
        }
        if ($change->revision !== $currentRevision) {
            throw new ChangeRejected([Error::of('Conflict with Logistics Object revision number', '409', \sprintf('The change applies to revision %d but the logistics object is at revision %d.', $change->revision, $currentRevision), null, $current->iri->value)]);
        }

        $errors = [];
        foreach ($change->operations as $operation) {
            if ($operation->predicate->value === Cargo::events) {
                $errors[] = Error::of('Invalid resource', '400', 'Logistics events cannot be changed through a Change; post them to /logistics-events.', Cargo::events, $current->iri->value);
            }
            if ($operation->predicate->value === Graph::RDF_TYPE && $operation->subject->equals($current->iri)) {
                $errors[] = Error::of('Invalid resource', '400', 'The type of a logistics object cannot be changed.', Graph::RDF_TYPE, $current->iri->value);
            }
            if (str_starts_with($operation->predicate->value, \LambdaTwelve\OneRecord\Spec\Namespaces::API)) {
                // Revision counters and anything else in the API namespace are the server's to write (R2-006).
                $errors[] = Error::of('Invalid resource', '400', \sprintf('%s is set by the server, not through a change.', $operation->predicate->value), $operation->predicate->value, $current->iri->value);
            }
        }
        if ($errors !== []) {
            throw new ChangeRejected($errors);
        }

        $graph = new Graph($current->graph);
        /** @var array<string, Iri> $minted blank label => embedded id */
        $minted = [];
        // Blank nodes are introduced by ADD operations whose value is the label; mint their ids first
        // so operations on the new node (as subject) resolve whatever their order in the document.
        $sequence = 0;
        foreach ($change->operations as $operation) {
            if ($operation->kind === OperationKind::Add && $operation->object->isBlankNode()) {
                $label = substr($operation->object->value, 2);
                $minted[$label] ??= $this->minter->mint($current->iri, \sprintf('r%d:%s:%d', $currentRevision + 1, $label, $sequence++));
            }
        }

        $deletes = array_values(array_filter($change->operations, static fn(Operation $o): bool => $o->kind === OperationKind::Delete));
        $adds = array_values(array_filter($change->operations, static fn(Operation $o): bool => $o->kind === OperationKind::Add));

        foreach ($deletes as $operation) {
            $subject = $this->resolveSubject($graph, $current->iri, $operation, $minted, $errors);
            if ($subject === null) {
                continue;
            }
            $this->delete($graph, $subject, $operation, $minted, $errors);
        }
        foreach ($adds as $operation) {
            $subject = $this->resolveSubject($graph, $current->iri, $operation, $minted, $errors);
            if ($subject === null) {
                continue;
            }
            $this->add($graph, $subject, $operation, $minted, $errors);
        }
        if ($errors !== []) {
            throw new ChangeRejected($errors);
        }

        $this->removeOrphans($graph, $current->iri);
        $this->validateGraph($graph, $current->iri, $minted, $errors);
        if ($errors !== []) {
            throw new ChangeRejected($errors);
        }
        $changed = $this->changedProperties($change, $current->iri, $current->graph, $graph, $minted);

        return new ChangeResult(new LogisticsObject($current->iri, $graph), $changed);
    }

    /**
     * The root properties a change touched, as a notification's
     * api:hasChangedProperty wants them: an operation on an embedded node
     * counts for the property through which the node hangs off the object
     * (editing the numericalValue of a grossWeight changes grossWeight).
     *
     * @param array<string, Iri> $minted
     * @return list<string>
     */
    private function changedProperties(Change $change, Iri $root, Graph $before, Graph $after, array $minted): array
    {
        $rootPropertiesOf = [];
        foreach ([$before, $after] as $graph) {
            foreach ($graph->about($root) as $triple) {
                $object = $triple->object;
                if (($object instanceof Iri || $object instanceof BlankNode) && LogisticsObject::isEmbeddedId($object)) {
                    $this->assignRootProperty($graph, $object, $triple->predicate->value, $rootPropertiesOf);
                }
            }
        }
        $properties = [];
        foreach ($change->operations as $operation) {
            $subject = $operation->subject;
            if ($subject instanceof BlankNode) {
                $subject = $minted[$subject->label] ?? $subject;
            }
            if ($subject->equals($root)) {
                $properties[$operation->predicate->value] = true;
            } else {
                // A node shared under several root properties changes all of them (R3-005).
                foreach (array_keys($rootPropertiesOf[$subject->toNTriples()] ?? []) as $property) {
                    $properties[$property] = true;
                }
            }
        }
        $list = array_keys($properties);
        sort($list, SORT_STRING);

        return $list;
    }

    /**
     * @param array<string, array<string, true>> $rootPropertiesOf node => the root properties it hangs from
     */
    private function assignRootProperty(Graph $graph, Iri|BlankNode $node, string $property, array &$rootPropertiesOf): void
    {
        $key = $node->toNTriples();
        if (isset($rootPropertiesOf[$key][$property])) {
            // Visited under this property already: a cycle, or a second path under the same property.
            return;
        }
        $rootPropertiesOf[$key][$property] = true;
        foreach ($graph->about($node) as $triple) {
            $object = $triple->object;
            if (($object instanceof Iri || $object instanceof BlankNode) && LogisticsObject::isEmbeddedId($object)) {
                $this->assignRootProperty($graph, $object, $property, $rootPropertiesOf);
            }
        }
    }

    /**
     * @param array<string, Iri> $minted
     * @param list<Error> $errors
     */
    private function resolveSubject(Graph $graph, Iri $root, Operation $operation, array $minted, array &$errors): ?Iri
    {
        $subject = $operation->subject;
        if ($subject instanceof BlankNode) {
            if (isset($minted[$subject->label])) {
                return $minted[$subject->label];
            }
            $errors[] = Error::of('Invalid resource', '400', \sprintf('Blank node %s is used as a subject but no ADD operation introduces it.', $subject->toNTriples()), $operation->predicate->value);

            return null;
        }
        if ($subject->equals($root)) {
            return $subject;
        }
        if (LogisticsObject::isEmbeddedId($subject) && $graph->about($subject) !== []) {
            return $subject;
        }
        $errors[] = Error::of('Invalid resource', '400', \sprintf('"%s" is neither the logistics object nor one of its embedded objects.', $subject->value), $operation->predicate->value, $subject->value);

        return null;
    }

    /**
     * @param array<string, Iri> $minted
     * @param list<Error> $errors
     */
    private function delete(Graph $graph, Iri $subject, Operation $operation, array $minted, array &$errors): void
    {
        $wanted = $this->objectTerm($operation, $minted);
        $existing = $graph->objects($subject, $operation->predicate);
        foreach ($existing as $candidate) {
            if ($this->sameValue($candidate, $wanted)) {
                $graph->remove(new Triple($subject, $operation->predicate, $candidate));

                return;
            }
        }
        $errors[] = Error::of('Unprocessable content', '422', \sprintf('Cannot delete %s: the value is not present on %s.', $operation->object->value, $subject->value), $operation->predicate->value);
    }

    /**
     * @param array<string, Iri> $minted
     * @param list<Error> $errors
     */
    private function add(Graph $graph, Iri $subject, Operation $operation, array $minted, array &$errors): void
    {
        $term = $this->objectTerm($operation, $minted);
        $predicate = $operation->predicate->value;
        // Property validity is judged on the finished graph (validateGraph): judging it here would
        // depend on whether the node's type arrived before or after this operation (AR-013).
        if ($term instanceof Literal && !self::lexicallyValid($term)) {
            $errors[] = Error::of('Invalid resource', '400', \sprintf('"%s" is not a valid %s.', $term->lexical, $term->datatype), $predicate);

            return;
        }
        foreach ($graph->objects($subject, $operation->predicate) as $candidate) {
            if ($this->sameValue($candidate, $term)) {
                $errors[] = Error::of('Unprocessable content', '422', \sprintf('Cannot add %s: the value is already present.', $operation->object->value), $predicate);

                return;
            }
        }

        $graph->add(new Triple($subject, $operation->predicate, $term));
        // A new embedded object carries its class in the operation's datatype (spec example C2).
        if ($operation->object->isBlankNode() && $term instanceof Iri && $graph->typesOf($term) === [] && $this->vocabulary->isClass($operation->object->datatype)) {
            $graph->add(new Triple($term, new Iri(Graph::RDF_TYPE), new Iri($operation->object->datatype)));
        }
    }

    /**
     * Every property the change touched must be one the ontology knows, one the
     * node's classes accept, and of the right kind. Checked once the whole
     * change is applied so the answer does not depend on operation order.
     *
     * @param array<string, Iri> $minted
     * @param list<Error> $errors
     */
    private function validateGraph(Graph $graph, Iri $root, array $minted, array &$errors): void
    {
        $subjects = [$root, ...array_values($minted)];
        foreach ($graph->subjects() as $subject) {
            if ($subject instanceof Iri && LogisticsObject::isEmbeddedId($subject)) {
                $subjects[] = $subject;
            }
        }
        $seen = [];
        foreach ($subjects as $subject) {
            if (isset($seen[$subject->toNTriples()])) {
                continue;
            }
            $seen[$subject->toNTriples()] = true;
            $types = array_map(static fn(Iri $t): string => $t->value, $graph->typesOf($subject));
            if ($types === []) {
                continue;
            }
            foreach ($graph->about($subject) as $triple) {
                $predicate = $triple->predicate->value;
                if ($predicate === Graph::RDF_TYPE || \in_array($predicate, [Api::hasRevision, Api::hasLatestRevision], true)) {
                    // The two revision properties are metadata a stored object may legitimately carry; no other API term is.
                    continue;
                }
                $info = $this->vocabulary->property($predicate);
                if ($info === null) {
                    $errors[] = Error::of('Invalid resource', '400', \sprintf('"%s" is not a property of the ontology.', $predicate), $predicate, $subject->value);
                    continue;
                }
                if (!$this->vocabulary->accepts($types, $predicate)) {
                    $errors[] = Error::of('Invalid resource', '400', \sprintf('%s does not accept %s.', implode(', ', $types), $predicate), $predicate, $subject->value);
                    continue;
                }
                if ($info->kind === PropertyKind::Datatype && !$triple->object instanceof Literal) {
                    $errors[] = Error::of('Invalid resource', '400', \sprintf('%s takes a literal value.', $predicate), $predicate, $subject->value);
                } elseif ($info->kind === PropertyKind::Object && $triple->object instanceof Literal) {
                    $errors[] = Error::of('Invalid resource', '400', \sprintf('%s takes an object or reference, not a literal.', $predicate), $predicate, $subject->value);
                }
            }
        }
    }

    /**
     * @param array<string, Iri> $minted
     */
    private function objectTerm(Operation $operation, array $minted): Term
    {
        $object = $operation->object;
        if ($object->isLiteral()) {
            return $object->toLiteral();
        }
        if ($object->isBlankNode()) {
            return $minted[substr($object->value, 2)] ?? throw new ChangeRejected([Error::of('Invalid resource', '400', \sprintf('Blank node %s is deleted but was never added.', $object->value), $operation->predicate->value)]);
        }

        return new Iri($object->value);
    }

    private function sameValue(Term $a, Term $b): bool
    {
        if ($a instanceof Literal && $b instanceof Literal) {
            return $this->comparer->normaliseLiteral($a)->equals($this->comparer->normaliseLiteral($b));
        }

        return $a->equals($b);
    }

    private static function lexicallyValid(Literal $literal): bool
    {
        return match ($literal->datatype) {
            Literal::XSD_BOOLEAN => \in_array($literal->lexical, ['true', 'false', '1', '0'], true),
            Literal::XSD_INTEGER, Literal::XSD_DOUBLE, Literal::XSD_DECIMAL => is_numeric($literal->lexical),
            Literal::XSD_DATETIME => preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})?$/', $literal->lexical) === 1,
            default => true,
        };
    }

    /**
     * Embedded objects no longer reachable from the logistics object take
     * their triples with them (the spec's "cleansing of the triples").
     */
    private function removeOrphans(Graph $graph, Iri $root): void
    {
        $reachable = [$root->toNTriples() => true];
        $queue = [$root];
        while ($queue !== []) {
            $node = array_shift($queue);
            foreach ($graph->about($node) as $triple) {
                $object = $triple->object;
                if (($object instanceof BlankNode || ($object instanceof Iri && LogisticsObject::isEmbeddedId($object))) && !isset($reachable[$object->toNTriples()])) {
                    $reachable[$object->toNTriples()] = true;
                    $queue[] = $object;
                }
            }
        }
        foreach ($graph->subjects() as $subject) {
            if (!isset($reachable[$subject->toNTriples()]) && LogisticsObject::isEmbeddedId($subject)) {
                foreach ($graph->about($subject) as $triple) {
                    $graph->remove($triple);
                }
            }
        }
    }
}
