<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model\Builder;

use LambdaTwelve\OneRecord\Model\LocalRef;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Rdf\Literal;

/**
 * An object that lives inside a logistics object (cargo:Value, Dimensions,
 * Party, Address, CodeListElement, ...): a type and properties, written as a
 * blank node when the parent is built. Validated against the vocabulary with
 * the parent.
 */
final class Embedded
{
    /** @var array<string, list<Literal|Iri|LocalRef|Embedded>> property IRI => values */
    private array $properties = [];

    /**
     * @param list<string> $types
     */
    private function __construct(private readonly array $types) {}

    public static function of(string $type, string ...$moreTypes): self
    {
        return new self(array_values([$type, ...$moreTypes]));
    }

    /**
     * @return list<string>
     */
    public function types(): array
    {
        return $this->types;
    }

    /**
     * Replace the property's values with one value.
     *
     * @return $this
     */
    public function set(string $property, mixed $value): self
    {
        $this->properties[$property] = [Values::term($value)];

        return $this;
    }

    /**
     * Add one more value to a property.
     *
     * @return $this
     */
    public function add(string $property, mixed $value): self
    {
        $this->properties[$property][] = Values::term($value);

        return $this;
    }

    /**
     * Set a property only when the value is not null; keeps mappers free of ifs.
     *
     * @return $this
     */
    public function setIf(string $property, mixed $value): self
    {
        return $value === null ? $this : $this->set($property, $value);
    }

    /**
     * @return array<string, list<Literal|Iri|LocalRef|Embedded>>
     */
    public function properties(): array
    {
        return $this->properties;
    }
}
