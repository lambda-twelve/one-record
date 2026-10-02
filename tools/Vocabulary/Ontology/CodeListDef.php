<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

final readonly class CodeListDef
{
    /**
     * @param array<string, ?string> $codes code => rdfs:comment (the human meaning)
     */
    public function __construct(
        public string $iri,
        public string $name,
        public bool $open,
        public ?string $comment,
        public array $codes,
    ) {}
}
