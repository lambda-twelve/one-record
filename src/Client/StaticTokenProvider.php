<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

/**
 * A fixed token: for tests, for tokens the host obtains itself, and for
 * partners that hand out long-lived credentials.
 */
final class StaticTokenProvider implements TokenProvider
{
    public function __construct(private readonly string $token) {}

    public function token(string $serverEndpoint): string
    {
        return $this->token;
    }
}
