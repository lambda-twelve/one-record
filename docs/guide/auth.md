# Authentication

ONE Record servers authenticate each other with bearer JSON Web Tokens from
an identity provider the receiving server trusts. The token carries `iss`,
`exp` and `logistics_agent_uri`, the URI of the `cargo:LogisticsAgent` the
caller acts as. The `Auth` namespace provides the pieces a host needs on
both sides, framework-free.

## Verifying tokens

`Auth\Jwt\Rs256Verifier` accepts RS256 and nothing else. The algorithm is
fixed, keys come only from a `KeyResolver` (never from `jku`, `x5u` or an
embedded `jwk`), `iss` and `exp` are mandatory, `nbf` is honoured with a
30-second leeway, `crit` headers are refused, and the audience is checked when
the server expects one. Refusals are `JwtException` with a stable `reason`
code for logs; no token content is echoed.

```php
$verifier = new Rs256Verifier(
    new StaticKeyResolver(['https://auth.partner.example' => $pem]),   // issuer => PEM public key(s)
    $clock,                       // PSR-20
    expectedAudience: 'one-record',
);
$claims = $verifier->verify($token);
$claims->logisticsAgentUri();
```

Two resolvers ship:

- `StaticKeyResolver`: issuers configured in code with one PEM, a list, or a
  map of key id to PEM. What a host with a few partners needs.
- `JwksKeyResolver`: fetches each trusted issuer's JWKS document (an explicit
  URL or `{issuer}/.well-known/jwks.json`) through any PSR-18 client, caches
  it in any PSR-16 cache, and refreshes once when a token names an unknown
  key id, so key rotation needs no restart. Untrusted issuers are never
  fetched. Of the keys a document lists, only those published for signature
  verification are used: `kty` RSA, `use` absent or `sig`, `alg` absent or
  `RS256`, `key_ops` absent or including `verify`. Everything else, an
  encryption key or a malformed entry, is skipped and logged.

`Auth\JwtAuthenticator` plugs the verifier into the server's `Authenticator`
SPI: a valid token with a valid `logistics_agent_uri` becomes an `Agent`;
anything else is anonymous and the server answers 401.

### Several sources of keys

`ChainKeyResolver` combines resolvers: pinned PEM keys for some issuers
(`StaticKeyResolver`), JWKS documents for others (`JwksKeyResolver`), the
host's own key for its own token endpoint. The first resolver that knows the
issuer answers. `JwksKeyResolver` works without a cache (every token fetches)
and treats a failing cache as a miss, so a cache outage never turns into 401s.

## Issuing tokens

`Auth\Jwt\Rs256Signer` signs with a PEM RSA private key (2048 bits or more)
and sets `iss`, `iat`, `nbf`, `exp` and a random `jti` that callers cannot
override. It exposes the public key as PEM and as a JWK, so a host can
publish its own JWKS document.

`Auth\TokenEndpoint` is a PSR-15 handler implementing the OAuth 2.0
client-credentials grant: partners exchange a client id and secret (in the
body or as HTTP Basic) for a short-lived token with `sub`,
`logistics_agent_uri` and, when configured, `aud`. Errors follow RFC 6749
(`unsupported_grant_type`, `invalid_request`, `invalid_client`). Credentials
are checked through `ClientCredentialsVerifier`; the host stores secrets
hashed and compares in constant time (`InMemoryClientCredentials` shows the
pattern with `password_hash`). Rate limiting belongs in a middleware in front
of the handler.

For a handful of airline partners this is enough and keeps one less service
to run and secure; partners only ever see a token URL, so a real identity
provider can replace it later without them changing anything else.

### Publishing your keys

`JwksEndpoint` is a PSR-15 handler serving the JWKS document for one or more
`Rs256Signer`s (the current key and the one being retired). Mount it at
`/.well-known/jwks.json`; partners point a `JwksKeyResolver` at it and key
rotation needs no out-of-band exchange.

## Where the boundary is

| SDK | Host |
| --- | --- |
| Verifier, signer, key resolvers, authenticator, token endpoint | Which issuers to trust and their keys or JWKS URLs |
| Constant-time credential check pattern | Partner records, hashed secrets, their storage |
| | Rate limiting, audit logging beyond the PSR-3 messages the SDK emits |
