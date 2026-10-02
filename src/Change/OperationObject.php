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

    /**
     * @throws ChangeException for a language-tagged literal: api:hasDatatype/api:hasValue cannot carry the tag
     *                         (spec question 29), and writing it as xsd:string would silently change the value
     */
    public static function literal(Literal $literal): self
    {
        if ($literal->language !== null) {
            throw ChangeException::because('Invalid resource', \sprintf('"%s"@%s is a language-tagged literal; an api:Change cannot express the language tag, so the value cannot be added or deleted through a change.', $literal->lexical, $literal->language));
        }

        return new self($literal->datatype, $literal->lexical);
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
