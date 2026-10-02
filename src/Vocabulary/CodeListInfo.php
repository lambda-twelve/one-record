<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary;

final readonly class CodeListInfo
{
    /**
     * @param array<string, string> $codes code => version that introduced it
     */
    public function __construct(
        public string $iri,
        public string $name,
        public bool $open,
        public string $since,
        public array $codes,
    ) {}

    public function iriOf(string $code): string
    {
        return $this->iri . '#' . $code;
    }

    public function hasCode(string $code): bool
    {
        return isset($this->codes[$code]);
    }
}
