# The SPI a host implements

Everything the server needs from its host is an interface in
`LambdaTwelve\OneRecord\Server\Spi`. Each has an in-memory implementation in
`Server\InMemory` that the test suite runs against; a wrapper replaces them one
by one with persistent ones (Eloquent, Doctrine, Drupal's database API) and
keeps the same tests green. The interfaces are deliberately small.

## Storage

### `LogisticsObjectStore`

Revisions of logistics objects. Every revision is kept: the API serves
historical reads and the audit trail.

| Method | Contract |
| --- | --- |
| `latest(Iri): ?StoredObject` | The current revision, or null |
| `revision(Iri, int): ?StoredObject` | A specific revision |
| `at(Iri, DateTimeImmutable): ?StoredObject` | The revision current at that instant |
| `exists(Iri): bool` | |
| `create(LogisticsObject, DateTimeImmutable): StoredObject` | Revision 1; throws `StoreException` if the URI exists |
| `saveRevision(LogisticsObject, int $expectedCurrent, DateTimeImmutable): StoredObject` | Stores `expectedCurrent + 1` only if the current revision is `expectedCurrent` (optimistic concurrency); throws `StoreException` otherwise |
| `erase(Iri): void` | Removes every revision (see [Forgetting data](forgetting-data.md)) |

`StoredObject` carries the object, its revision, the latest revision, and the
timestamps. The object graph includes the embedded nodes with their
`internal:` ids; store it as the JSON-LD the SDK writes (`toJson()`), or as
triples, whichever suits the database.

### `LogisticsEventStore`

Append-only events per object: `append`, `get`, `query(Iri, EventQuery)` with
the spec's filters (event codes, created/occurred ranges, sort, limit, skip),
`lastModified(Iri)` for the list's `Last-Modified`, and `eraseFor(Iri)` for
[forgetting](forgetting-data.md). Two rules the contract test pins: appending
an event IRI that already exists is a `StoreException` (event IRIs are minted
by the server, so a repeat is a bug, never an update), and `lastModified` is
the newest event's `created`, not the last one appended. Rebuild events from
storage with `LogisticsEvent::fromStored()`, which validates nothing.

### `ActionRequestStore`

`save`, `get`, `auditTrail(Iri, AuditTrailQuery)` (the change and verification
requests of an object, filtered by time range and status), `pendingChanges(Iri)`,
which the lifecycle uses to reject stale changes when one is accepted, and
`accepted(ActionRequestType)`, the active subscriptions and delegations.
Requests are saved whole and replaced whole; store them as
`ActionRequest::toStorageJsonLd()` (every property this package knows,
whatever version partners negotiate) and read them back with `fromJsonLd()`.

### `SubscriptionStore`

Two directions. `subscribersOf(Iri, types, now)` answers who must be notified
about an object; the in-memory version derives it from
`ActionRequestStore::accepted(Subscription)`, so it works over any request
store. `offer(Subscription)`, `withdraw(Subscription)` and
`offered(topicType, topic)` are the host's own interests, answered to
`GET /subscriptions` when a publisher asks.

### `AccessDelegationStore`

`grant(Grant)`, `grantsFor(agent, object)`, `revokeFrom(requestIri)` and
`eraseFor(object)`. The access policy consults it; accepted
`AccessDelegationRequest`s write to it, and so do the host's own grants and
the public ones (`GrantAccessPolicy::EVERYONE` as the agent).

## Authentication and authorisation

### `Authenticator`

`authenticate(ServerRequestInterface): ?Agent`. Null means 401. The SDK ships
`Auth\JwtAuthenticator` for RS256 bearer tokens; a host with its own token
scheme implements the interface directly.

### `AccessPolicy`

`decide(Agent, Action, ?Iri): Decision`, where `Action` is one of the spec's
four permissions (`GET_LOGISTICS_OBJECT`, `PATCH_LOGISTICS_OBJECT`,
`POST_LOGISTICS_EVENT`, `GET_LOGISTICS_EVENT`) or one of the server's
internal actions (read audit trail, create object, decide an action request,
read or revoke someone else's action request). `Decision::Allow`,
`Decision::Forbid` (403) or `Decision::Hide` (404, so existence is not
confirmed).

This is where a host encodes its own rules ("a forwarder sees the shipments
routed to it"). `Server\GrantAccessPolicy` is the policy most hosts run:
internal agents (constructor argument), every grant read from the
`AccessDelegationStore` including public ones, deny by default. Wrap or
replace it for rules the grant model cannot express.

## Notifications

### `NotificationOutbox`

`enqueue(OutboundNotification)`. The server never sends HTTP itself: it queues
`{recipient, notification, createdAt}` and the host delivers, through a queue
worker and its own egress rules, typically with the SDK's client.
`OutboundNotification::suggestedEndpoint()` derives the recipient's
`/notifications` URL from its agent URI; a host may know better.

## Transactions

### `UnitOfWork`

`run(callable): mixed`. The server runs every mutating request through it,
and `DataHolder` and `ActionRequests` run each of their operations through
it, so everything an operation writes (a revision, the request, rejected
siblings, queued notifications) stands or falls together. Bind your database
transaction here; nested calls join the outer unit (use a depth counter or
savepoints). The default runs the work directly.

## Identifiers

`Model\IriMinter` (`mint(localKey, types): Iri`) decides the URIs of objects
the host publishes; `UuidIriMinter` is the default. `Model\EmbeddedIdMinter`
decides the ids of embedded nodes; the default is the spec's
`internal:<uuid5>`. `Spi\IdGenerator` (`next(): string`) provides the ids of
action requests, events and outbound notifications; `UuidIdGenerator` is the
default and takes an injectable byte source for deterministic tests.

## Clock, events, logging

The server takes a PSR-20 clock (`Server\SystemClock` if you have none), a
PSR-14 dispatcher and an optional PSR-3 logger. Timestamps are written with
millisecond precision; `SystemClock` ticks in milliseconds so a stored
document carries exactly the instant the server computed. A clock of your
own should truncate the same way. It raises
`LogisticsObjectCreated`, `LogisticsObjectRevised`, `LogisticsEventReceived`,
`ActionRequestCreated`, `ActionRequestStatusChanged` and
`NotificationReceived`; a wrapper maps them to its own event system.

## Wiring

`Server\Services` is the bag of all of the above. `ServerBuilder::build(Services)`
returns the PSR-15 handler; `InMemoryServer` is a worked example of the wiring.

## Testing your implementation

`Testing\Contract` ships one abstract PHPUnit test class per store interface
(`LogisticsObjectStoreContract`, `LogisticsEventStoreContract`,
`ActionRequestStoreContract`, `SubscriptionStoreContract`,
`AccessDelegationStoreContract`, `NotificationOutboxContract`). Extend one,
return your store from its factory method, and the tests that prove the
in-memory stores prove yours. `Testing` also holds the doubles the SDK's own
tests use: `FixedClock`, `HeaderAuthenticator`, `RecordingDispatcher`,
`RecordingUnitOfWork`, `FakeHttpClient`, `InProcessHttpClient` (the SDK
client against a PSR-15 handler in one process) and `ArrayCache`. PHPUnit is
a suggested dependency; nothing else in the package needs it.
