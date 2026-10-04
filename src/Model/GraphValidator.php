<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Xsd;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\PropertyKind;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;

/**
 * Checks a whole logistics-object or event graph against the ontology: the
 * root and every embedded node (blank, or under the embedded-id prefix),
 * however deep. One set of rules for creation over HTTP, for changes and
 * for posted events, so no path can accept what another refuses (R10-001
 * to R10-004, D10-001).
 *
 * Rules, per subject: the root's classes must exist; an embedded node must
 * declare at least one class, every class must exist and none may be a
 * logistics object class (those have their own URIs). Per triple: the
 * property must exist, be accepted by the subject's classes and be of the
 * right kind; a literal must fit the property's range (XSD derivation
 * included) and its datatype's grammar; a node reached through an object
 * property must, when the graph knows its classes, be of the range or a
 * subclass. API-namespace properties are the server's, except those a
 * caller tolerates (the stored revision counters).
 */
final class GraphValidator
{
    public function __construct(private readonly Vocabulary $vocabulary) {}

    /**
     * @param list<string> $tolerated API-namespace predicates the graph may carry
     * @param bool $nestedLogisticsObjects accept an embedded node of a logistics object class: the shape of
     *                                     the spec's own POST examples, which a server is expected to split into
     *                                     separate objects and this one keeps embedded (spec question 33)
     * @return list<GraphViolation> empty when the graph is valid
     */
    public function validate(Graph $graph, Iri|BlankNode $root, array $tolerated = [], bool $nestedLogisticsObjects = false): array
    {
        $violations = [];
        // Every node the graph describes is an embedded node, whatever id it carries (R11-001). A typed
        // link, which only states the class of what it points to, is a reference: its class must still
        // exist (R12-001), a logistics object class or a code list, and the range check below applies.
        $subjects = [$root->toNTriples() => $root];
        foreach ($graph->subjects() as $subject) {
            if (!$subject->equals($root)) {
                $subjects[$subject->toNTriples()] = $subject;
            }
        }
        foreach ($subjects as $subject) {
            $isRoot = $subject->equals($root);
            $subjectName = $subject instanceof Iri ? $subject->value : $subject->toNTriples();
            $types = array_map(static fn(Iri $t): string => $t->value, $graph->typesOf($subject));
            if (!$isRoot && !LogisticsObject::isEmbeddedIn($graph, $subject, $root instanceof Iri ? $root : null)) {
                foreach ($types as $type) {
                    if (!$this->vocabulary->isClass($type) && $this->vocabulary->codeList($type) === null) {
                        $violations[] = new GraphViolation(\sprintf('"%s" is not a class of the ontology.', $type), Graph::RDF_TYPE, $subjectName);
                    }
                }
                continue;
            }
            foreach ($types as $type) {
                if (!$this->vocabulary->isClass($type)) {
                    $violations[] = new GraphViolation(\sprintf('"%s" is not a class of the ontology.', $type), Graph::RDF_TYPE, $subjectName);
                } elseif (!$isRoot && !$nestedLogisticsObjects && $this->vocabulary->isLogisticsObjectClass($type)) {
                    $violations[] = new GraphViolation(\sprintf('%s is a logistics object class; a logistics object has its own URI and is referred to, not embedded.', $type), Graph::RDF_TYPE, $subjectName);
                }
            }
            if ($types === []) {
                $violations[] = new GraphViolation($isRoot ? 'The object declares no @type.' : 'An embedded object must declare its @type.', Graph::RDF_TYPE, $subjectName);
                continue;
            }
            foreach ($graph->about($subject) as $triple) {
                $predicate = $triple->predicate->value;
                if ($predicate === Graph::RDF_TYPE || \in_array($predicate, $tolerated, true)) {
                    continue;
                }
                if (str_starts_with($predicate, Namespaces::API)) {
                    $violations[] = new GraphViolation(\sprintf('%s is set by the server, not through a document.', $predicate), $predicate, $subjectName);
                    continue;
                }
                $info = $this->vocabulary->property($predicate);
                if ($info === null) {
                    $violations[] = new GraphViolation(\sprintf('"%s" is not a property of the ontology.', $predicate), $predicate, $subjectName);
                    continue;
                }
                if (!$this->vocabulary->accepts($types, $predicate)) {
                    $violations[] = new GraphViolation(\sprintf('%s does not accept %s.', implode(', ', $types), $predicate), $predicate, $subjectName);
                    continue;
                }
                $object = $triple->object;
                if ($info->kind === PropertyKind::Datatype) {
                    if (!$object instanceof Literal) {
                        $violations[] = new GraphViolation(\sprintf('%s takes a literal value.', $predicate), $predicate, $subjectName);
                    } elseif ($object->language === null && !Xsd::satisfies($object->datatype, $info->ranges)) {
                        $violations[] = new GraphViolation(\sprintf('%s expects %s, got %s.', $predicate, implode(' or ', $info->ranges), $object->datatype), $predicate, $subjectName);
                    } elseif (!Xsd::lexicallyValid($object)) {
                        $violations[] = new GraphViolation(\sprintf('"%s" is not a valid %s.', $object->lexical, $object->datatype), $predicate, $subjectName);
                    }
                    continue;
                }
                if ($object instanceof Literal) {
                    $violations[] = new GraphViolation(\sprintf('%s takes an object or reference, not a literal.', $predicate), $predicate, $subjectName);
                    continue;
                }
                // The target's classes, when the graph knows them, must be the range or below it. A reference
                // to a node outside the graph (another logistics object, a code-list member) carries no classes
                // here and is not judged; a class the ontology does not know is reported on the node itself.
                if (!$object instanceof Iri && !$object instanceof BlankNode) {
                    continue;
                }
                $targetTypes = array_values(array_filter(array_map(static fn(Iri $t): string => $t->value, $graph->typesOf($object)), fn(string $t): bool => $this->vocabulary->isClass($t)));
                if ($targetTypes !== [] && $info->ranges !== [] && !$this->withinRanges($targetTypes, $info->ranges)) {
                    $violations[] = new GraphViolation(\sprintf('%s expects %s, got %s.', $predicate, implode(' or ', $info->ranges), implode(', ', $targetTypes)), $predicate, $subjectName);
                }
            }
        }

        return $violations;
    }

    /**
     * @param list<string> $types
     * @param list<string> $ranges
     */
    private function withinRanges(array $types, array $ranges): bool
    {
        foreach ($types as $type) {
            foreach ($ranges as $range) {
                if ($type === $range || $this->vocabulary->isSubclassOf($type, $range)) {
                    return true;
                }
            }
        }

        return false;
    }
}
