<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Auth\Jwt\JwtException;
use LambdaTwelve\OneRecord\Auth\Jwt\Rs256Verifier;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * The spec's authentication: a bearer JWT from a trusted issuer whose
 * logistics_agent_uri claim names the calling organisation. Tokens without
 * that claim identify nobody on the ONE Record network and are refused, as
 * NE:ONE does.
 */
final class JwtAuthenticator implements Authenticator
{
    public function __construct(
        private readonly Rs256Verifier $verifier,
        private readonly LoggerInterface $logger = new NullLogger(),
    ) {}

    public function authenticate(ServerRequestInterface $request): ?Agent
    {
        $token = self::bearerToken($request);
        if ($token === null) {
            return null;
        }
        try {
            $claims = $this->verifier->verify($token);
        } catch (JwtException $e) {
            $this->logger->info('Bearer token refused', ['reason' => $e->reason]);

            return null;
        }
        $agentUri = $claims->logisticsAgentUri();
        if ($agentUri === null) {
            $this->logger->info('Bearer token refused', ['reason' => 'missing_logistics_agent_uri', 'issuer' => $claims->issuer()]);

            return null;
        }
        try {
            $iri = new Iri($agentUri);
        } catch (InvalidArgumentException) {
            $this->logger->info('Bearer token refused', ['reason' => 'invalid_logistics_agent_uri', 'issuer' => $claims->issuer()]);

            return null;
        }

        return new Agent($iri, $claims->issuer(), $claims->all);
    }

    public static function bearerToken(ServerRequestInterface $request): ?string
    {
        $header = $request->getHeaderLine('Authorization');
        if (preg_match('/^Bearer\s+([A-Za-z0-9._~+\/=-]+)\s*$/i', $header, $m) !== 1) {
            return null;
        }

        return $m[1];
    }
}
