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

    /**
     * @throws JwtException when the claim is present but not a time
     */
    public function expiresAt(): ?float
    {
        return self::timestamp($this->all['exp'] ?? null, 'exp');
    }

    /**
     * @throws JwtException when the claim is present but not a time
     */
    public function notBefore(): ?float
    {
        return self::timestamp($this->all['nbf'] ?? null, 'nbf');
    }

    /**
     * @throws JwtException when the claim is present but not a time
     */
    public function issuedAt(): ?float
    {
        return self::timestamp($this->all['iat'] ?? null, 'iat');
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
     * RFC 7519 wants NumericDate, kept with its fraction; the spec's own
     * example writes exp as an ISO string, so an absolute RFC 3339 instant
     * with a zone is read too. Nothing else: a relative expression would get
     * a new instant at every parse from the process clock, and a present but
     * malformed claim is a refusal, never "absent" (R7-005).
     *
     * @throws JwtException
     */
    private static function timestamp(mixed $value, string $claim): ?float
    {
        if ($value === null) {
            return null;
        }
        if (\is_int($value) || \is_float($value)) {
            if (!is_finite((float) $value)) {
                throw new JwtException(JwtException::INVALID_CLAIM, \sprintf('The %s claim is not a time.', $claim));
            }

            return (float) $value;
        }
        if (\is_string($value)) {
            if (preg_match('/^\d{1,12}(\.\d+)?$/', $value) === 1) {
                return (float) $value;
            }
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})$/', $value, $m) === 1 && checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
                try {
                    return (float) (new DateTimeImmutable($value))->format('U.u');
                } catch (Exception) {
                    // falls through to the refusal
                }
            }
        }

        throw new JwtException(JwtException::INVALID_CLAIM, \sprintf('The %s claim is not a time.', $claim));
    }
}
