# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow
[Semantic Versioning](https://semver.org/).

The first release is 1.0.0-beta1, cut when the roadmap is complete: both API
editions served and consumed, the compliance collection and the NE:ONE
interoperability suite green. Pre-release versions (`-betaN`, `-rcN`) may still
change the public API; every such change is listed under the release that makes
it. From 1.0.0 on, breaking changes need a new major version.

## [Unreleased]

### Added

- Repository skeleton: Composer package, PHPUnit 11, PHPStan (level max) with an
  SDK-boundary rule, php-cs-fixer (PER-CS 2.0), DDEV configuration, GitHub
  Actions, MkDocs documentation site.
- `Spec\Edition`, `Spec\ApiVersion`, `Spec\DataModelVersion` and
  `Spec\Namespaces`: the supported editions (2025-07: API 2.2.0 / data model
  3.2; 2026-07: API 2.3.0 / data model 3.3) and the IRIs ONE Record is built
  from.
- `Rdf\Graph` and its terms (`Iri`, `BlankNode`, `Literal`, `Triple`): the RDF
  layer everything else works on.
- Generated vocabulary (`Vocabulary\Generated`): every class, property and
  named individual of the cargo and API ontologies and every code list, merged
  across both editions with per-term `since` / `deprecatedIn` / `removedIn`
  metadata, plus `Vocabulary\Vocabulary` for runtime questions (accepted
  properties per class, logistics-object classes, most specific type, code-list
  membership) and version-limited views.
- `bin/generate-vocabulary`: regenerates the vocabulary from IATA's ontologies
  at pinned commits; CI fails on a diff.
- `JsonLd`: the restricted JSON-LD processor ONE Record needs. `Expander`
  turns a compacted document into triples (inline object contexts with
  prefixes, `@vocab`, `@base`, `@language` and `@type`-coercing term
  definitions; `@id`, `@type`, value objects, arrays, embedded objects and
  references) and rejects everything outside that subset with a message
  naming the construct and its path. `Writer` compacts a graph back to
  deterministic JSON-LD. `Comparer` decides graph isomorphism modulo blank-node
  labels, with embedded-object IRIs (`internal:`, `neone:`) treated as blank
  nodes and numeric literals normalised; `Diff` reports the differing triples.
- `Model`: `LogisticsObject`, `LocalGraph` (objects linked by local key, resolved
  to URIs with an `IriMinter`; `UuidIriMinter` mints deterministic UUIDs under a
  base URL), `ObjectBuilder`/`Embedded`/`Values` builders validated against the
  ontology (with an optional data model version ceiling), and
  `EmbeddedIdMinter` for the spec's stable `internal:<uuid5>` embedded ids.
- `Change`: the `api:Change` model with JSON-LD read/write, `ChangeBuilder`
  (diff two versions of an object into ADD/DELETE operations, editing embedded
  objects in place or replacing them via blank nodes) and `ChangeApplier`
  (apply a change atomically with the spec's rules: revision check, no event
  edits, deletes before adds, ontology validation, orphan cleanup).
- `Api\Error`, `Api\ErrorDetail`, `Api\Severity` value objects.
- `Auth`: `Rs256Verifier` (RS256 only, keys from a resolver, issuer/expiry/
  not-before/audience checks), `Rs256Signer`, `StaticKeyResolver`,
  `JwksKeyResolver` (PSR-18 + PSR-16, refresh on unknown key id), `Jwk` (RSA
  JWK to PEM), `JwtAuthenticator` for the server's `Authenticator` SPI, and
  `TokenEndpoint`, a PSR-15 OAuth 2.0 client-credentials endpoint with a
  `ClientCredentialsVerifier` SPI and an in-memory reference implementation.
- `Server`: the PSR-15 ONE Record server. `OneRecordServer` negotiates the
  API version, routes, authenticates and answers every failure as an
  `api:Error`; `ServerConfig` holds base URL, base path, data holder,
  versions, languages and limits. Endpoints for server information, logistics
  objects (read with `?at=` and `?embedded=`, create, change, verify), audit
  trail, logistics events (single and 2.3 bulk), notifications,
  subscriptions, access delegations and action requests. `ActionRequests`
  implements the lifecycle (accept applies changes, grants delegations,
  rejects stale pending changes); `DataHolder` is the host's PHP API
  (`create`, `update`, `publish`, `announce`, `accept`/`reject`/`acknowledge`/
  `revoke`, `subscribe`, `forget`); `Notification\Fanout` queues notifications
  in the outbox. PSR-14 events for created/revised objects, received events
  and notifications, and action-request changes.
- `Server\Spi`: the interfaces a host implements (`LogisticsObjectStore`,
  `LogisticsEventStore`, `ActionRequestStore`, `SubscriptionStore`,
  `AccessDelegationStore`, `NotificationOutbox`, `Authenticator`,
  `AccessPolicy`) with in-memory implementations in `Server\InMemory` and
  `InMemoryServer` wiring them all; `SystemClock`.
- `Api`: value objects for every API document (`Subscription`,
  `AccessDelegation`, `Verification`, `Notification`, `ServerInformation`,
  `ActionRequest`, `Collection`, `ErrorDocument`) and `Spec\ApiFeatures`,
  the table of properties gated by API version.
- Compliance collection (`tests/Compliance/`): a newman collection generated
  from the specification's examples with assertions from its MUST tables, run
  in CI against `bin/serve` for API 2.2.0 and 2.3.0 (`ddev compliance` locally).
- `bin/serve`: the in-memory server under PHP's built-in web server with an
  OAuth 2.0 token endpoint, for development and the compliance collection.
- `Server\Spi\Authenticator` and `Server\Spi\Agent`.
- Interoperability suite (`tests/Interop/`): NE:ONE built from source at a
  pinned commit as a comparison oracle; the same graph published to both
  servers and compared as RDF, plus changes, events and access delegations.
  Findings: the JSON-LD reader accepts a top-level `@graph` (NE:ONE's
  flattened answers), the comparer canonicalises `xsd:dateTime` and the
  bounded integer types, and `ChangeBuilder` ignores inferred superclass
  types when diffing.
- `Client`: `OneRecordClient` over PSR-18 for every endpoint of a partner's
  server, with server-information discovery, API version negotiation
  (highest common version, 2.3-only properties optional on read), typed
  errors carrying the partner's `api:Error` (`OneRecordHttpException`), a
  bulk-event call that falls back to per-object posts, and
  `ClientCredentialsTokenProvider` / `StaticTokenProvider` behind the
  `TokenProvider` interface.
- Architecture rule: a table-driven PHPStan rule (`tools/PhpStan/LayerRule.php`)
  enforces the layering inside `src/` (documented in the SDK boundary page),
  next to the existing rule that keeps `src/` free of anything but PSR and PHP.
  `Api\Nodes` moved to `JsonLd\Nodes` and `Model\LogisticsEvent` throws
  `ModelException` so the model no longer depends on the API layer.
