# NE:ONE interoperability

[NE:ONE](https://git.openlogisticsfoundation.org/wg-digitalaircargo/ne-one)
(Open Logistics Foundation, OLFL-1.3) is the reference ONE Record server and,
at the pinned commit `9d485f63…`, implements API 2.2.0 with data model 3.2.
The interoperability suite (`tests/Interop/`) uses it as a comparison oracle:
the same graph is published to a running NE:ONE through this package's client
and to this package's server, both are read back and compared as RDF graphs
with the [comparer](../guide/json-ld.md). Every difference is either a bug
here or a numbered entry in [spec questions](../spec-questions.md).

## Running it

NE:ONE is built from source at the pinned commit by
`tests/Interop/neone/Dockerfile` (a Lambda Twelve file; NE:ONE itself is not
part of this repository) and started by `tests/Interop/run.sh`, which also
generates the RS256 key pair the tests sign tokens with and mounts its public
half as the only issuer NE:ONE trusts.

```sh
ddev interop                     # DDEV: NE:ONE on the host, PHPUnit in the web container
bash tests/Interop/run.sh        # PHP on the Docker host (CI)
NEONE_IMAGE=my-neone:local ddev interop   # reuse an image instead of building
```

CI runs the suite on every push to `main` and on demand; the build from source
takes a while, so it is not part of the pull-request matrix.

## What is compared

| Flow | Result |
| --- | --- |
| Server information, version negotiation (NE:ONE lists 2.2.0, the client speaks 2.2.0) | identical |
| A shipment, a piece and a company with embedded values and a party, created with predefined URIs, read back | identical modulo the differences below |
| A change request by the data holder, accepted, revision 2, audit trail | identical |
| A logistics event posted and listed, with event-code filters | identical modulo server-set properties |
| A partner refused (403), then granted access through an access delegation request the holder accepts | identical outcome |

## Known differences

All recorded in [spec questions](../spec-questions.md) with what this package
does about them.

| | NE:ONE | This package | Handling |
| --- | --- | --- | --- |
| Response shape | Flattened: a top-level `@graph` whenever an object has embedded nodes, `@vocab` for `cargo:` | One nested node | The JSON-LD reader accepts a top-level `@graph` and roots it at the object asked for (question 24) |
| `@type` | Every superclass added (`Piece`, `PhysicalLogisticsObject`, `LogisticsObject`) | The declared classes | Compared on the most specific classes; `ChangeBuilder` never diffs types (question 21) |
| Embedded ids | `neone:<uuid>` | `internal:<uuid5>` | Both treated as blank nodes in comparison (question 3) |
| Revision literals | `xsd:int` | `xsd:positiveInteger` / native integer | Same value in comparison (question 5) |
| Timestamps | Millisecond lexical form (`…:00.000Z`) | As received | Canonicalised in comparison (question 5) |
| Received events | `creationDate`, `eventFor` and `recordingOrganization` set by the server | Body kept as posted, `eventFor` from the path | Excluded from the event comparison (question 22) |
| `PATCH /action-requests/{id}?status=` | 204, applied on the next evaluation tick | 204, applied at once | Tests poll (question 23) |
| `Content-Type` | `application/ld+json;charset=UTF-8`, no version | The negotiated version echoed | The client reads the version it asked for when none is echoed (question 10) |
