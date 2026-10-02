<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

final readonly class ClassDef
{
    /**
     * @param list<string> $parents IRIs of named superclasses
     * @param array<string, ?string> $properties property IRI => allValuesFrom IRI when the class restricts it
     */
    public function __construct(
        public string $iri,
        public string $name,
        public array $parents,
        public array $properties,
        public bool $deprecated,
        public ?string $comment,
    ) {}
}
