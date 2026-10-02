<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model\Builder;

use LambdaTwelve\OneRecord\Model\LocalRef;
use LambdaTwelve\OneRecord\Model\LogisticsObject;
use LambdaTwelve\OneRecord\Model\ModelException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\PropertyInfo;
use LambdaTwelve\OneRecord\Vocabulary\PropertyKind;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;

/**
 * Builds a logistics object property by property, checking each one against
 * the ontology: the class must exist and be a logistics object, the property
 * must be accepted by the class, and the value must match the property's
 * kind (a literal for datatype properties, a node for object properties).
 * Give it a version-limited Vocabulary to refuse terms a partner on an older
 * data model would not understand.
 */
final class ObjectBuilder
{
    private readonly Vocabulary $vocabulary;

    /** @var array<string, list<Literal|Iri|LocalRef|Embedded>> */
    private array $properties = [];

    private int $blankCounter = 0;

    /**
     * @param list<string> $types
     */
    private function __construct(
        private readonly array $types,
        ?Vocabulary $vocabulary,
        private readonly bool $strict,
    ) {
        $this->vocabulary = $vocabulary ?? Vocabulary::default();
        foreach ($types as $type) {
            if ($strict && !$this->vocabulary->isClass($type)) {
                throw new ModelException(\sprintf('"%s" is not a class of the ontology.', $type));
            }
        }
        if ($strict && array_filter($types, fn(string $t): bool => $this->vocabulary->isLogisticsObjectClass($t)) === []) {
            throw new ModelException(\sprintf('%s is not a logistics object class; embedded objects are built with Embedded::of().', implode(', ', $types)));
        }
    }

    /**
     * @param ?Vocabulary $vocabulary the terms allowed, e.g. Vocabulary::for(DataModelVersion::V3_2)
     */
    public static function of(string $type, ?Vocabulary $vocabulary = null): self
    {
        return new self([$type], $vocabulary, strict: true);
    }

    /**
     * Several types, e.g. [Company, Organization, LogisticsAgent, LogisticsObject] as IATA's examples do.
     *
     * @param non-empty-list<string> $types
     */
    public static function ofTypes(array $types, ?Vocabulary $vocabulary = null): self
    {
        return new self($types, $vocabulary, strict: true);
    }

    /**
     * No ontology checks: for terms outside the vocabulary (a host extension)
     * or when reproducing a partner's document verbatim.
     *
     * @param non-empty-list<string> $types
     */
    public static function unchecked(array $types): self
    {
        return new self($types, null, strict: false);
    }

    /**
     * @return $this
     */
    public function set(string $property, mixed $value): self
    {
        $this->properties[$property] = [$this->check($property, Values::term($value))];

        return $this;
    }

    /**
     * @return $this
     */
    public function add(string $property, mixed $value): self
    {
        $this->properties[$property][] = $this->check($property, Values::term($value));

        return $this;
    }

    /**
     * @return $this
     */
    public function setIf(string $property, mixed $value): self
    {
        return $value === null ? $this : $this->set($property, $value);
    }

    /**
     * @param iterable<mixed> $values
     * @return $this
     */
    public function addAll(string $property, iterable $values): self
    {
        foreach ($values as $value) {
            $this->add($property, $value);
        }

        return $this;
    }

    public function build(Iri $iri): LogisticsObject
    {
        return new LogisticsObject($iri, $this->buildGraph($iri));
    }

    /**
     * The triples with the given subject (a real URI or a local:<key> placeholder).
     */
    public function buildGraph(Iri $subject): Graph
    {
        $this->blankCounter = 0;
        $graph = new Graph();
        foreach ($this->types as $type) {
            $graph->add(new Triple($subject, new Iri(Graph::RDF_TYPE), new Iri($type)));
        }
        $this->write($graph, $subject, $this->properties);

        return $graph;
    }

    /**
     * @param array<string, list<Literal|Iri|LocalRef|Embedded>> $properties
     */
    private function write(Graph $graph, Iri|BlankNode $subject, array $properties): void
    {
        foreach ($properties as $property => $values) {
            foreach ($values as $value) {
                $graph->add(new Triple($subject, new Iri($property), $this->term($graph, $value)));
            }
        }
    }

    private function term(Graph $graph, Literal|Iri|LocalRef|Embedded $value): Literal|Iri|BlankNode
    {
        if ($value instanceof LocalRef) {
            return $value->iri();
        }
        if ($value instanceof Embedded) {
            $node = new BlankNode('e' . $this->blankCounter++);
            foreach ($value->types() as $type) {
                $graph->add(new Triple($node, new Iri(Graph::RDF_TYPE), new Iri($type)));
            }
            $this->write($graph, $node, $value->properties());

            return $node;
        }

        return $value;
    }

    private function check(string $property, Literal|Iri|LocalRef|Embedded $value): Literal|Iri|LocalRef|Embedded
    {
        $this->checkOn($this->types, $property, $value);

        return $value;
    }

    /**
     * @param list<string> $types
     */
    private function checkOn(array $types, string $property, Literal|Iri|LocalRef|Embedded $value): void
    {
        if (!$this->strict) {
            return;
        }
        $info = $this->vocabulary->property($property);
        if ($info === null) {
            throw new ModelException(\sprintf('"%s" is not a property of the ontology%s.', $property, $this->vocabulary->ceiling() !== null ? ' (data model ' . $this->vocabulary->ceiling()->value . ')' : ''));
        }
        if (!$this->vocabulary->accepts($types, $property)) {
            throw new ModelException(\sprintf('%s does not accept %s%s.', implode(', ', array_map(self::short(...), $types)), self::short($property), $this->vocabulary->ceiling() !== null ? ' in data model ' . $this->vocabulary->ceiling()->value : ''));
        }
        $this->checkKind($info, $value);
        if ($value instanceof Embedded) {
            foreach ($value->types() as $type) {
                if (!$this->vocabulary->isClass($type)) {
                    throw new ModelException(\sprintf('"%s" is not a class of the ontology.', $type));
                }
                if ($this->vocabulary->isLogisticsObjectClass($type)) {
                    throw new ModelException(\sprintf('%s is a logistics object class; publish it as its own object and refer to it with Values::ref().', self::short($type)));
                }
            }
            foreach ($value->properties() as $nested => $items) {
                foreach ($items as $item) {
                    $this->checkOn($value->types(), $nested, $item);
                }
            }
        }
    }

    private function checkKind(PropertyInfo $info, Literal|Iri|LocalRef|Embedded $value): void
    {
        if ($info->kind === PropertyKind::Datatype && !$value instanceof Literal) {
            throw new ModelException(\sprintf('%s takes a literal value, not an object or reference.', self::short($info->iri)));
        }
        if ($info->kind === PropertyKind::Object && $value instanceof Literal) {
            throw new ModelException(\sprintf('%s takes an object, reference or code-list IRI, not a literal.', self::short($info->iri)));
        }
        if ($info->kind === PropertyKind::Datatype && $value instanceof Literal && $info->ranges !== [] && $value->language === null
            && !\in_array($value->datatype, $info->ranges, true) && !self::compatible($value->datatype, $info->ranges)) {
            throw new ModelException(\sprintf('%s expects %s, got %s.', self::short($info->iri), implode(' or ', array_map(self::short(...), $info->ranges)), self::short($value->datatype)));
        }
    }

    /**
     * xsd:integer satisfies xsd:double (a whole number is a number); nothing else is lenient.
     *
     * @param list<string> $ranges
     */
    private static function compatible(string $datatype, array $ranges): bool
    {
        return $datatype === Literal::XSD_INTEGER && (\in_array(Literal::XSD_DOUBLE, $ranges, true) || \in_array(Literal::XSD_DECIMAL, $ranges, true));
    }

    private static function short(string $iri): string
    {
        foreach ([Namespaces::CARGO => 'cargo:', Namespaces::API => 'api:', Namespaces::XSD => 'xsd:', Namespaces::CODE_LISTS => 'codes:'] as $ns => $prefix) {
            if (str_starts_with($iri, $ns)) {
                return $prefix . substr($iri, \strlen($ns));
            }
        }

        return $iri;
    }
}
