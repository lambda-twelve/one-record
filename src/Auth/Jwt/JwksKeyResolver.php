<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

use InvalidArgumentException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Psr\SimpleCache\CacheInterface;
use Throwable;

/**
 * Public keys from an identity provider's JWKS document, as the spec's
 * security section describes: each trusted issuer maps to a JWKS URL (or the
 * well-known location under the issuer), documents are cached, and a token
 * naming a key id the cache does not know triggers one refresh so key
 * rotation needs no restart. Without a cache every token costs a fetch; a
 * cache that fails is logged and bypassed.
 */
final class JwksKeyResolver implements KeyResolver
{
    /**
     * @param array<string, string|null> $issuers issuer => JWKS URL, or null for {issuer}/.well-known/jwks.json
     */
    public function __construct(
        private readonly array $issuers,
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requests,
        private readonly ?CacheInterface $cache = null,
        private readonly int $ttlSeconds = 3600,
        private readonly LoggerInterface $logger = new NullLogger(),
        private readonly int $refreshCooldownSeconds = 60,
    ) {}

    /** @var array<string, float> issuer => when an unknown key id last forced a refresh */
    private array $refreshedAt = [];

    public function publicKeys(string $issuer, ?string $keyId): array
    {
        if (!\array_key_exists($issuer, $this->issuers)) {
            return [];
        }
        $keys = $this->load($issuer, refresh: false);
        if ($keyId !== null && !isset($keys[$keyId])) {
            // Rotation needs one refresh; a stream of forged key ids must not become a stream of
            // fetches against the issuer (AR-025). One refresh per issuer per cooldown.
            $now = microtime(true);
            if (!isset($this->refreshedAt[$issuer]) || $now - $this->refreshedAt[$issuer] >= $this->refreshCooldownSeconds) {
                $this->refreshedAt[$issuer] = $now;
                $keys = $this->load($issuer, refresh: true);
            }
        }
        if ($keyId !== null && isset($keys[$keyId])) {
            return [$keys[$keyId]];
        }

        return array_values($keys);
    }

    /**
     * @return array<string, string> kid (or ordinal) => PEM
     */
    private function load(string $issuer, bool $refresh): array
    {
        $cacheKey = 'one-record.jwks.' . hash('sha256', $issuer);
        if (!$refresh && $this->cache !== null) {
            try {
                $cached = $this->cache->get($cacheKey);
            } catch (Throwable $e) {
                // A broken cache must not refuse tokens; it only costs a fetch.
                $this->logger->warning('JWKS cache read failed', ['issuer' => $issuer, 'error' => $e->getMessage()]);
                $cached = null;
            }
            if (\is_array($cached)) {
                /** @var array<string, string> $cached */
                return $cached;
            }
        }

        $url = $this->issuers[$issuer] ?? rtrim($issuer, '/') . '/.well-known/jwks.json';
        try {
            $response = $this->http->sendRequest($this->requests->createRequest('GET', $url)->withHeader('Accept', 'application/json'));
        } catch (ClientExceptionInterface $e) {
            $this->logger->warning('JWKS fetch failed', ['issuer' => $issuer, 'error' => $e->getMessage()]);

            return [];
        }
        if ($response->getStatusCode() !== 200) {
            $this->logger->warning('JWKS fetch returned an error', ['issuer' => $issuer, 'status' => $response->getStatusCode()]);

            return [];
        }
        $document = json_decode((string) $response->getBody(), true);
        $keys = [];
        if (\is_array($document) && \is_array($document['keys'] ?? null)) {
            foreach ($document['keys'] as $index => $jwk) {
                if (!\is_array($jwk) || ($jwk['kty'] ?? null) !== 'RSA' || (($jwk['use'] ?? 'sig') !== 'sig') || (isset($jwk['alg']) && $jwk['alg'] !== 'RS256')) {
                    continue;
                }
                try {
                    /** @var array<string, mixed> $jwk */
                    $keys[\is_string($jwk['kid'] ?? null) ? $jwk['kid'] : 'k' . $index] = Jwk::rsaToPem($jwk);
                } catch (InvalidArgumentException $e) {
                    $this->logger->warning('JWKS contains an unusable key', ['issuer' => $issuer, 'error' => $e->getMessage()]);
                }
            }
        }
        if ($this->cache !== null) {
            try {
                $this->cache->set($cacheKey, $keys, $this->ttlSeconds);
            } catch (Throwable $e) {
                $this->logger->warning('JWKS cache write failed', ['issuer' => $issuer, 'error' => $e->getMessage()]);
            }
        }

        return $keys;
    }
}
