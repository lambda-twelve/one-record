<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;
use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * Compares two RDF graphs for isomorphism: equal up to a relabelling of blank
 * nodes, with literal values normalised.
 *
 * This is how the NE:ONE interoperability suite judges "same graph", and how
 * tests check that a document survives a round trip. Two servers give the
 * same embedded object different identifiers, so IRIs under configurable
 * prefixes (`internal:` here, `neone:` there) are compared as blank nodes.
 *
 * Blank nodes are labelled by iterated hashing of their neighbourhood; nodes
 * the hashing cannot tell apart are, for ONE Record's tree-shaped documents,
 * structurally identical, and get ordinal labels in a stable order.
 */
final class Comparer
{
    /**
     * @param list<string> $blankNodePrefixes IRI prefixes whose nodes are compared as blank nodes
     */
    public function __construct(
        private readonly array $blankNodePrefixes = [Namespaces::EMBEDDED],
        private readonly bool $normaliseNumbers = true,
        private readonly bool $ignoreLanguageTags = false,
    ) {}

    public function compare(Graph $left, Graph $right): Diff
    {
        $leftCanonical = $this->canonical($left);
        $rightCanonical = $this->canonical($right);
        $leftKeys = array_map(static fn(Triple $t): string => $t->toNTriples(), $leftCanonical->sorted());
        $rightKeys = array_map(static fn(Triple $t): string => $t->toNTriples(), $rightCanonical->sorted());
        $leftOnly = array_values(array_diff($leftKeys, $rightKeys));
        $rightOnly = array_values(array_diff($rightKeys, $leftKeys));

        $byKey = static function (Graph $graph): array {
            $map = [];
            foreach ($graph as $triple) {
                $map[$triple->toNTriples()] = $triple;
            }

            return $map;
        };
        $leftMap = $byKey($leftCanonical);
        $rightMap = $byKey($rightCanonical);

        return new Diff(
            array_map(static fn(string $k): Triple => $leftMap[$k], $leftOnly),
            array_map(static fn(string $k): Triple => $rightMap[$k], $rightOnly),
        );
    }

    public function isomorphic(Graph $left, Graph $right): bool
    {
        return $this->compare($left, $right)->isEqual();
    }

    /**
     * The graph with normalised literals and canonical blank node labels.
     */
    public function canonical(Graph $graph): Graph
    {
        $normalised = new Graph();
        foreach ($graph as $triple) {
            $subject = $this->normaliseNode($triple->subject);
            $object = $this->normaliseTerm($triple->object);
            $normalised->add(new Triple($subject, $triple->predicate, $object));
        }

        $labels = $this->canonicalLabels($normalised);
        if ($labels === []) {
            return $normalised;
        }
        $relabelled = new Graph();
        foreach ($normalised as $triple) {
            $subject = $triple->subject instanceof BlankNode ? new BlankNode($labels[$triple->subject->label]) : $triple->subject;
            $object = $triple->object instanceof BlankNode ? new BlankNode($labels[$triple->object->label]) : $triple->object;
            $relabelled->add(new Triple($subject, $triple->predicate, $object));
        }

        return $relabelled;
    }

    private function normaliseNode(Iri|BlankNode $node): Iri|BlankNode
    {
        if ($node instanceof Iri) {
            foreach ($this->blankNodePrefixes as $prefix) {
                if (str_starts_with($node->value, $prefix)) {
                    return new BlankNode('e' . hash('crc32b', $node->value) . '_' . bin2hex(substr($node->value, \strlen($prefix))));
                }
            }
        }

        return $node;
    }

    private function normaliseTerm(Term $term): Term
    {
        if ($term instanceof Iri || $term instanceof BlankNode) {
            return $this->normaliseNode($term);
        }
        if (!$term instanceof Literal) {
            return $term;
        }

        return $this->normaliseLiteral($term);
    }

    /**
     * The literal in the form this comparer considers canonical: numbers in
     * JSON-LD's canonical lexical forms, booleans as true/false.
     */
    public function normaliseLiteral(Literal $term): Literal
    {
        if ($this->ignoreLanguageTags && $term->language !== null) {
            return Literal::string($term->lexical);
        }
        if (!$this->normaliseNumbers) {
            return $term;
        }

        return match ($term->datatype) {
            Literal::XSD_INTEGER => preg_match('/^[+-]?\d+$/', $term->lexical) === 1
                ? new Literal(self::canonicalInteger($term->lexical), Literal::XSD_INTEGER)
                : $term,
            Literal::XSD_DOUBLE, Literal::XSD_DECIMAL => is_numeric($term->lexical)
                ? new Literal(Literal::formatDouble((float) $term->lexical), Literal::XSD_DOUBLE)
                : $term,
            Literal::XSD_BOOLEAN => match ($term->lexical) {
                '1' => Literal::boolean(true),
                '0' => Literal::boolean(false),
                default => $term,
            },
            default => $term,
        };
    }

    private static function canonicalInteger(string $lexical): string
    {
        $negative = str_starts_with($lexical, '-');
        $digits = ltrim(ltrim($lexical, '+-'), '0');
        if ($digits === '') {
            return '0';
        }

        return ($negative ? '-' : '') . $digits;
    }

    /**
     * @return array<string, string> original label => canonical label
     */
    private function canonicalLabels(Graph $graph): array
    {
        /** @var array<string, BlankNode> $blanks */
        $blanks = [];
        foreach ($graph as $triple) {
            foreach ([$triple->subject, $triple->object] as $term) {
                if ($term instanceof BlankNode) {
                    $blanks[$term->label] = $term;
                }
            }
        }
        if ($blanks === []) {
            return [];
        }

        $hashes = array_fill_keys(array_keys($blanks), '');
        $distinct = 0;
        for ($round = 0; $round < \count($blanks) + 1; $round++) {
            $next = [];
            foreach ($blanks as $label => $node) {
                $parts = [];
                foreach ($graph as $triple) {
                    if ($triple->subject instanceof BlankNode && $triple->subject->label === $label) {
                        $parts[] = 'o ' . $triple->predicate->value . ' ' . $this->termHash($triple->object, $hashes);
                    }
                    if ($triple->object instanceof BlankNode && $triple->object->label === $label) {
                        $parts[] = 'i ' . $triple->predicate->value . ' ' . $this->termHash($triple->subject, $hashes);
                    }
                }
                sort($parts, SORT_STRING);
                $next[$label] = hash('sha256', implode("\n", $parts));
            }
            $hashes = $next;
            $nowDistinct = \count(array_unique($hashes));
            if ($nowDistinct === $distinct) {
                break;
            }
            $distinct = $nowDistinct;
        }

        // Ties are structurally identical nodes; give them ordinal labels in a stable order.
        $groups = [];
        foreach ($hashes as $label => $hash) {
            $groups[$hash][] = $label;
        }
        $labels = [];
        foreach ($groups as $hash => $members) {
            sort($members, SORT_STRING);
            foreach ($members as $index => $label) {
                $labels[$label] = 'c' . substr($hash, 0, 20) . (\count($members) > 1 ? '_' . $index : '');
            }
        }

        return $labels;
    }

    /**
     * @param array<string, string> $hashes
     */
    private function termHash(Term $term, array $hashes): string
    {
        if ($term instanceof BlankNode) {
            return 'b:' . $hashes[$term->label];
        }

        return $term->toNTriples();
    }
}
