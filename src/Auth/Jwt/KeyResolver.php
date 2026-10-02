<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

/**
 * Where the public keys of trusted issuers come from. Keys never come from
 * the token itself (no jku, no x5u, no embedded jwk): a token can only name a
 * key the host already trusts.
 */
interface KeyResolver
{
    /**
     * Candidate public keys (PEM) for a token from $issuer, narrowed to $keyId
     * when the token names one and the resolver knows it. An empty list means
     * the issuer is not trusted.
     *
     * @return list<string>
     */
    public function publicKeys(string $issuer, ?string $keyId): array;
}
