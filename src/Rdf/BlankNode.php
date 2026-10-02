<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

use InvalidArgumentException;

/**
 * A node without a global identifier. Labels are only meaningful inside one
 * graph; two graphs are compared modulo a relabelling of their blank nodes.
 */
final readonly class BlankNode implements Term
{
    public function __construct(public string $label)
    {
        if (preg_match('/^[A-Za-z0-9_][A-Za-z0-9_.-]*$/', $label) !== 1) {
            throw new InvalidArgumentException(\sprintf('Not a valid blank node label: "%s".', $label));
        }
    }

    public function toNTriples(): string
    {
        return '_:' . $this->label;
    }

    public function equals(Term $other): bool
    {
        return $other instanceof self && $other->label === $this->label;
    }
}
