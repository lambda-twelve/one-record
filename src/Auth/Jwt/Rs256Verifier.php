<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

use JsonException;
use Psr\Clock\ClockInterface;

/**
 * Verifies RS256 JSON Web Tokens and nothing else.
 *
 * The algorithm is fixed, the keys come from the resolver (never from the
 * token), issuer and expiry are mandatory, not-before is honoured with a
 * small leeway for clock skew, and the audience is checked when the host
 * expects one. Every JWT weakness in the wild starts with trusting the
 * header, so nothing in it is negotiable here.
 */
final class Rs256Verifier
{
    public function __construct(
        private readonly KeyResolver $keys,
        private readonly ClockInterface $clock,
        private readonly ?string $expectedAudience = null,
        private readonly int $leewaySeconds = 30,
    ) {}

    public function verify(string $token): Claims
    {
        $parts = explode('.', $token);
        if (\count($parts) !== 3 || $parts[0] === '' || $parts[1] === '' || $parts[2] === '') {
            throw new JwtException(JwtException::MALFORMED, 'A JWT has three non-empty parts.');
        }
        $header = self::decodeJson($parts[0]);
        $payload = self::decodeJson($parts[1]);
        $signature = Jwk::base64UrlDecode($parts[2]);
        if ($signature === '') {
            throw new JwtException(JwtException::MALFORMED, 'The signature is not valid base64url.');
        }

        if (($header['alg'] ?? null) !== 'RS256') {
            throw new JwtException(JwtException::UNSUPPORTED_ALGORITHM, 'Only RS256 tokens are accepted.');
        }
        if (isset($header['typ']) && (!\is_string($header['typ']) || !\in_array(strtolower($header['typ']), ['jwt', 'at+jwt', 'application/at+jwt'], true))) {
            throw new JwtException(JwtException::UNSUPPORTED_ALGORITHM, 'Unsupported token type.');
        }
        if (isset($header['crit'])) {
            throw new JwtException(JwtException::UNSUPPORTED_ALGORITHM, 'Critical header extensions are not supported.');
        }

        $claims = new Claims($payload);
        $issuer = $claims->issuer();
        if ($issuer === null) {
            throw new JwtException(JwtException::MISSING_CLAIM, 'The token has no issuer (iss).');
        }
        $keyId = \is_string($header['kid'] ?? null) ? $header['kid'] : null;
        $candidates = $this->keys->publicKeys($issuer, $keyId);
        if ($candidates === []) {
            throw new JwtException(JwtException::UNKNOWN_ISSUER, 'The token issuer is not trusted.');
        }

        $signed = $parts[0] . '.' . $parts[1];
        $verified = false;
        foreach ($candidates as $pem) {
            $key = openssl_pkey_get_public($pem);
            if ($key === false) {
                continue;
            }
            if (openssl_verify($signed, $signature, $key, OPENSSL_ALGO_SHA256) === 1) {
                $verified = true;
                break;
            }
        }
        if (!$verified) {
            throw new JwtException(JwtException::BAD_SIGNATURE, 'The token signature does not verify against the issuer\'s keys.');
        }

        $now = $this->clock->now()->getTimestamp();
        $exp = $claims->expiresAt();
        if ($exp === null) {
            throw new JwtException(JwtException::MISSING_CLAIM, 'The token has no expiry (exp).');
        }
        if ($exp <= $now - $this->leewaySeconds) {
            throw new JwtException(JwtException::EXPIRED, 'The token has expired.');
        }
        $nbf = $claims->notBefore();
        if ($nbf !== null && $nbf > $now + $this->leewaySeconds) {
            throw new JwtException(JwtException::NOT_YET_VALID, 'The token is not valid yet.');
        }
        if ($this->expectedAudience !== null && !\in_array($this->expectedAudience, $claims->audience(), true)) {
            throw new JwtException(JwtException::AUDIENCE_MISMATCH, 'The token is not meant for this server.');
        }

        return $claims;
    }

    /**
     * @return array<string, mixed>
     */
    private static function decodeJson(string $segment): array
    {
        $json = Jwk::base64UrlDecode($segment);
        if ($json === '') {
            throw new JwtException(JwtException::MALFORMED, 'A token segment is not valid base64url.');
        }
        try {
            $decoded = json_decode($json, true, 16, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            throw new JwtException(JwtException::MALFORMED, 'A token segment is not valid JSON.');
        }
        if (!\is_array($decoded) || array_is_list($decoded)) {
            throw new JwtException(JwtException::MALFORMED, 'A token segment must be a JSON object.');
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }
}
