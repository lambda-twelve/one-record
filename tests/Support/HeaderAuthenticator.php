<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Support;

use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Server\Spi\Agent;
use LambdaTwelve\OneRecord\Server\Spi\Authenticator;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Trusts an X-Test-Agent header. Tests of endpoint behaviour do not need real
 * tokens; JwtAuthenticator has its own tests.
 */
final class HeaderAuthenticator implements Authenticator
{
    public function authenticate(ServerRequestInterface $request): ?Agent
    {
        $iri = $request->getHeaderLine('X-Test-Agent');

        return $iri === '' ? null : new Agent(new Iri($iri), 'https://test.issuer', ['sub' => $iri]);
    }
}
