<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Rdf\Triple;

/**
 * Turns a compacted JSON-LD document into triples.
 *
 * Supported: one inline object @context at the root, @id, @type (string or
 * list), properties as terms, compact IRIs or absolute IRIs, native scalars,
 * value objects (@value with @type or @language), arrays as multi-valued
 * properties, embedded objects (blank nodes or identified nodes) and
 * references ({"@id": ...}). Everything else raises JsonLdException, because
 * a construct this reader does not model (lists, reverse properties, named
 * graphs, nested contexts, remote contexts) cannot be processed faithfully.
 *
 * Blank nodes are labelled in document order, so the same document always
 * expands to the same graph.
 */
final class Expander
{
    private const array REJECTED_KEYWORDS = [
        '@graph' => 'Named graphs (@graph)',
        '@list' => 'Ordered lists (@list)',
        '@set' => '@set',
        '@reverse' => 'Reverse properties (@reverse)',
        '@nest' => '@nest',
        '@index' => '@index',
        '@included' => '@included',
        '@json' => 'JSON literals (@json)',
        '@direction' => '@direction',
        '@container' => '@container',
    ];

    private int $counter = 0;

    /** @var array<string, BlankNode> document blank node label => node */
    private array $documentBlankNodes = [];

    /**
     * @param array<string, mixed> $document a decoded JSON object
     */
    public function expand(array $document): ExpandedDocument
    {
        $this->counter = 0;
        $this->documentBlankNodes = [];
        $graph = new Graph();
        $context = Context::fromRaw($document['@context'] ?? null);
        unset($document['@context']);
        $root = $this->node($document, '', $context, $graph);

        return new ExpandedDocument($graph, $root, $context);
    }

    /**
     * @param array<string, mixed> $object
     */
    private function node(array $object, string $path, Context $context, Graph $graph): Iri|BlankNode
    {
        if (\array_key_exists('@context', $object)) {
            throw JsonLdException::unsupported(self::join($path, '@context'), 'A @context inside an embedded object');
        }
        if (\array_key_exists('@value', $object)) {
            throw JsonLdException::at($path, 'A value object (@value) cannot carry other properties');
        }

        $subject = $this->subjectOf($object, $path, $context);

        foreach ($object as $key => $value) {
            $here = self::join($path, $key);
            if ($key === '@id') {
                continue;
            }
            if ($key === '@type') {
                foreach ($this->strings($value, $here) as $type) {
                    $graph->add(new Triple($subject, new Iri(Graph::RDF_TYPE), new Iri($context->expandIri($type, $here, vocabRelative: true))));
                }
                continue;
            }
            if (str_starts_with($key, '@')) {
                throw JsonLdException::unsupported($here, self::REJECTED_KEYWORDS[$key] ?? "The keyword {$key}");
            }

            $predicate = new Iri($context->expandIri($key, $here, vocabRelative: true));
            $coercion = $context->coercionOf($predicate->value);
            $values = \is_array($value) && array_is_list($value) ? $value : [$value];
            foreach ($values as $index => $item) {
                if ($item === null) {
                    continue;
                }
                $itemPath = \is_array($value) && array_is_list($value) ? $here . '[' . $index . ']' : $here;
                if (\is_array($item) && array_is_list($item)) {
                    throw JsonLdException::unsupported($itemPath, 'A nested array (list of lists)');
                }
                $graph->add(new Triple($subject, $predicate, $this->valueTerm($item, $itemPath, $coercion, $context, $graph)));
            }
        }

        return $subject;
    }

    /**
     * @param array<string, mixed> $object
     */
    private function subjectOf(array $object, string $path, Context $context): Iri|BlankNode
    {
        if (!\array_key_exists('@id', $object)) {
            return $this->freshBlankNode();
        }
        $id = $object['@id'];
        if (!\is_string($id) || $id === '') {
            throw JsonLdException::at(self::join($path, '@id'), '@id must be a non-empty string');
        }

        return $this->identifier($id, self::join($path, '@id'), $context);
    }

    private function identifier(string $id, string $path, Context $context): Iri|BlankNode
    {
        if (str_starts_with($id, '_:')) {
            $label = substr($id, 2);
            if ($label === '' || preg_match('/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $label) !== 1) {
                throw JsonLdException::at($path, \sprintf('"%s" is not a valid blank node identifier', $id));
            }

            // Document labels are kept apart from generated ones so neither can collide.
            return $this->documentBlankNodes[$label] ??= new BlankNode('n_' . $label);
        }
        $iri = $context->expandIri($id, $path, vocabRelative: false);
        try {
            return new Iri($iri);
        } catch (InvalidArgumentException $e) {
            throw JsonLdException::at($path, \sprintf('"%s" is not a valid IRI', $iri));
        }
    }

    /**
     * @param ?string $coercion "@id", a datatype IRI, or null, from the context's term definition
     */
    private function valueTerm(mixed $value, string $path, ?string $coercion, Context $context, Graph $graph): Term
    {
        if (\is_string($value)) {
            if ($coercion === Context::JSON_LD_ID) {
                return $this->identifier($value, $path, $context);
            }
            if ($coercion !== null) {
                return new Literal($value, $coercion);
            }

            return $context->language !== null ? new Literal($value, null, $context->language) : Literal::string($value);
        }
        if (\is_bool($value)) {
            return Literal::boolean($value);
        }
        if (\is_int($value)) {
            return Literal::integer($value);
        }
        if (\is_float($value)) {
            return Literal::double($value);
        }
        if (!\is_array($value)) {
            throw JsonLdException::at($path, 'Unsupported JSON value of type ' . get_debug_type($value));
        }
        /** @var array<string, mixed> $value */
        if (\array_key_exists('@value', $value)) {
            return $this->valueObject($value, $path, $context);
        }
        if (\array_key_exists('@id', $value) && \count($value) <= 2 && (\count($value) === 1 || \array_key_exists('@type', $value))) {
            // A reference, possibly typed: {"@id": X, "@type": T}. Its type is a fact about X.
            $node = $this->subjectOf($value, $path, $context);
            if (\array_key_exists('@type', $value)) {
                foreach ($this->strings($value['@type'], self::join($path, '@type')) as $type) {
                    $graph->add(new Triple($node, new Iri(Graph::RDF_TYPE), new Iri($context->expandIri($type, self::join($path, '@type'), vocabRelative: true))));
                }
            }

            return $node;
        }

        return $this->node($value, $path, $context, $graph);
    }

    /**
     * @param array<string, mixed> $object
     */
    private function valueObject(array $object, string $path, Context $context): Literal
    {
        foreach (array_keys($object) as $key) {
            if (!\in_array($key, ['@value', '@type', '@language'], true)) {
                throw JsonLdException::at(self::join($path, $key), 'A value object may only contain @value, @type and @language');
            }
        }
        $raw = $object['@value'];
        if (!\is_string($raw) && !\is_bool($raw) && !\is_int($raw) && !\is_float($raw)) {
            throw JsonLdException::at(self::join($path, '@value'), '@value must be a string, number or boolean');
        }
        $hasType = \array_key_exists('@type', $object);
        $hasLanguage = \array_key_exists('@language', $object);
        if ($hasType && $hasLanguage) {
            throw JsonLdException::at($path, 'A value object cannot have both @type and @language');
        }
        if ($hasLanguage) {
            $language = $object['@language'];
            if (!\is_string($language) || $language === '' || !\is_string($raw)) {
                throw JsonLdException::at(self::join($path, '@language'), 'A language-tagged value must be a string with a non-empty language tag');
            }

            return new Literal($raw, null, $language);
        }
        if ($hasType) {
            $type = $object['@type'];
            if (!\is_string($type) || $type === '') {
                throw JsonLdException::at(self::join($path, '@type'), '@type of a value object must be a non-empty string');
            }

            return new Literal(self::lexical($raw), $context->expandIri($type, self::join($path, '@type'), vocabRelative: true));
        }
        if (\is_string($raw)) {
            return $context->language !== null ? new Literal($raw, null, $context->language) : Literal::string($raw);
        }
        if (\is_bool($raw)) {
            return Literal::boolean($raw);
        }
        if (\is_int($raw)) {
            return Literal::integer($raw);
        }

        return Literal::double($raw);
    }

    private static function lexical(string|int|float|bool $raw): string
    {
        return match (true) {
            \is_bool($raw) => $raw ? 'true' : 'false',
            \is_float($raw) => Literal::formatDouble($raw),
            default => (string) $raw,
        };
    }

    /**
     * @return list<string>
     */
    private function strings(mixed $value, string $path): array
    {
        $values = \is_array($value) && array_is_list($value) ? $value : [$value];
        $out = [];
        foreach ($values as $item) {
            if (!\is_string($item) || $item === '') {
                throw JsonLdException::at($path, '@type values must be non-empty strings');
            }
            $out[] = $item;
        }

        return $out;
    }

    private function freshBlankNode(): BlankNode
    {
        return new BlankNode('b' . $this->counter++);
    }

    private static function join(string $path, string $key): string
    {
        return $path === '' ? $key : $path . '.' . $key;
    }
}
