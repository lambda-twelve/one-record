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

## Layers inside the SDK

The inside is layered too, and a second PHPStan rule (`tools/PhpStan/LayerRule.php`)
enforces it from one table. Each layer is a namespace under
`LambdaTwelve\OneRecord`; the longest matching prefix wins, so `Server\Spi`
is its own layer while `Server\Endpoint` belongs to `Server`. A layer may
depend on itself and on what its row lists; everything else fails the build.

| Layer | May depend on | Why |
| --- | --- | --- |
| `Spec` | nothing | Versions and namespaces: constants everything reads |
| `Rdf` | `Spec` | Terms, triples, graphs |
| `Vocabulary` | `Spec`, `Rdf` | The generated ontology terms and questions about them |
| `JsonLd` | `Spec`, `Rdf`, `Vocabulary` | The restricted JSON-LD processor and node helpers |
| `Model` | the four above | Logistics objects, events, builders, local graphs |
| `Change` | the above, `Model`, `Api` | `api:Change` is an API document with diff/apply logic |
| `Api` | the above, `Model`, `Change` | Every other API document; an ActionRequest carries a Change |
| `Auth` | `Spec`, `Rdf`, `Server\Spi` | Implements the `Authenticator` SPI and nothing else of the server |
| `Server\Spi` | the document layers | What hosts implement: documents in, documents out, no server internals |
| `Server\Event` | the document layers, `Server\Spi` | PSR-14 events |
| `Server\InMemory` | `Server\Spi`, `Server\Event`, and the wiring classes `ServerConfig`, `Services`, `ServerBuilder`, `OneRecordServer`, `IdGenerator`, `SystemClock` | Reference stores and a complete server; never endpoint code |
| `Server` | everything above it | Routing, HTTP, endpoints, lifecycle, fan-out |
| `Client` | the document layers, `Auth` | Talks to other servers; must never reach into ours |

`Change` and `Api` are a declared pair: a Change is itself an API document
(`api:Change`) and lives in its own namespace only because of the size of its
builder and applier. Nothing else may be circular.

What this guarantees a wrapper: implementing the SPI needs only the document
layers; replacing an in-memory store never pulls in endpoint code; and a
future client package can be split off without touching the server.

Business concepts (shipments, tenants, ERPs, carriers) never appear in the SDK.
The SDK knows logistics objects, logistics events, action requests,
subscriptions and notifications: the vocabulary of the specification and
nothing else.
