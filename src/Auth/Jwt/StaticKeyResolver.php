<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

/**
 * Trusted issuers configured in code: each with one PEM public key, a list
 * of them, or a map of key id to PEM. This is what a host with a handful of
 * partners needs; JwksKeyResolver covers identity providers that publish keys.
 */
final class StaticKeyResolver implements KeyResolver
{
    /** @var array<string, array<string|int, string>> */
    private array $keys = [];

    /**
     * @param array<string, string|list<string>|array<string, string>> $issuers issuer => PEM, PEMs, or kid => PEM
     */
    public function __construct(array $issuers)
    {
        foreach ($issuers as $issuer => $keys) {
            $this->keys[$issuer] = \is_string($keys) ? [$keys] : $keys;
        }
    }

    public function publicKeys(string $issuer, ?string $keyId): array
    {
        $keys = $this->keys[$issuer] ?? [];
        if ($keyId !== null && isset($keys[$keyId])) {
            return [$keys[$keyId]];
        }

        return array_values($keys);
    }
}
