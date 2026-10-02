# Contributing

Thank you for helping build a ONE Record implementation for PHP.

## Ground rules

- **Specification first.** Behaviour follows the IATA ONE Record API
  specification and ontologies. Where the specification is ambiguous, open an
  entry in `docs/spec-questions.md` describing the question, what NE:ONE does
  and what this package chose. Do not copy code from NE:ONE (it is OLFL-1.3);
  read it for behaviour only.
- **Framework-free.** Nothing under `src/` may depend on anything but this
  package, PSR interfaces and PHP. PHPStan enforces this. Host-specific code
  belongs in a wrapper package such as `lambda-twelve/one-record-laravel`.
- **Tests first.** Every endpoint, builder and parser change comes with tests.
  PHPStan must stay clean at level max without a baseline.

## Working locally

The project uses [DDEV](https://ddev.com) so no host PHP is needed:

```sh
ddev start
ddev composer install
ddev test            # PHPUnit
ddev phpstan         # static analysis
ddev coverage        # PHPUnit with code coverage (report in .cache/coverage)
ddev cs              # code style check (ddev cs fix to apply)
ddev vocab           # regenerate the vocabulary and show any diff
ddev compliance      # run the newman compliance collection against bin/serve
ddev interop         # start NE:ONE in Docker and run the interoperability suite
ddev docs            # preview the documentation site
```

Without DDEV, the equivalent Composer scripts are `composer test`,
`composer phpstan`, `composer cs`, `composer cs:fix` and `composer vocab`.

## Commits and pull requests

- Small commits with a clear subject and a body that says why.
- Update `CHANGELOG.md` under *Unreleased* for user-visible changes.
- Regenerated vocabulary must come from `bin/generate-vocabulary` at the
  pinned upstream commits; CI fails on a hand edit.

## Licence

By contributing you agree that your contributions are licensed under the
Apache License 2.0 that covers this project.
