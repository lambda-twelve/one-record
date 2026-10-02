<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

/**
 * What one ontology file says, reduced to the facts the generator emits.
 */
final readonly class OntologyModel
{
    /**
     * @param array<string, ClassDef> $classes keyed by IRI
     * @param array<string, PropertyDef> $properties keyed by IRI
     * @param array<string, IndividualDef> $individuals keyed by IRI
     * @param array<string, CodeListDef> $codeLists keyed by IRI
     */
    public function __construct(
        public string $ontologyIri,
        public string $versionInfo,
        public ?string $versionIri,
        public array $classes,
        public array $properties,
        public array $individuals,
        public array $codeLists,
    ) {}
}
