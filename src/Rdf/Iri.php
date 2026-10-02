<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

use InvalidArgumentException;

final readonly class Iri implements Term
{
    public function __construct(public string $value)
    {
        if ($value === '' || preg_match('/[\s<>"{}|\\\\^`]/', $value) === 1) {
            throw new InvalidArgumentException(\sprintf('Not a valid IRI: "%s".', $value));
        }
    }

    public function toNTriples(): string
    {
        return '<' . $this->value . '>';
    }

    public function equals(Term $other): bool
    {
        return $other instanceof self && $other->value === $this->value;
    }

    /**
     * The part after the last '#' or '/': "Piece" for cargo:Piece. Used for
     * generated constant names and compact JSON-LD keys.
     */
    public function localName(): string
    {
        $cut = max((int) strrpos($this->value, '#'), (int) strrpos($this->value, '/'));

        return substr($this->value, $cut + 1);
    }

    public function namespace(): string
    {
        return substr($this->value, 0, \strlen($this->value) - \strlen($this->localName()));
    }

    public function startsWith(string $prefix): bool
    {
        return str_starts_with($this->value, $prefix);
    }
}
