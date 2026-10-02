# SDK boundary

This package is a software development kit, not an application. It is wrapped
by framework packages (Laravel first, Drupal next) and by applications. The
line between the two is the most important design decision in the package, so
it is written down here and enforced by a PHPStan rule: nothing under `src/`
may reference anything outside the package's own namespace, the PSR interfaces
and PHP itself.

| The SDK owns | A wrapper or host supplies |
| --- | --- |
| Vocabulary (generated from IATA's ontologies), the JSON-LD subset, the model and builders, change diffing and applying | Nothing |
| The PSR-15 `OneRecordServer`: routing, content negotiation, errors, every endpoint, the action-request lifecycle, notification fan-out rules | Mounting it under a route; converting the framework's request/response to PSR-7 if needed |
| SPI interfaces with in-memory reference implementations | Persistent implementations (Eloquent, Drupal entity or database API), migrations |
| `NotificationOutbox`: the SDK enqueues outgoing notifications | Draining the outbox (queue job, cron) and sending through the host's egress controls, using the SDK client |
| PSR-14 event objects raised on object creation, revision, received events and action-request changes | Mapping them to framework events, webhooks, business reactions |
| `Authenticator` and `AccessPolicy` interfaces; an RS256 JWT verifier with static-key and JWKS resolvers; a client-credentials token endpoint with a `ClientCredentialsVerifier` interface | Partner and credential storage, secret hashing, rate limiting, ownership rules such as "a partner sees only the objects routed to it" |
| `IriMinter` interface with a deterministic UUIDv5 default | Tenant or base-URL specifics |
| The PSR-18 client, `TokenProvider` interface, a client-credentials provider with PSR-16 caching | The HTTP client, cache binding, proxies |
| Consumption of PSR-20 clock, PSR-3 logger, PSR-14 dispatcher | Bindings |
| "Forget" as a first-class operation: closing access immediately and an SPI hook for the host's data-protection erasure | Retention policy and scheduling |

Business concepts (shipments, tenants, ERPs, carriers) never appear in the SDK.
The SDK knows logistics objects, logistics events, action requests,
subscriptions and notifications: the vocabulary of the specification and
nothing else.
