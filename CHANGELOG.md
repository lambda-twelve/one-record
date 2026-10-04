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

- `Spi\Volatile`, a marker for stores with nothing to roll back; the
  in-memory stores carry it and the identity-unit-of-work warning is decided
  by it rather than by class name. `ServerBuilder::check(Services)` returns
  the same findings for a host's status page.
- `Testing\RacingActionRequestStore`: stages a decision lost to another
  worker, outright or through a host callback that flips its own row first.
- `Testing\FixedClock::set()` for jumping to an absolute instant.
- `Client\DeliveryVerdict`: the retry classification every outbox worker
  needs (`Retry` for transport failures, 5xx, 408 and 429; `Reject`
  otherwise), and a guide section specifying the outbox row, claim and
  outcome model two hosts converged on.

### Changed

- `ActionRequestStatusChanged` and the status notification fire after the
  decision's side effects (grants, revision, revocation) are in place, once
  per decision, with the status the request had before it; a change that
  fails to apply reports `Failed` from `Pending` even though the store
  recorded `Accepted` in between (Drupal integration review, round 2).

## [1.0.0-beta3] - 2026-10-04

### Changed

- Every decision on an action request stores its new status (the
  compare-and-set) before any side effect: grants, the new revision,
  notifications. A decision that loses the race against another worker now
  writes nothing even under a host without a transactional unit of work; it
  used to commit the grants first (Laravel integration review, round 2).
- `Services` logs a warning when no `UnitOfWork` is given and a store is not
  one of the SDK's in-memory ones, since operations are then not atomic.
- The contract traits' fixture constants are prefixed (`CONTRACT_OBJECT`,
  `CONTRACT_PARTNER`, ...) so a host test case with constants of its own can
  use the traits.
- `NotificationOutbox` and the SPI guide state when `enqueue()` runs relative
  to the unit of work, that delivery is at least once, and that PSR-14
  listeners run inside the unit of work before the fan-out.

## [1.0.0-beta2] - 2026-10-04

### Added

- `Testing\Contract\*ContractTests`: every store contract is also a trait,
  for hosts whose test cases must extend a framework base class (Testbench,
  `KernelTestBase`) and so cannot extend the abstract contract; the abstract
  class is now the trait on a bare `TestCase`, so the two cannot drift.

### Changed

- `LogisticsEventStore` states that every event it receives carries
  `cargo:eventDate` (the server and the checked builder both refuse one
  without), so stores need no semantics for date-less events.

- A status-change notification for an action request over several logistics
  objects (an access delegation, typically) no longer names an arbitrary first
  object in `api:hasLogisticsObject`, whose cardinality is at most one; it
  names none and the request in `api:isTriggeredBy` lists them all (spec
  question 32, IATA-Cargo/ONE-Record#437). Type subscriptions keep matching
  declared `@type` values only, now recorded as spec question 31
  (IATA-Cargo/ONE-Record#412).

## [1.0.0-beta1] - 2026-10-04

First release: the server and the client for API 2.2.0 and 2.3.0 with data
model 3.2 and 3.3, the compliance collection green for both editions, the
NE:ONE interoperability suite green, and five independent adversarial review
rounds folded in. Public API changes remain possible between betas and are
recorded here.

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
- Review round with the Laravel and Drupal wrapper teams:
  `Server\Spi\UnitOfWork` (the host's transaction boundary around every
  mutating request and holder operation); `Server\Spi\IdGenerator` as an
  interface with `UuidIdGenerator`; an `id` on `OutboundNotification`;
  `SubscriptionStore::offer()`/`withdraw()`, `ActionRequestStore::accepted()`,
  `LogisticsEventStore::eraseFor()`, `AccessDelegationStore::eraseFor()` and a
  scoped `DataHolder::forget()`; `Server\GrantAccessPolicy` (public grants
  stored as grants to `EVERYONE`; `InMemoryAccessPolicy` deprecated);
  millisecond `SystemClock`, `ActionRequest::toStorageJsonLd()`,
  `LogisticsEvent::fromStored()` and `ObjectBuilder::ofEvent()`;
  `Auth\Jwt\ChainKeyResolver`, an optional, fault-tolerant cache on
  `JwksKeyResolver`, `Auth\JwksEndpoint`; `ServerBuilder::routes()`; objects
  created over HTTP fan out to subscribers; and the `Testing` namespace with
  the doubles and `Testing\Contract` store contract tests.
- Adversarial review round (2026-10-03), one regression test per finding:
  client credentials bound to the partner's origin (`additionalOrigins` for
  multi-host partners); `ActionRequestStore::transition()` compare-and-set
  and lifecycle decisions made on stored state; shared visited set in graph
  collectors; strict `xsd:dateTime` parsing (a malformed expiry is a 400, not
  "no expiry"); the client refuses to request an expiring delegation from a
  2.2 partner; fan-out consults the access policy before disclosing a body;
  `DataHolder::change()`/`update()`/`publish()` throw `ChangeFailed`;
  language-tagged literals refused in changes; exact `xsd:decimal`
  comparison; sound canonical blank-node labels; order-independent change
  validation; discovery falls back through API versions on 406; publishers
  may query offers for their own objects; bulk events validated like single
  ones; client checks the answer is about the requested object; typed links
  rewritten in historical reads; RFC 6749 form-encoding of Basic
  credentials; `q=0` exclusions and configured body versions honoured;
  `isTriggeredBy` never names an organisation; global event IRI uniqueness;
  malformed `?at=` and non-finite numbers answer 400; `Idempotency-Key` on
  sent and received notifications; JWKS refresh cooldown; only the requestor
  or the policy may revoke; reference stores keep snapshots; position-aware
  JSON-LD compaction, RFC 3986 `@base` resolution, term-scoped coercion and
  labelled blank roots.
- Second adversarial round (2026-10-03), again one regression test per
  finding: canonical blank-node labels by exhaustive individualisation search
  (`ComparisonBudgetExceeded` when a graph is too symmetric, instead of a
  guess); the writer uses a term alias only when its coercion fits the values
  and never writes a node id as a bare prefix; historical reads with
  `embedded=true` accepted by the client; a subscriber may revoke a
  SubscriptionRequest a third party created (spec question 30); the in-memory
  event store and outbox keep snapshots, with contract tests; changes may not
  write API-namespace properties and final validation tolerates only the two
  revision properties; content negotiation evaluates every served version
  against the most specific matching range; `xsd:dateTime` ranges checked,
  `24:00:00` accepted as the next midnight; dot-segment removal for
  network-path references; the JWKS refresh cooldown kept in the shared
  cache; a shared embedded node introduced once in a change; a store's status
  conflict answers 409.
- Third adversarial round (2026-10-03): the change builder plans deletions
  over the whole before/after graphs, so a shared embedded node is edited
  once, keeps its triples while any link reaches it, and loses them once when
  none does; a node under several root properties reports every one of them
  as changed; every compact key the writer emits must read back as its IRI,
  so shadowed `@vocab` names and wrong coercions are skipped instead of
  dropping a predicate or turning a string into an IRI; the client names both
  identities it accepts before a flattened answer's root is chosen; in-memory
  outbox reads hand out copies and the outbox contract states ownership.
- Fourth adversarial round (2026-10-03): the change builder now builds a
  correspondence between old and new embedded nodes over the whole graphs
  before emitting any operation (unchanged content first in every slot, then
  one-to-one in-place pairs), so an unchanged branch is never edited to serve
  another, shared nodes split and merge exactly as the target does, and
  topology-only changes are changes; term definitions in a written `@context`
  use prefixes only and the reader resolves definitions through other terms,
  so a datatype alias round-trips instead of failing or changing the
  datatype; the JWKS rotation test checks the rotated key itself.
- Fifth adversarial round (2026-10-04): a term spelled as a compact IRI of a
  defined prefix, or as an absolute IRI, cannot be redefined to mean another
  IRI (JSON-LD's invalid IRI mapping); a definition that only repeats the
  prefix mapping is left out of a written `@context` instead of appearing as
  an empty array.
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
