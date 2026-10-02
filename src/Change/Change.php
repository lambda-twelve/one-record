<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\JsonLd\Context;
use LambdaTwelve\OneRecord\JsonLd\ExpandedDocument;
use LambdaTwelve\OneRecord\JsonLd\Json;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Rdf\Term;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:Change: the body of PATCH /logistics-objects/{id}.
 *
 * `revision` is the revision of the logistics object the change was written
 * against (what the requester last saw); the holder applies it only while
 * that is still the current revision, and the result becomes revision + 1.
 * This is what NE:ONE enforces and what the "outdated revision" example in
 * the spec implies (spec question 14).
 */
final readonly class Change
{
    /**
     * @param non-empty-list<Operation> $operations
     * @param list<Iri> $verificationRequests
     */
    public function __construct(
        public Iri $logisticsObject,
        public int $revision,
        public array $operations,
        public ?string $description = null,
        public bool $notifyRequestStatusChange = false,
        public array $verificationRequests = [],
    ) {
        if ($revision < 1) {
            throw new ChangeException('A change must name the revision it applies to (1 or higher).');
        }
    }

    /**
     * @param string|array<string, mixed>|ExpandedDocument $document
     */
    public static function fromJsonLd(string|array|ExpandedDocument $document): self
    {
        try {
            $expanded = $document instanceof ExpandedDocument ? $document : JsonLd::expand($document);
        } catch (JsonLdException $e) {
            throw ChangeException::because('Invalid body request', $e->getMessage());
        }
        $graph = $expanded->graph;
        $root = $expanded->root;
        if (!\in_array(Api::Change, $expanded->rootTypes(), true)) {
            throw ChangeException::because('Invalid resource', 'The body is not an api:Change.');
        }

        $target = $graph->firstObject($root, Api::hasLogisticsObject);
        if (!$target instanceof Iri) {
            throw ChangeException::because('Invalid resource', 'api:hasLogisticsObject must reference the logistics object by IRI.', Api::hasLogisticsObject);
        }
        $revisionTerm = $graph->firstObject($root, Api::hasRevision);
        if (!$revisionTerm instanceof Literal || preg_match('/^\+?\d+$/', $revisionTerm->lexical) !== 1 || (int) $revisionTerm->lexical < 1) {
            throw ChangeException::because('Invalid resource', 'api:hasRevision must be a positive integer.', Api::hasRevision);
        }
        $description = $graph->firstObject($root, Api::hasDescription);
        $notify = $graph->firstObject($root, Api::notifyRequestStatusChange);
        $verifications = [];
        foreach ($graph->objects($root, Api::hasVerificationRequest) as $term) {
            if ($term instanceof Iri) {
                $verifications[] = $term;
            }
        }

        $operations = [];
        foreach ($graph->objects($root, Api::hasOperation) as $node) {
            if (!$node instanceof Iri && !$node instanceof BlankNode) {
                throw ChangeException::because('Invalid resource', 'api:hasOperation must contain api:Operation objects.', Api::hasOperation);
            }
            foreach (self::operationsFrom($graph, $node) as $operation) {
                $operations[] = $operation;
            }
        }
        if ($operations === []) {
            throw ChangeException::because('Invalid resource', 'A change must contain at least one operation.', Api::hasOperation);
        }

        return new self(
            $target,
            (int) $revisionTerm->lexical,
            $operations,
            $description instanceof Literal ? $description->lexical : null,
            $notify instanceof Literal && $notify->lexical === 'true',
            $verifications,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toJsonLd(): array
    {
        $operations = [];
        foreach ($this->operations as $operation) {
            $operations[] = [
                '@type' => 'api:Operation',
                'api:op' => ['@id' => 'api:' . ($operation->kind === OperationKind::Add ? 'ADD' : 'DELETE')],
                'api:s' => $operation->subjectString(),
                'api:p' => $operation->predicate->value,
                'api:o' => [[
                    '@type' => 'api:OperationObject',
                    'api:hasDatatype' => $operation->object->datatype,
                    'api:hasValue' => $operation->object->value,
                ]],
            ];
        }
        $document = [
            '@context' => [
                'cargo' => Namespaces::CARGO,
                'api' => Namespaces::API,
                'xsd' => Namespaces::XSD,
                'api:hasDatatype' => ['@type' => 'xsd:anyURI'],
                'api:p' => ['@type' => 'xsd:anyURI'],
            ],
            '@type' => 'api:Change',
            'api:hasLogisticsObject' => ['@id' => $this->logisticsObject->value],
        ];
        if ($this->description !== null) {
            $document['api:hasDescription'] = $this->description;
        }
        $document['api:hasOperation'] = $operations;
        $document['api:hasRevision'] = ['@type' => 'xsd:positiveInteger', '@value' => (string) $this->revision];
        if ($this->notifyRequestStatusChange) {
            $document['api:notifyRequestStatusChange'] = true;
        }
        if ($this->verificationRequests !== []) {
            $document['api:hasVerificationRequest'] = array_map(static fn(Iri $iri): array => ['@id' => $iri->value], $this->verificationRequests);
        }

        return $document;
    }

    public function toJson(bool $pretty = true): string
    {
        return Json::encode($this->toJsonLd(), $pretty);
    }

    public static function context(): Context
    {
        return Context::fromRaw((new self(new Iri('https://example.invalid/x'), 1, [Operation::add(new Iri('https://example.invalid/x'), new Iri(Namespaces::CARGO . 'x'), new OperationObject(Literal::XSD_STRING, ''))]))->toJsonLd()['@context']);
    }

    /**
     * @return list<string> the property IRIs this change touches on the logistics object itself
     */
    public function changedProperties(): array
    {
        $properties = [];
        foreach ($this->operations as $operation) {
            if ($operation->subject->equals($this->logisticsObject)) {
                $properties[$operation->predicate->value] = true;
            }
        }
        $list = array_keys($properties);
        sort($list, SORT_STRING);

        return $list;
    }

    /**
     * @return list<Operation>
     */
    private static function operationsFrom(Graph $graph, Iri|BlankNode $node): array
    {
        $kindTerm = $graph->firstObject($node, Api::op);
        $kind = $kindTerm instanceof Iri ? OperationKind::tryFrom($kindTerm->value) : null;
        if ($kind === null) {
            throw ChangeException::because('Invalid resource', 'api:op must be api:ADD or api:DELETE.', Api::op);
        }
        $subject = self::nodeFrom($graph->firstObject($node, Api::s), Api::s);
        $predicateTerm = $graph->firstObject($node, Api::p);
        $predicate = match (true) {
            $predicateTerm instanceof Iri => $predicateTerm,
            $predicateTerm instanceof Literal && Context::isAbsoluteIri($predicateTerm->lexical) => new Iri($predicateTerm->lexical),
            default => throw ChangeException::because('Invalid resource', 'api:p must be the IRI of a property.', Api::p),
        };

        $objects = $graph->objects($node, Api::o);
        if ($objects === []) {
            throw ChangeException::because('Invalid resource', 'An operation needs an api:o object.', Api::o);
        }
        $operations = [];
        foreach ($objects as $objectNode) {
            if (!$objectNode instanceof Iri && !$objectNode instanceof BlankNode) {
                throw ChangeException::because('Invalid resource', 'api:o must be an api:OperationObject.', Api::o);
            }
            $datatype = $graph->firstObject($objectNode, Api::hasDatatype);
            $value = $graph->firstObject($objectNode, Api::hasValue);
            $datatypeIri = match (true) {
                $datatype instanceof Iri => $datatype->value,
                $datatype instanceof Literal && Context::isAbsoluteIri($datatype->lexical) => $datatype->lexical,
                default => throw ChangeException::because('Invalid resource', 'api:hasDatatype must be an IRI.', Api::hasDatatype),
            };
            $valueString = match (true) {
                $value instanceof Literal => $value->lexical,
                $value instanceof Iri => $value->value,
                default => throw ChangeException::because('Invalid resource', 'api:hasValue is required.', Api::hasValue),
            };
            $operations[] = new Operation($kind, $subject, $predicate, new OperationObject($datatypeIri, $valueString));
        }

        return $operations;
    }

    private static function nodeFrom(?Term $term, string $property): Iri|BlankNode
    {
        if ($term instanceof Iri) {
            return $term;
        }
        if ($term instanceof BlankNode) {
            return $term;
        }
        if ($term instanceof Literal) {
            if (str_starts_with($term->lexical, '_:') && \strlen($term->lexical) > 2) {
                return new BlankNode(substr($term->lexical, 2));
            }
            if (Context::isAbsoluteIri($term->lexical)) {
                return new Iri($term->lexical);
            }
        }

        throw ChangeException::because('Invalid resource', 'api:s must be the IRI of the logistics object, an embedded object id, or a blank node label.', $property);
    }
}
