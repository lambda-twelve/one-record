<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LogicException;

/**
 * Writes a graph back as compacted JSON-LD, rooted at one node.
 *
 * Every node that has triples of its own is embedded where it is first
 * referenced (identified nodes keep their @id, blank nodes get none unless
 * they are referenced twice), later references become {"@id": ...}, and a
 * node that only has rdf:type triples is written as a typed reference. Keys
 * and values are sorted, so the same graph always yields the same JSON. The
 * output expands back to the same graph.
 */
final class Writer
{
    /** @var array<string, true> */
    private array $visited = [];

    /** @var array<string, int> */
    private array $references = [];

    public function __construct(
        private readonly bool $nativeLiterals = true,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function write(Graph $graph, Iri|BlankNode $root, Context $context, bool $includeContext = true): array
    {
        $this->visited = [];
        $this->references = [];
        foreach ($graph as $triple) {
            if ($triple->object instanceof BlankNode) {
                $key = $triple->object->toNTriples();
                $this->references[$key] = ($this->references[$key] ?? 0) + 1;
            }
        }

        $object = $this->nodeObject($graph, $root, $context, isRoot: true);
        if ($includeContext) {
            $raw = $context->toRaw();
            if ($raw !== []) {
                $object = ['@context' => $raw, ...$object];
            }
        }

        return $object;
    }

    /**
     * @return array<string, mixed>
     */
    private function nodeObject(Graph $graph, Iri|BlankNode $node, Context $context, bool $isRoot): array
    {
        $this->visited[$node->toNTriples()] = true;
        $out = [];
        if ($node instanceof Iri) {
            $out['@id'] = $context->compactIri($node->value, vocabRelative: false);
        } elseif (($this->references[$node->toNTriples()] ?? 0) > 1 || ($isRoot && ($this->references[$node->toNTriples()] ?? 0) >= 1)) {
            // A blank root that anything references (itself included) needs a label, or the reference
            // becomes a different anonymous node on reading (AR-009).
            $out['@id'] = $node->toNTriples();
        }

        $types = [];
        /** @var array<string, list<Term>> $properties */
        $properties = [];
        foreach ($graph->about($node) as $triple) {
            if ($triple->predicate->value === Graph::RDF_TYPE && $triple->object instanceof Iri) {
                $types[] = $context->compactIri($triple->object->value);
                continue;
            }
            $properties[$triple->predicate->value][] = $triple->object;
        }
        if ($types !== []) {
            sort($types, SORT_STRING);
            $out['@type'] = \count($types) === 1 ? $types[0] : $types;
        }

        $compactKeys = [];
        foreach ($properties as $predicate => $values) {
            // Of the keys that read back as this predicate, the shortest whose coercion fits every value;
            // a key coerced to @id would turn a string into an IRI (R2-002, R3-002). Two predicates can
            // never share a key, since every candidate expands to its own predicate.
            $chosen = null;
            foreach ($context->keyCandidates($predicate) as $candidate) {
                $coercion = $context->coercionOfTerm($candidate);
                if ($coercion === null || self::valuesFit($values, $coercion)) {
                    $chosen = [$predicate, $coercion];
                    $key = $candidate;
                    break;
                }
            }
            if ($chosen === null || !isset($key)) {
                throw JsonLdException::at('', \sprintf('"%s" cannot be written in this context without changing its values.', $predicate));
            }
            if (isset($compactKeys[$key])) {
                throw new LogicException(\sprintf('Key "%s" chosen for two predicates.', $key));
            }
            $compactKeys[$key] = $chosen;
        }
        ksort($compactKeys, SORT_STRING);
        foreach ($compactKeys as $key => [$predicate, $coercion]) {
            $values = $properties[$predicate];
            usort($values, static fn(Term $a, Term $b): int => strcmp($a->toNTriples(), $b->toNTriples()));
            $written = array_map(fn(Term $term): mixed => $this->value($graph, $term, $context, $coercion), $values);
            $out[$key] = \count($written) === 1 ? $written[0] : $written;
        }

        return $out;
    }

    /**
     * @param list<Term> $values
     */
    private static function valuesFit(array $values, string $coercion): bool
    {
        foreach ($values as $value) {
            if ($coercion === Context::JSON_LD_ID) {
                if ($value instanceof Literal) {
                    return false;
                }
                continue;
            }
            if (!$value instanceof Literal || $value->language !== null || $value->datatype !== $coercion) {
                return false;
            }
        }

        return true;
    }

    private function value(Graph $graph, Term $term, Context $context, ?string $coercion): mixed
    {
        if ($term instanceof Literal) {
            return $this->literal($term, $context, $coercion);
        }
        if (!$term instanceof Iri && !$term instanceof BlankNode) {
            throw new LogicException('Unknown term type ' . $term::class);
        }
        $about = $graph->about($term);
        $alreadyWritten = isset($this->visited[$term->toNTriples()]);
        $onlyTypes = $about !== [] && array_filter($about, static fn(Triple $t): bool => $t->predicate->value !== Graph::RDF_TYPE) === [];

        if ($about === [] || $alreadyWritten) {
            if ($term instanceof Iri && $coercion === Context::JSON_LD_ID) {
                return $context->compactIri($term->value, vocabRelative: false);
            }

            return ['@id' => $term instanceof Iri ? $context->compactIri($term->value, vocabRelative: false) : $term->toNTriples()];
        }
        if ($onlyTypes && $term instanceof Iri) {
            $this->visited[$term->toNTriples()] = true;
            $types = array_map(static fn(Triple $t): string => $context->compactIri($t->object instanceof Iri ? $t->object->value : ''), $about);
            sort($types, SORT_STRING);

            return ['@id' => $context->compactIri($term->value, vocabRelative: false), '@type' => \count($types) === 1 ? $types[0] : $types];
        }

        return $this->nodeObject($graph, $term, $context, isRoot: false);
    }

    private function literal(Literal $literal, Context $context, ?string $coercion): mixed
    {
        if ($literal->language !== null) {
            return $literal->language === $context->language
                ? $literal->lexical
                : ['@language' => $literal->language, '@value' => $literal->lexical];
        }
        if ($coercion !== null && $coercion === $literal->datatype) {
            return $literal->lexical;
        }
        if ($literal->isString()) {
            // A plain string in a document with a default language would read back language-tagged.
            return $context->language === null ? $literal->lexical : ['@type' => $context->compactIri(Literal::XSD_STRING), '@value' => $literal->lexical];
        }
        if ($this->nativeLiterals) {
            $native = self::native($literal);
            if ($native !== null) {
                return $native;
            }
        }

        return ['@type' => $context->compactIri($literal->datatype), '@value' => $literal->lexical];
    }

    /**
     * Only lexical forms that survive a round trip through JSON become native values.
     */
    private static function native(Literal $literal): bool|int|float|null
    {
        return match ($literal->datatype) {
            Literal::XSD_BOOLEAN => match ($literal->lexical) {
                'true' => true,
                'false' => false,
                default => null,
            },
            Literal::XSD_INTEGER => preg_match('/^(0|-?[1-9][0-9]*)$/', $literal->lexical) === 1 && (string) (int) $literal->lexical === $literal->lexical
                ? (int) $literal->lexical
                : null,
            Literal::XSD_DOUBLE => is_numeric($literal->lexical) && Literal::formatDouble((float) $literal->lexical) === $literal->lexical
                ? (float) $literal->lexical
                : null,
            default => null,
        };
    }
}
