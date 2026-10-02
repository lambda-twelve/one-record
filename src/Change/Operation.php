<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Rdf\BlankNode;
use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * api:Operation: add or delete one triple. The subject is the logistics
 * object, one of its embedded objects, or a blank node this change adds.
 */
final readonly class Operation
{
    public function __construct(
        public OperationKind $kind,
        public Iri|BlankNode $subject,
        public Iri $predicate,
        public OperationObject $object,
    ) {}

    public static function add(Iri|BlankNode $subject, Iri $predicate, OperationObject $object): self
    {
        return new self(OperationKind::Add, $subject, $predicate, $object);
    }

    public static function delete(Iri|BlankNode $subject, Iri $predicate, OperationObject $object): self
    {
        return new self(OperationKind::Delete, $subject, $predicate, $object);
    }

    /**
     * The subject as it appears in api:s: an IRI, or a `_:label` string.
     */
    public function subjectString(): string
    {
        return $this->subject instanceof Iri ? $this->subject->value : $this->subject->toNTriples();
    }
}
