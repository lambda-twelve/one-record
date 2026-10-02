<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Client credentials held in memory with password_hash() hashes: the reference
 * implementation for tests and bin/serve, and the pattern a host's database
 * version follows (hash at rest, constant-time verification, no early return
 * that reveals whether the client id exists).
 */
final class InMemoryClientCredentials implements ClientCredentialsVerifier
{
    /** @var array<string, array{hash: string, agent: Iri}> */
    private array $clients = [];

    private readonly string $dummyHash;

    public function __construct()
    {
        // Verifying against this when the client id is unknown keeps timing even.
        $this->dummyHash = password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
    }

    public function add(string $clientId, string $clientSecret, Iri $agent): void
    {
        $this->clients[$clientId] = ['hash' => password_hash($clientSecret, PASSWORD_DEFAULT), 'agent' => $agent];
    }

    public function verify(string $clientId, string $clientSecret): ?Iri
    {
        $client = $this->clients[$clientId] ?? null;
        $valid = password_verify($clientSecret, $client['hash'] ?? $this->dummyHash);

        return $valid && $client !== null ? $client['agent'] : null;
    }
}
