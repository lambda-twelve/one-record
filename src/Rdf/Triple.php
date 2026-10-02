<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Rdf;

final readonly class Triple
{
    public function __construct(
        public Iri|BlankNode $subject,
        public Iri $predicate,
        public Term $object,
    ) {}

    public function toNTriples(): string
    {
        return $this->subject->toNTriples() . ' ' . $this->predicate->toNTriples() . ' ' . $this->object->toNTriples() . ' .';
    }

    public function equals(self $other): bool
    {
        return $this->subject->equals($other->subject)
            && $this->predicate->equals($other->predicate)
            && $this->object->equals($other->object);
    }
}
