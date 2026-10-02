# Changelog

All notable changes to this package are recorded here. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/); versions follow
[Semantic Versioning](https://semver.org/). Breaking changes are allowed before
1.0.0 and are listed under the release that makes them.

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
