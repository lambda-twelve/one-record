<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

use InvalidArgumentException;
use OpenSSLAsymmetricKey;
use Psr\Clock\ClockInterface;
use RuntimeException;

/**
 * Issues RS256 tokens: for a host acting as its partners' identity provider
 * (the token endpoint), and for a client authenticating to a server that
 * trusts the host's key directly.
 */
final class Rs256Signer
{
    private OpenSSLAsymmetricKey $privateKey;

    /** @var callable(int): string */
    private $randomBytes;

    /**
     * @param string $privateKeyPem a PEM RSA private key; "\n" escapes are accepted for keys passed through environment variables
     * @param ?callable(int): string $randomBytes source for token ids, defaults to random_bytes
     */
    public function __construct(
        string $privateKeyPem,
        private readonly string $issuer,
        private readonly ClockInterface $clock,
        private readonly ?string $keyId = null,
        ?callable $randomBytes = null,
    ) {
        $pem = str_replace('\n', "\n", $privateKeyPem);
        $key = $pem === '' ? false : openssl_pkey_get_private($pem);
        if ($key === false) {
            throw new InvalidArgumentException('The private key is not a PEM RSA private key.');
        }
        $details = openssl_pkey_get_details($key);
        if ($details === false || $details['type'] !== OPENSSL_KEYTYPE_RSA || $details['bits'] < 2048) {
            throw new InvalidArgumentException('RS256 needs an RSA key of at least 2048 bits.');
        }
        $this->privateKey = $key;
        $this->randomBytes = $randomBytes ?? static fn(int $length): string => random_bytes(max(1, $length));
    }

    /**
     * @param array<string, mixed> $claims added to the standard claims; iss, iat, nbf, exp and jti cannot be overridden
     */
    public function sign(array $claims, int $ttlSeconds): string
    {
        $now = $this->clock->now()->getTimestamp();
        $payload = [
            ...$claims,
            'iss' => $this->issuer,
            'iat' => $now,
            'nbf' => $now,
            'exp' => $now + $ttlSeconds,
            'jti' => bin2hex(($this->randomBytes)(16)),
        ];
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        if ($this->keyId !== null) {
            $header['kid'] = $this->keyId;
        }
        $signed = Jwk::base64UrlEncode(json_encode($header, JSON_THROW_ON_ERROR)) . '.'
            . Jwk::base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES));
        if (!openssl_sign($signed, $signature, $this->privateKey, OPENSSL_ALGO_SHA256)) {
            throw new RuntimeException('Signing failed.');
        }

        return $signed . '.' . Jwk::base64UrlEncode($signature);
    }

    public function issuer(): string
    {
        return $this->issuer;
    }

    public function keyId(): ?string
    {
        return $this->keyId;
    }

    /**
     * The public half, in PEM: what partners (or NE:ONE) are configured to trust.
     */
    public function publicKeyPem(): string
    {
        $details = openssl_pkey_get_details($this->privateKey);
        if ($details === false || !\is_string($details['key'] ?? null)) {
            throw new RuntimeException('Cannot derive the public key.');
        }

        return $details['key'];
    }

    /**
     * The public key as a JWK, for a JWKS document other servers can fetch.
     *
     * @return array<string, string>
     */
    public function publicJwk(): array
    {
        $details = openssl_pkey_get_details($this->privateKey);
        $rsa = $details === false ? null : ($details['rsa'] ?? null);
        if (!\is_array($rsa) || !\is_string($rsa['n'] ?? null) || !\is_string($rsa['e'] ?? null)) {
            throw new RuntimeException('Cannot derive the public key.');
        }
        $jwk = ['kty' => 'RSA', 'use' => 'sig', 'alg' => 'RS256', 'n' => Jwk::base64UrlEncode($rsa['n']), 'e' => Jwk::base64UrlEncode($rsa['e'])];
        if ($this->keyId !== null) {
            $jwk['kid'] = $this->keyId;
        }

        return $jwk;
    }
}
