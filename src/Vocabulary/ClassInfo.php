<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary;

final readonly class ClassInfo
{
    /**
     * @param list<string> $parents IRIs of direct named superclasses
     * @param array<string, string> $ownProperties property IRI => version that attached it to this class
     */
    public function __construct(
        public string $iri,
        public string $name,
        public array $parents,
        public array $ownProperties,
        public string $since,
        public ?string $deprecatedIn,
        public ?string $removedIn,
    ) {}
}
