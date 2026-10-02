<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

final readonly class IndividualDef
{
    /**
     * @param list<string> $types class IRIs (owl:NamedIndividual itself excluded)
     */
    public function __construct(
        public string $iri,
        public string $name,
        public array $types,
        public ?string $comment,
    ) {}
}
