<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Client;

/**
 * Supplies the bearer token the client sends to one ONE Record server. The
 * host decides where tokens come from: a client-credentials flow
 * (ClientCredentialsTokenProvider), a fixed token (StaticTokenProvider) or
 * its own scheme.
 */
interface TokenProvider
{
    /**
     * A token valid for the given server endpoint, fetched or refreshed as needed.
     *
     * @throws ClientException when no token can be obtained
     */
    public function token(string $serverEndpoint): string;
}
