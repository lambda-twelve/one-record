<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

/**
 * Several resolvers as one: pinned PEM keys for some issuers, JWKS documents
 * for others, the host's own signing key for its own token endpoint. The
 * first resolver that knows the issuer answers; the rest are not consulted,
 * so an issuer is trusted by exactly one source.
 */
final class ChainKeyResolver implements KeyResolver
{
    /** @var list<KeyResolver> */
    private readonly array $resolvers;

    public function __construct(KeyResolver ...$resolvers)
    {
        $this->resolvers = array_values($resolvers);
    }

    public function publicKeys(string $issuer, ?string $keyId): array
    {
        foreach ($this->resolvers as $resolver) {
            $keys = $resolver->publicKeys($issuer, $keyId);
            if ($keys !== []) {
                return $keys;
            }
        }

        return [];
    }
}
