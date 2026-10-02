<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\SimpleCache\CacheInterface;
use SensitiveParameter;
use Throwable;

/**
 * OAuth 2.0 client credentials, as the ONE Record security model prescribes:
 * POST grant_type=client_credentials to the partner's token endpoint, keep
 * the token until shortly before it expires, then fetch a new one. Tokens are
 * cached per endpoint and client id (PSR-16 when given, else in this object).
 */
final class ClientCredentialsTokenProvider implements TokenProvider
{
    /** @var array<string, array{token: string, expiresAt: int}> */
    private array $memory = [];

    /**
     * @param ?string $scope the scope or audience parameter some token endpoints require (sent as `scope`)
     * @param bool $basicAuth send the credentials as HTTP Basic (client_secret_basic) instead of form fields
     * @param int $refreshMarginSeconds how long before expiry a token is considered stale
     */
    public function __construct(
        private readonly ClientInterface $http,
        private readonly RequestFactoryInterface $requests,
        private readonly StreamFactoryInterface $streams,
        private readonly ClockInterface $clock,
        private readonly string $tokenUrl,
        private readonly string $clientId,
        #[SensitiveParameter]
        private readonly string $clientSecret,
        private readonly ?CacheInterface $cache = null,
        private readonly ?string $scope = null,
        private readonly bool $basicAuth = false,
        private readonly int $refreshMarginSeconds = 60,
    ) {}

    public function token(string $serverEndpoint): string
    {
        $key = 'one-record.token.' . hash('sha256', $this->tokenUrl . '|' . $this->clientId . '|' . ($this->scope ?? ''));
        $now = $this->clock->now()->getTimestamp();
        $cached = $this->load($key);
        if ($cached !== null && $cached['expiresAt'] - $this->refreshMarginSeconds > $now) {
            return $cached['token'];
        }

        $fresh = $this->fetch();
        $this->store($key, $fresh, max(1, $fresh['expiresAt'] - $now));

        return $fresh['token'];
    }

    /**
     * @return array{token: string, expiresAt: int}
     */
    private function fetch(): array
    {
        $fields = ['grant_type' => 'client_credentials'];
        if ($this->scope !== null) {
            $fields['scope'] = $this->scope;
        }
        $request = $this->requests->createRequest('POST', $this->tokenUrl)
            ->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->withHeader('Accept', 'application/json');
        if ($this->basicAuth) {
            // RFC 6749 §2.3.1: each part is form-encoded before the pair is base64-encoded, so a colon or
            // percent in a credential survives (AR-019).
            $request = $request->withHeader('Authorization', 'Basic ' . base64_encode(urlencode($this->clientId) . ':' . urlencode($this->clientSecret)));
        } else {
            $fields['client_id'] = $this->clientId;
            $fields['client_secret'] = $this->clientSecret;
        }
        $request = $request->withBody($this->streams->createStream(http_build_query($fields, '', '&', PHP_QUERY_RFC3986)));

        try {
            $response = $this->http->sendRequest($request);
        } catch (ClientExceptionInterface $e) {
            throw new ClientException(\sprintf('Token request to %s failed: %s', $this->tokenUrl, $e->getMessage()), 0, $e);
        }
        $body = (string) $response->getBody();
        if ($response->getStatusCode() !== 200) {
            // OAuth error bodies carry "error"; never echo the body, it may contain more than that.
            $error = null;
            try {
                $decoded = json_decode($body, true, 16, JSON_THROW_ON_ERROR);
                $error = \is_array($decoded) && \is_string($decoded['error'] ?? null) ? $decoded['error'] : null;
            } catch (Throwable) {
            }
            throw new ClientException(\sprintf('Token endpoint %s answered %d%s.', $this->tokenUrl, $response->getStatusCode(), $error !== null ? ' (' . $error . ')' : ''));
        }
        try {
            $decoded = json_decode($body, true, 16, JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            throw new ClientException(\sprintf('Token endpoint %s did not answer with JSON.', $this->tokenUrl), 0, $e);
        }
        if (!\is_array($decoded)) {
            throw new ClientException(\sprintf('Token endpoint %s did not answer with a JSON object.', $this->tokenUrl));
        }
        $token = $decoded['access_token'] ?? null;
        if (!\is_string($token) || $token === '') {
            throw new ClientException(\sprintf('Token endpoint %s answered without an access_token.', $this->tokenUrl));
        }
        $type = $decoded['token_type'] ?? 'Bearer';
        if (!\is_string($type) || strcasecmp($type, 'Bearer') !== 0) {
            throw new ClientException(\sprintf('Token endpoint %s issued a token of type "%s"; only Bearer is usable.', $this->tokenUrl, \is_string($type) ? $type : \gettype($type)));
        }
        $expiresIn = is_numeric($decoded['expires_in'] ?? null) ? (int) $decoded['expires_in'] : 3600;

        return ['token' => $token, 'expiresAt' => $this->clock->now()->getTimestamp() + $expiresIn];
    }

    /**
     * @return ?array{token: string, expiresAt: int}
     */
    private function load(string $key): ?array
    {
        if ($this->cache === null) {
            return $this->memory[$key] ?? null;
        }
        try {
            $value = $this->cache->get($key);
        } catch (Throwable) {
            return null;
        }

        return \is_array($value) && \is_string($value['token'] ?? null) && \is_int($value['expiresAt'] ?? null)
            ? ['token' => $value['token'], 'expiresAt' => $value['expiresAt']]
            : null;
    }

    /**
     * @param array{token: string, expiresAt: int} $value
     */
    private function store(string $key, array $value, int $ttl): void
    {
        if ($this->cache === null) {
            $this->memory[$key] = $value;

            return;
        }
        try {
            $this->cache->set($key, $value, $ttl);
        } catch (Throwable) {
            // A cache that cannot store only costs an extra token request next time.
            $this->memory[$key] = $value;
        }
    }
}
