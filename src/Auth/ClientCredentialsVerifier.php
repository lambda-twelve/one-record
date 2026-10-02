<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * What the token endpoint asks the host: are these client credentials valid,
 * and which logistics agent do they belong to? The host stores secrets hashed
 * and compares in constant time; InMemoryClientCredentials shows how.
 */
interface ClientCredentialsVerifier
{
    /**
     * @return ?Iri the logistics agent URI the client acts as, or null when the credentials are not valid
     */
    public function verify(string $clientId, string $clientSecret): ?Iri;
}
