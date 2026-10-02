<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary;

final readonly class IndividualInfo
{
    /**
     * @param list<string> $types class IRIs
     */
    public function __construct(
        public string $iri,
        public string $name,
        public array $types,
        public string $since,
        public ?string $removedIn,
    ) {}
}
