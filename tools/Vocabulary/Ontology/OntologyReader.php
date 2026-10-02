<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use RuntimeException;

/**
 * Reads IATA's OWL ontologies (cargo, API, code lists) from a parsed graph.
 *
 * IATA attaches properties to classes with owl:Restriction blocks rather than
 * rdfs:domain (the cargo ontology has no rdfs:domain at all, only a textual
 * "Domain :X" annotation), and publishes code lists as classes whose members
 * are named individuals, closed lists additionally enumerated with owl:oneOf.
 * This reader knows those conventions so the generator does not have to.
 */
final class OntologyReader
{
    private const string OWL = 'http://www.w3.org/2002/07/owl#';
    private const string RDFS = 'http://www.w3.org/2000/01/rdf-schema#';

    public function read(Graph $graph): OntologyModel
    {
        [$ontologyIri, $versionInfo, $versionIri] = $this->header($graph);
        $termNamespace = $this->termNamespace($ontologyIri);

        $classes = [];
        $codeLists = [];
        $properties = [];
        $individuals = [];

        foreach ($graph->subjects() as $subject) {
            if (!$subject instanceof Iri) {
                continue;
            }
            $types = array_map(static fn(Iri $t): string => $t->value, $graph->typesOf($subject));

            if (\in_array(self::OWL . 'Class', $types, true)) {
                if ($subject->startsWith(Namespaces::CODE_LISTS)) {
                    $codeLists[$subject->value] = $this->codeList($graph, $subject);
                } elseif ($subject->startsWith($termNamespace)) {
                    $classes[$subject->value] = $this->class($graph, $subject, $termNamespace);
                }
            }
            if (\in_array(self::OWL . 'ObjectProperty', $types, true) && $subject->startsWith($termNamespace)) {
                $properties[$subject->value] = $this->property($graph, $subject, PropertyKind::Object, $termNamespace);
            }
            if (\in_array(self::OWL . 'DatatypeProperty', $types, true) && $subject->startsWith($termNamespace)) {
                $properties[$subject->value] = $this->property($graph, $subject, PropertyKind::Datatype, $termNamespace);
            }
            if (\in_array(self::OWL . 'NamedIndividual', $types, true) && !$subject->startsWith(Namespaces::CODE_LISTS)) {
                $individuals[$subject->value] = new IndividualDef(
                    $subject->value,
                    $subject->localName(),
                    array_values(array_filter($types, static fn(string $t): bool => $t !== self::OWL . 'NamedIndividual')),
                    $this->comment($graph, $subject),
                );
            }
        }

        // Code-list members are individuals typed with their list(s); open lists have no owl:oneOf.
        foreach ($graph->subjects() as $subject) {
            if (!$subject instanceof Iri || !$subject->startsWith(Namespaces::CODE_LISTS) || !str_contains($subject->value, '#')) {
                continue;
            }
            foreach ($graph->typesOf($subject) as $type) {
                if (!isset($codeLists[$type->value])) {
                    continue;
                }
                $list = $codeLists[$type->value];
                $codes = $list->codes;
                $codes[$subject->localName()] = $this->comment($graph, $subject);
                $codeLists[$type->value] = new CodeListDef($list->iri, $list->name, $list->open, $list->comment, $codes);
            }
        }
        foreach ($codeLists as $iri => $list) {
            $codes = $list->codes;
            ksort($codes, SORT_STRING);
            $codeLists[$iri] = new CodeListDef($list->iri, $list->name, $list->open, $list->comment, $codes);
        }

        ksort($classes, SORT_STRING);
        ksort($properties, SORT_STRING);
        ksort($individuals, SORT_STRING);
        ksort($codeLists, SORT_STRING);

        return new OntologyModel($ontologyIri, $versionInfo, $versionIri, $classes, $properties, $individuals, $codeLists);
    }

    /**
     * @return array{string, string, ?string}
     */
    private function header(Graph $graph): array
    {
        foreach ($graph->subjects() as $subject) {
            if (!$subject instanceof Iri) {
                continue;
            }
            foreach ($graph->typesOf($subject) as $type) {
                if ($type->value === self::OWL . 'Ontology') {
                    $info = $graph->firstObject($subject, self::OWL . 'versionInfo');
                    $versionIri = $graph->firstObject($subject, self::OWL . 'versionIRI');

                    return [
                        $subject->value,
                        $info instanceof Literal ? $info->lexical : throw new RuntimeException('Ontology without owl:versionInfo.'),
                        $versionIri instanceof Iri ? $versionIri->value : null,
                    ];
                }
            }
        }

        throw new RuntimeException('No owl:Ontology header found.');
    }

    /**
     * The ontology IRI is unversioned and lacks the '#' or '/' terms hang off.
     */
    private function termNamespace(string $ontologyIri): string
    {
        return match ($ontologyIri) {
            Namespaces::CARGO_ONTOLOGY => Namespaces::CARGO,
            Namespaces::API_ONTOLOGY => Namespaces::API,
            Namespaces::CODE_LISTS_ONTOLOGY => Namespaces::CODE_LISTS,
            default => $ontologyIri . '#',
        };
    }

    private function class(Graph $graph, Iri $class, string $termNamespace): ClassDef
    {
        $parents = [];
        $properties = [];
        foreach ($graph->objects($class, self::RDFS . 'subClassOf') as $parent) {
            if ($parent instanceof Iri) {
                if ($parent->startsWith($termNamespace)) {
                    $parents[] = $parent->value;
                }
                continue;
            }
            if (!$parent instanceof BlankNode || !$this->hasType($graph, $parent, self::OWL . 'Restriction')) {
                continue;
            }
            $onProperty = $graph->firstObject($parent, self::OWL . 'onProperty');
            if (!$onProperty instanceof Iri) {
                continue;
            }
            $allValuesFrom = $graph->firstObject($parent, self::OWL . 'allValuesFrom');
            $range = $allValuesFrom instanceof Iri ? $allValuesFrom->value : null;
            // Several restrictions may mention the same property (allValuesFrom + maxCardinality); keep the range.
            $properties[$onProperty->value] = $range ?? ($properties[$onProperty->value] ?? null);
        }
        sort($parents, SORT_STRING);
        ksort($properties, SORT_STRING);

        return new ClassDef(
            $class->value,
            $class->localName(),
            $parents,
            $properties,
            $this->isDeprecated($graph, $class),
            $this->comment($graph, $class),
        );
    }

    private function property(Graph $graph, Iri $property, PropertyKind $kind, string $termNamespace): PropertyDef
    {
        $ranges = [];
        foreach ($graph->objects($property, self::RDFS . 'range') as $range) {
            foreach ($this->classExpression($graph, $range) as $iri) {
                $ranges[] = $iri;
            }
        }
        $domains = [];
        foreach ($graph->objects($property, self::RDFS . 'domain') as $domain) {
            foreach ($this->classExpression($graph, $domain) as $iri) {
                $domains[] = $iri;
            }
        }
        if ($domains === []) {
            // The cargo ontology states the domain only in prose: owl:comment "Domain :Piece".
            foreach ([self::OWL . 'comment', self::RDFS . 'comment'] as $predicate) {
                foreach ($graph->objects($property, $predicate) as $note) {
                    if ($note instanceof Literal && preg_match('/^Domain\s+(.+)$/', trim($note->lexical), $m) === 1) {
                        $tokens = preg_split('/[\s,]+/', $m[1]);
                        if ($tokens === false) {
                            continue;
                        }
                        foreach ($tokens as $token) {
                            if ($token === 'owl:Thing') {
                                $domains[] = PropertyDef::ANY_DOMAIN;
                            } elseif (str_starts_with($token, ':')) {
                                $domains[] = $termNamespace . substr($token, 1);
                            }
                        }
                    }
                }
            }
        }

        return new PropertyDef(
            $property->value,
            $property->localName(),
            $kind,
            array_values(array_unique($ranges)),
            array_values(array_unique($domains)),
            $this->isDeprecated($graph, $property),
            $this->comment($graph, $property, excludeDomainNote: true),
        );
    }

    private function codeList(Graph $graph, Iri $list): CodeListDef
    {
        $codes = [];
        $closed = false;
        foreach ($graph->objects($list, self::OWL . 'equivalentClass') as $equivalent) {
            if (!$equivalent instanceof BlankNode) {
                continue;
            }
            $oneOf = $graph->firstObject($equivalent, self::OWL . 'oneOf');
            if ($oneOf === null) {
                continue;
            }
            $closed = true;
            foreach ($graph->listItems($oneOf) as $member) {
                if ($member instanceof Iri) {
                    $codes[$member->localName()] = $this->comment($graph, $member);
                }
            }
        }

        return new CodeListDef($list->value, $list->localName(), !$closed, $this->comment($graph, $list), $codes);
    }

    /**
     * Named class, or the members of an owl:unionOf, or the base of a derived rdfs:Datatype.
     *
     * @return list<string>
     */
    private function classExpression(Graph $graph, Term $expression): array
    {
        if ($expression instanceof Iri) {
            return [$expression->value];
        }
        if (!$expression instanceof BlankNode) {
            return [];
        }
        $union = $graph->firstObject($expression, self::OWL . 'unionOf');
        if ($union !== null) {
            $out = [];
            foreach ($graph->listItems($union) as $item) {
                foreach ($this->classExpression($graph, $item) as $iri) {
                    $out[] = $iri;
                }
            }

            return $out;
        }
        $onDatatype = $graph->firstObject($expression, self::OWL . 'onDatatype');
        if ($onDatatype instanceof Iri) {
            return [$onDatatype->value];
        }

        return [];
    }

    private function hasType(Graph $graph, Iri|BlankNode $subject, string $type): bool
    {
        foreach ($graph->typesOf($subject) as $t) {
            if ($t->value === $type) {
                return true;
            }
        }

        return false;
    }

    private function isDeprecated(Graph $graph, Iri $subject): bool
    {
        $flag = $graph->firstObject($subject, self::OWL . 'deprecated');

        return $flag instanceof Literal && $flag->lexical === 'true';
    }

    private function comment(Graph $graph, Iri $subject, bool $excludeDomainNote = false): ?string
    {
        $comments = [];
        foreach ($graph->objects($subject, self::RDFS . 'comment') as $comment) {
            if (!$comment instanceof Literal) {
                continue;
            }
            $text = trim($comment->lexical);
            if ($text === '' || ($excludeDomainNote && str_starts_with($text, 'Domain '))) {
                continue;
            }
            $comments[] = $text;
        }

        return $comments === [] ? null : implode(' ', $comments);
    }
}
