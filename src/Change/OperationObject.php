<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Rdf\Literal;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LogicException;

/**
 * api:OperationObject: the object of a triple as a (datatype, value) pair.
 * An XSD datatype means a literal; any other datatype is the class of the
 * node the value identifies (a logistics object URI, an embedded object id,
 * or a blank node label `_:b0` for an object this change introduces).
 */
final readonly class OperationObject
{
    public function __construct(
        public string $datatype,
        public string $value,
    ) {}

    public static function literal(Literal $literal): self
    {
        return new self($literal->language !== null ? Literal::XSD_STRING : $literal->datatype, $literal->lexical);
    }

    public function isLiteral(): bool
    {
        return str_starts_with($this->datatype, Namespaces::XSD);
    }

    public function isBlankNode(): bool
    {
        return !$this->isLiteral() && str_starts_with($this->value, '_:');
    }

    public function toLiteral(): Literal
    {
        if (!$this->isLiteral()) {
            throw new LogicException('Not a literal operation object.');
        }

        return new Literal($this->value, $this->datatype);
    }
}
