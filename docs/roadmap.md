# Roadmap

Work proceeds in phases; each phase ends with tests green and PHPStan clean.
Status: :white_check_mark: done, :construction: in progress, :hourglass: planned.

| # | Phase | Status |
| --- | --- | --- |
| 0 | Skeleton: Composer package, DDEV, PHPUnit 11, PHPStan max with the SDK-boundary rule, php-cs-fixer, CI, this site | :white_check_mark: |
| 1 | Vocabulary generator: Turtle reader, generated classes/properties/individuals/code lists for data model 3.2 and 3.3 with per-term version metadata, regeneration check in CI | :white_check_mark: |
| 2 | JSON-LD subset: reader, writer, expansion to triples, graph comparison modulo blank nodes | :white_check_mark: |
| 3 | Model, local-key graph, value builders, change diff and change applier | :white_check_mark: |
| 4 | RS256 JWT verifier/signer, JWKS resolver, client-credentials token endpoint | :white_check_mark: |
| 5 | Server: SPI, in-memory stores, every API 2.2/2.3 endpoint, action-request lifecycle, notification fan-out, `bin/serve` | :construction: |
| 6 | Compliance collection (newman) run for API 2.2.0 and 2.3.0 in CI | :hourglass: |
| 7 | Client over PSR-18 with server-information discovery and version selection | :hourglass: |
| 8 | Interoperability suite against NE:ONE as an RDF comparison oracle | :hourglass: |
| 9 | Documentation complete, first release 1.0.0-beta1 | :hourglass: |

## Release numbering

The first tag is **1.0.0-beta1**, not 0.1.0. By then the package serves and
consumes both API editions, is exercised by the compliance collection for each
of them and is compared against NE:ONE as an RDF oracle; "0.x" would understate
that. The beta label says what is actually still open: real-world use by
wrappers and partners may still force public API changes before 1.0.0, and
those are allowed (and recorded) between betas. See
[decisions](decisions.md#first-release-is-100-beta1).

## After 1.0.0

- Laravel wrapper (`lambda-twelve/one-record-laravel`) and a Drupal wrapper, in
  their own repositories.
- Bulk logistics-event creation (API 2.3.0, optional) enabled by default.
- Turtle (`text/turtle`) as an additional content type.
- Group authorisations in the access policy helpers.
- New IATA editions as they are endorsed, following the
  [runbook](guide/adding-a-spec-edition.md).
