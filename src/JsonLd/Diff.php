<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use LambdaTwelve\OneRecord\Rdf\Triple;

/**
 * The outcome of comparing two graphs: the triples (in canonical form) each
 * side has that the other lacks. Empty on both sides means isomorphic.
 */
final readonly class Diff
{
    /**
     * @param list<Triple> $onlyInLeft
     * @param list<Triple> $onlyInRight
     */
    public function __construct(
        public array $onlyInLeft,
        public array $onlyInRight,
    ) {}

    public function isEqual(): bool
    {
        return $this->onlyInLeft === [] && $this->onlyInRight === [];
    }

    /**
     * A readable report for test output and interop logs.
     */
    public function describe(): string
    {
        if ($this->isEqual()) {
            return 'The graphs are isomorphic.';
        }
        $lines = [];
        foreach ($this->onlyInLeft as $triple) {
            $lines[] = '- ' . $triple->toNTriples();
        }
        foreach ($this->onlyInRight as $triple) {
            $lines[] = '+ ' . $triple->toNTriples();
        }

        return implode("\n", $lines);
    }
}
