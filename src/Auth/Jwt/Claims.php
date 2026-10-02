<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

use DateTimeImmutable;
use Exception;

/**
 * The verified payload of a token. ONE Record requires iss, exp and
 * logistics_agent_uri; everything else is whatever the identity provider put in.
 */
final readonly class Claims
{
    public const string LOGISTICS_AGENT_URI = 'logistics_agent_uri';

    /**
     * @param array<string, mixed> $all
     */
    public function __construct(public array $all) {}

    public function get(string $name): mixed
    {
        return $this->all[$name] ?? null;
    }

    public function issuer(): ?string
    {
        return \is_string($this->all['iss'] ?? null) ? $this->all['iss'] : null;
    }

    public function subject(): ?string
    {
        return \is_string($this->all['sub'] ?? null) ? $this->all['sub'] : null;
    }

    /**
     * @return list<string>
     */
    public function audience(): array
    {
        $aud = $this->all['aud'] ?? null;
        if (\is_string($aud)) {
            return [$aud];
        }
        if (\is_array($aud)) {
            return array_values(array_filter($aud, is_string(...)));
        }

        return [];
    }

    public function expiresAt(): ?int
    {
        return self::timestamp($this->all['exp'] ?? null);
    }

    public function notBefore(): ?int
    {
        return self::timestamp($this->all['nbf'] ?? null);
    }

    public function issuedAt(): ?int
    {
        return self::timestamp($this->all['iat'] ?? null);
    }

    public function tokenId(): ?string
    {
        return \is_string($this->all['jti'] ?? null) ? $this->all['jti'] : null;
    }

    /**
     * The URI of the cargo:LogisticsAgent the caller acts as; the claim ONE Record adds to OAuth.
     */
    public function logisticsAgentUri(): ?string
    {
        $value = $this->all[self::LOGISTICS_AGENT_URI] ?? null;

        return \is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * RFC 7519 wants NumericDate; the spec's own example writes exp as an ISO
     * string, so both are read.
     */
    private static function timestamp(mixed $value): ?int
    {
        if (\is_int($value)) {
            return $value;
        }
        if (\is_float($value)) {
            return (int) $value;
        }
        if (\is_string($value) && $value !== '') {
            if (preg_match('/^\d+$/', $value) === 1) {
                return (int) $value;
            }
            try {
                return (new DateTimeImmutable($value))->getTimestamp();
            } catch (Exception) {
                return null;
            }
        }

        return null;
    }
}
