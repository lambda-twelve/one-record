<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth;

use LambdaTwelve\OneRecord\Auth\Jwt\Claims;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Signer;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * An OAuth 2.0 client-credentials token endpoint (RFC 6749 section 4.4) that
 * issues the RS256 tokens ONE Record servers authenticate each other with.
 *
 * For a host with a handful of partners this replaces running an identity
 * provider: each partner gets a client id and secret, exchanges them here for
 * a short-lived token carrying logistics_agent_uri, and presents it on every
 * call. Partners only ever see a token URL, so a real identity provider can
 * take over later without them changing anything else. Rate limiting is the
 * host's responsibility (a middleware in front of this handler).
 */
final class TokenEndpoint implements RequestHandlerInterface
{
    /**
     * @param ?string $audience put in the aud claim so tokens are bound to one server
     */
    public function __construct(
        private readonly ClientCredentialsVerifier $credentials,
        private readonly Rs256Signer $signer,
        private readonly ResponseFactoryInterface $responses,
        private readonly StreamFactoryInterface $streams,
        private readonly int $ttlSeconds = 3600,
        private readonly ?string $audience = null,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (strtoupper($request->getMethod()) !== 'POST') {
            return $this->error(405, 'invalid_request', 'Use POST.')->withHeader('Allow', 'POST');
        }

        $params = $this->formParameters($request);
        if (($params['grant_type'] ?? null) !== 'client_credentials') {
            return $this->error(400, 'unsupported_grant_type', 'Only the client_credentials grant is supported.');
        }

        // client_secret_basic takes precedence over client_secret_post, as RFC 6749 section 2.3.1 prefers it.
        [$clientId, $secret, $viaBasic] = $this->basicCredentials($request) ?? [$params['client_id'] ?? null, $params['client_secret'] ?? null, false];
        if (!\is_string($clientId) || $clientId === '' || !\is_string($secret) || $secret === '') {
            return $this->error(400, 'invalid_request', 'client_id and client_secret are required.');
        }

        $agent = $this->credentials->verify($clientId, $secret);
        if ($agent === null) {
            $this->logger->warning('Token refused', ['client_id' => $clientId]);
            $response = $this->error(401, 'invalid_client', 'Client authentication failed.');

            return $viaBasic ? $response->withHeader('WWW-Authenticate', 'Basic realm="one-record"') : $response;
        }

        $claims = ['sub' => $clientId, Claims::LOGISTICS_AGENT_URI => $agent->value];
        if ($this->audience !== null) {
            $claims['aud'] = $this->audience;
        }
        $token = $this->signer->sign($claims, $this->ttlSeconds);
        $this->logger->info('Token issued', ['client_id' => $clientId, 'agent' => $agent->value]);

        return $this->json(200, ['access_token' => $token, 'token_type' => 'Bearer', 'expires_in' => $this->ttlSeconds]);
    }

    /**
     * @return array<string, string>
     */
    private function formParameters(ServerRequestInterface $request): array
    {
        $parsed = $request->getParsedBody();
        if (!\is_array($parsed) || $parsed === []) {
            $parsed = [];
            parse_str((string) $request->getBody(), $parsed);
        }
        $params = [];
        foreach ($parsed as $key => $value) {
            if (\is_string($value)) {
                $params[(string) $key] = $value;
            }
        }

        return $params;
    }

    /**
     * @return ?array{string, string, true}
     */
    private function basicCredentials(ServerRequestInterface $request): ?array
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/^Basic\s+([A-Za-z0-9+\/=]+)\s*$/i', $header, $m) !== 1) {
            return null;
        }
        $decoded = base64_decode($m[1], true);
        if ($decoded === false || !str_contains($decoded, ':')) {
            return null;
        }
        [$id, $secret] = explode(':', $decoded, 2);

        return [rawurldecode($id), rawurldecode($secret), true];
    }

    private function error(int $status, string $code, string $description): ResponseInterface
    {
        return $this->json($status, ['error' => $code, 'error_description' => $description]);
    }

    /**
     * @param array<string, mixed> $body
     */
    private function json(int $status, array $body): ResponseInterface
    {
        return $this->responses->createResponse($status)
            ->withHeader('Content-Type', 'application/json; charset=utf-8')
            ->withHeader('Cache-Control', 'no-store')
            ->withHeader('Pragma', 'no-cache')
            ->withBody($this->streams->createStream(json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES)));
    }
}
