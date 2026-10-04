<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

/**
 * One way a graph fails the ontology, with the property and subject it
 * concerns so a caller can point at them in an error document.
 */
final readonly class GraphViolation
{
    public function __construct(
        public string $message,
        public ?string $property,
        public ?string $subject,
    ) {}
}
