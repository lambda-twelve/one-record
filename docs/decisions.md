# Decisions

Read this before reversing an architectural choice. Each entry says what was
decided and why.

## A framework-agnostic SDK, wrapped by framework packages

The server and client live in one package that depends only on PSR interfaces.
Laravel, Drupal and any other host integrate through small wrapper packages.
Why: Lambda Twelve needs the same implementation in more than one host, and a
public package that pulls in a framework would be useless to everyone else.
The boundary is spelled out in [SDK boundary](sdk-boundary.md) and enforced by
a PHPStan rule.

## Built from the specification, with NE:ONE as an oracle only

The code is written from IATA's MIT-licensed specification and ontologies.
NE:ONE is run in the interoperability suite to compare outputs as RDF graphs,
and read to understand behaviour where the specification is unclear, but no
NE:ONE code is copied: its licence (OLFL-1.3) is not compatible with releasing
this package under Apache-2.0.

## Two API editions and two data model versions, together

API 2.2.0 and 2.3.0, data model 3.2 and 3.3 are supported at the same time,
negotiated per request on the server and per partner in the client. Why: both
editions are endorsed, partners run either, and 2.3/3.3 are supersets, so the
cost is a version table and per-term metadata rather than two code paths. See
[API and data model versions](compatibility/versions.md).

## Our own compliance collection

IATA's Postman collection is documentation: it carries no test assertions and
placeholder bodies. The package ships its own newman collection built from
IATA's request set and example bodies with assertions taken from the
specification's MUST tables, run for every supported API version in CI.

## A restricted JSON-LD processor, written here

ONE Record uses a small, predictable subset of JSON-LD. Implementing exactly
that subset (and rejecting the rest clearly) is smaller, faster and safer than
depending on a general processor, and the maintained PHP ones are old. The
subset is documented in [JSON-LD subset](guide/json-ld.md).

## RS256 only, nothing negotiable from the token header

The JWT verifier accepts RS256 and nothing else; issuer, expiry, not-before
and (optionally) audience are checked; keys come from a resolver, never from
the token. Why: the ONE Record security model needs one algorithm, and every
JWT weakness in the wild starts with trusting the header.

## Forgetting data is a first-class host operation

The API has no delete for logistics objects and keeps an audit trail. Real
deployments still have data-protection obligations, so the SDK makes
"forget" explicit: access is closed at once and the store is asked to erase.
The wrapper decides when.
