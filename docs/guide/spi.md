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
the spec's filters (event codes, created/occurred ranges, sort, limit, skip)
and `lastModified(Iri)` for the list's `Last-Modified`.

### `ActionRequestStore`

`save`, `get`, `auditTrail(Iri, AuditTrailQuery)` (the change requests of an
object, filtered by time range and status) and `pendingChanges(Iri)`, which
the lifecycle uses to reject stale changes when one is accepted.

### `SubscriptionStore`

Two directions. `subscribersOf(Iri, types, now)` answers who must be notified
about an object: the in-memory version derives this from accepted
`SubscriptionRequest`s in the `ActionRequestStore`. `offered(topicType, topic)`
answers `GET /subscriptions`: the subscriptions this host wants when a
publisher asks.

### `AccessDelegationStore`

`grant(Grant)`, `grantsFor(agent, object)` and `revokeFrom(requestIri)`. The
access policy consults it; accepted `AccessDelegationRequest`s write to it.

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
routed to it"). `GrantAccessPolicy` shows the expected shape: internal
agents, explicit grants, public grants, and grants from the delegation store,
deny by default.

## Notifications

### `NotificationOutbox`

`enqueue(OutboundNotification)`. The server never sends HTTP itself: it queues
`{recipient, notification, createdAt}` and the host delivers, through a queue
worker and its own egress rules, typically with the SDK's client.
`OutboundNotification::suggestedEndpoint()` derives the recipient's
`/notifications` URL from its agent URI; a host may know better.

## Identifiers

`Model\IriMinter` (`mint(localKey, types): Iri`) decides the URIs of objects
the host publishes; `UuidIriMinter` is the default. `Model\EmbeddedIdMinter`
decides the ids of embedded nodes; the default is the spec's
`internal:<uuid5>`. `Server\IdGenerator` provides the UUIDs of action requests
and events; its byte source is injectable for deterministic tests.

## Clock, events, logging

The server takes a PSR-20 clock (`Server\SystemClock` if you have none), a
PSR-14 dispatcher and an optional PSR-3 logger. It raises
`LogisticsObjectCreated`, `LogisticsObjectRevised`, `LogisticsEventReceived`,
`ActionRequestCreated`, `ActionRequestStatusChanged` and
`NotificationReceived`; a wrapper maps them to its own event system.

## Wiring

`Server\Services` is the bag of all of the above. `ServerBuilder::build(Services)`
returns the PSR-15 handler; `InMemoryServer` is a worked example of the wiring.
