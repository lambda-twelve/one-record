<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Turns a request into the agent it comes from, or null when it carries no
 * acceptable credential (the server answers 401 either way). JwtAuthenticator
 * implements the spec's OpenID Connect bearer tokens; a host with another
 * mechanism (mutual TLS, an API gateway header it trusts) implements this.
 */
interface Authenticator
{
    public function authenticate(ServerRequestInterface $request): ?Agent;
}
