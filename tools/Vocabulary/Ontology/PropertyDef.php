<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology;

final readonly class PropertyDef
{
    /** Marker domain meaning "any class" (the ontology writes "Domain owl:Thing"). */
    public const string ANY_DOMAIN = '*';

    /**
     * @param list<string> $ranges IRIs; several when the ontology declares a union
     * @param list<string> $domains class IRIs, or [ANY_DOMAIN]
     */
    public function __construct(
        public string $iri,
        public string $name,
        public PropertyKind $kind,
        public array $ranges,
        public array $domains,
        public bool $deprecated,
        public ?string $comment,
    ) {}
}
