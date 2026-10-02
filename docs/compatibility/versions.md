# API and data model versions

The SDK supports **API 2.2.0 and 2.3.0** and **data model 3.2 and 3.3** at the
same time. Both editions are endorsed IATA standards (2025-07 and 2026-07) and
real partners speak either, so compatibility with both is a goal, not a
fallback.

## Server: negotiated per request

- Clients state the version they want in `Accept: application/ld+json; version=2.2.0`.
  The server answers in that version and echoes it in `Content-Type`. A request
  without a version gets the highest supported version, as the specification
  asks. Server information lists every supported version.
- The few body properties that exist only in 2.3.0 (action-request status
  timestamps and history, error severity) are omitted when 2.2.0 is negotiated.
  Request shapes valid in 2.2.0 stay accepted (for example, an access
  delegation naming several organisations).
- 2.3.0 additions that are purely additive (the optional bulk logistics-events
  endpoint, standardised error titles, 422 responses) are served regardless.
- The data model is version-independent by specification: the server validates
  against the newest ontology, and 3.3 is a superset of 3.2, so 3.2 payloads
  validate unchanged. Terms deprecated in 3.3 are accepted and logged, never
  rejected.

## Client: negotiated per partner

- The client fetches the partner's server information, picks the highest API
  version both sides list and speaks it from then on. When the partner lists
  nothing this client speaks, `apiVersion()` throws `ClientException`;
  `withApiVersion()` forces a version for partners that misreport.
- Every generated vocabulary term records the data model version it appeared
  in, so builders can be given a partner's version and refuse to publish a
  3.3-only property to a 3.2 server.
- Parsing is lenient: unknown predicates are ignored and 2.3-only properties
  are optional. Bulk event posting falls back to per-object posts on 404 or 405.

## Future editions

Versions are enumerations, version-gated properties live in one table and
endpoints declare the version they appeared in. Adding a new IATA edition is a
mechanical change described in [Adding a new spec edition](../guide/adding-a-spec-edition.md),
and guard tests fail CI when an edition is only half added.
