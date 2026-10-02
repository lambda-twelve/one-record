<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * An authenticated caller: the logistics agent (an Organization URI on the
 * ONE Record network) the request acts as, plus who vouched for it and what
 * else the credential said, for access policies that need more.
 */
final readonly class Agent
{
    /**
     * @param array<string, mixed> $claims
     */
    public function __construct(
        public Iri $iri,
        public ?string $issuer = null,
        public array $claims = [],
    ) {}

    public function is(Iri|string $other): bool
    {
        return $this->iri->value === ($other instanceof Iri ? $other->value : $other);
    }
}
