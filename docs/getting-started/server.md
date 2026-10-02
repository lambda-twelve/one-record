# Running a server

The server is one PSR-15 request handler. You give it a configuration, the
services a host must provide (storage, authentication, access policy, an
outbox for notifications) and PSR-17 factories; it answers every ONE Record
endpoint under the base path you mount it on.

## The shortest path: in-memory everything

`InMemoryServer` wires the handler with an in-memory implementation of every
interface. It is what the test suite and `bin/serve` run, and the starting
point for a host: replace one store at a time with your own implementation of
the [SPI](../guide/spi.md).

```php
--8<-- "docs/examples/run-server.php"
```

Running it prints the partner's view of the piece:

```text
200 application/ld+json; version=2.3.0
Type: https://onerecord.iata.org/ns/cargo#Piece
Revision: 1
{ "@context": ..., "@id": "https://1r.example.com/logistics-objects/piece-1", ... }
```

## Mounting the handler

`$server->handler` (an `OneRecordServer`) implements
`Psr\Http\Server\RequestHandlerInterface`. Mount it wherever your framework
lets a handler own a path prefix, and tell `ServerConfig` about that prefix:

```php
$config = new ServerConfig(
    baseUrl: 'https://1r.example.com',   // scheme and host only
    dataHolder: new Iri('https://1r.example.com/one-record/logistics-objects/holder'),
    basePath: '/one-record',             // the prefix the handler is mounted on
);
```

Logistics-object URIs are then `https://1r.example.com/one-record/logistics-objects/{id}`.
Requests outside the base path are answered 404; the server never redirects.
A site served from a subdirectory puts the subdirectory in `basePath` too,
because the SDK sees the full request path.

Frameworks that want one named route per endpoint read
`ServerBuilder::routes()`: every pattern with every method the SDK answers,
so the framework passes them all through and the SDK, not the framework,
answers 405 with the spec's error body and `Allow` header.

Other `ServerConfig` options:

| Option | Default | Purpose |
| --- | --- | --- |
| `apiVersions` | both, highest first | The API versions offered in server information and accepted in `Accept` |
| `dataModelVersions` | both | The ontology versions listed in server information |
| `languages` | `['en-US']` | `Content-Language` of responses |
| `maxBodyBytes` | 1 MiB | Request bodies above this are refused with 413 |
| `embeddedDepth` | 3 | How deep `?embedded=true` follows links to other objects of this server |
| `bulkLogisticsEvents` | `false` | Serve the optional 2.3 `POST /logistics-events` |
| `dataHolderType` | `null` | The class of the data holder (`cargo:Company`, say) for server information |

## Authentication

The handler asks your `Authenticator` for the calling agent on every request
and answers 401 when it returns null. `Auth\JwtAuthenticator` verifies RS256
bearer tokens as the ONE Record security model requires; see
[Authentication](../guide/auth.md). Tests and examples use a header instead.

## Publishing your own data

Partners never create your objects; your application does, through
`Server\DataHolder`:

- `create(LogisticsObject)` stores a new object as revision 1.
- `update(LogisticsObject)` diffs against the stored revision, records the
  difference as an accepted change request (so the audit trail is complete)
  and stores the next revision. Returns null when nothing changed.
- `publish(LocalGraph, IriMinter, $existing)` does both for a whole graph of
  linked objects, idempotently. Pass the URIs of objects already published so
  they keep them; `UuidIriMinter` with a seed gives the same URIs every time.
- `announce(object, recipient)` queues a `LOGISTICS_OBJECT_AVAILABLE`
  notification so a partner learns the URI.
- `accept`, `reject`, `acknowledge`, `revoke` decide [action requests](../guide/action-requests.md).
- `forget(object)` erases every revision; see [Forgetting data](../guide/forgetting-data.md).

Embedded objects (a `cargo:Value`, a `cargo:Party`) receive stable
`internal:<uuid5>` ids the moment an object is first stored, so later
changes can address them.

## Who may do what

The `AccessPolicy` decides every action: reading an object, changing it,
posting or reading its events, reading the audit trail, creating objects over
HTTP, deciding action requests. The in-memory policy denies everything unless
told otherwise:

```php
$server->policy->addInternal($holderAgent);                  // your own systems: everything
$server->policy->allow($partner, $objectIri, [Permission::GetLogisticsObject]);
$server->policy->allowEveryone($objectIri, [Permission::GetLogisticsObject]);
```

Grants from accepted access delegations are honoured automatically. A denial
answers 403 as the spec says; construct the policy with `Decision::Hide` to
answer 404 and not confirm that an object exists.

## The example server

`bin/serve` runs this in-memory server under PHP's built-in web server with an
OAuth 2.0 token endpoint, for manual testing and the compliance collection:

```sh
bin/serve --port=8080
bin/serve --print-token=holder     # a bearer token for the data holder client
```

It is a development tool and needs the dev dependencies.
