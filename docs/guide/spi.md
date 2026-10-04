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
`{id, recipient, notification, createdAt}` and the host delivers, through a
queue worker and its own egress rules, typically with the SDK's client.
`OutboundNotification::suggestedEndpoint()` derives the recipient's
`/notifications` URL from its agent URI; a host may know better.

`enqueue()` runs inside the unit of work, next to the writes it announces.
Hand the row to your queue only once that unit has committed (a database
after-commit hook, not the enqueue call), and let a rolled-back unit leave no
job behind. Delivery is at least once whatever the host does, so keep the
notification id with the delivery row, never send one id twice as a new
notification, and expect recipients to deduplicate on it (the SDK client
sends it as `Idempotency-Key`).

## Transactions

### `UnitOfWork`

`run(callable): mixed`. **A host with persistent stores must bind its
database transaction here.** The default, `IdentityUnitOfWork`, runs the work
directly, which is right only for the in-memory stores; with anything else a
failed operation leaves behind what it had already written, and `Services`
logs a warning when it sees that combination. The server runs every mutating
request through the unit, and `DataHolder` and `ActionRequests` run each of
their operations through it, so everything an operation writes (a revision,
the request, rejected siblings, grants, queued notifications) stands or falls
together. Nested calls join the outer unit (use a depth counter or
savepoints). Independently of the unit, every decision on an action request
is a compare-and-set on its status that happens before any side effect, so a
decision that lost a race writes nothing even without a transaction.

The warning is decided by the `Spi\Volatile` marker, not by class name: the
in-memory stores implement it, and a decorator you put around one (a
recording double, a queue in front of a test outbox) should implement it too
to stay quiet. `ServerBuilder::check($services)` returns the same findings as
a list of sentences, for a status page or health check where operators look
(Drupal's `hook_requirements`, Laravel's `about`).

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

Listeners run inside the unit of work: a listener that throws fails the
operation and rolls it back with everything else. Listeners that do I/O of
their own should queue it for after the commit rather than perform it in
place. Every event reports a state whose consequences are already written:
`ActionRequestStatusChanged` fires after the grants, the revision or the
revocation it decided are in place (and after `LogisticsObjectRevised` for an
accepted change), so a listener that reads the policy or the object sees the
world after the decision. It fires once per decision, with the status the
request had before it; a change that was accepted and then failed to apply
reports `Failed` from `Pending`, although the store recorded `Accepted` in
between (a store validating transitions itself must allow `Accepted` to
`Failed`).

## Wiring

`Server\Services` is the bag of all of the above. `ServerBuilder::build(Services)`
returns the PSR-15 handler, `ServerBuilder::check(Services)` the wiring
findings worth showing an operator; `InMemoryServer` is a worked example of
the wiring.

## Testing your implementation

`Testing\Contract` ships the behaviour every store must have as PHPUnit
tests, in two forms per interface. The abstract classes
(`LogisticsObjectStoreContract`, `LogisticsEventStoreContract`,
`ActionRequestStoreContract`, `SubscriptionStoreContract`,
`AccessDelegationStoreContract`, `NotificationOutboxContract`) extend
PHPUnit's `TestCase`: extend one, return your store from its factory method,
and the tests that prove the in-memory stores prove yours. When your test
case must extend a framework base class instead (Laravel's Testbench,
Drupal's `KernelTestBase`), use the trait behind each class
(`LogisticsObjectStoreContractTests` and so on) in your own test case; it
carries the same tests and the same factory hook. The class is only the
trait on a bare `TestCase`, so the two cannot drift. Stores are expected to
start empty, and every event handed to a store carries `cargo:eventDate`: the
server refuses a posted event without one, and so does the checked builder.
`Testing` also holds the doubles the SDK's own
tests use: `FixedClock` (`advance()` and `set()`), `HeaderAuthenticator`,
`RecordingDispatcher`, `RecordingUnitOfWork`, `FakeHttpClient`,
`InProcessHttpClient` (the SDK client against a PSR-15 handler in one
process), `ArrayCache` and `RacingActionRequestStore`. The last one stages the
race every host must survive: wrap your request store, `arm()` it with a
request, and the next decision on that request either loses its
compare-and-set outright or first runs your callback (flip your own row to
the competing status there) and then loses for real; the SDK's own test of the
scenario uses it the same way. PHPUnit is
a suggested dependency; nothing else in the package needs it.
