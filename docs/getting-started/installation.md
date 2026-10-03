# Installation

```sh
composer require lambda-twelve/one-record:^1.0@beta
```

The `@beta` flag lets Composer pick the pre-release without lowering your
project's minimum stability for everything else. Once 1.0.0 is out,
`composer require lambda-twelve/one-record` is enough.

Requirements: PHP 8.3 or newer, `ext-json`, `ext-openssl`. Runtime dependencies
are PSR interfaces only (`psr/http-message`, `psr/http-factory`,
`psr/http-server-handler`, `psr/event-dispatcher`, `psr/log`, `psr/clock`);
`psr/http-client` and `psr/simple-cache` are suggested for the client and the
JWKS resolver, `phpunit/phpunit` for the shipped store contract tests. You
bring the implementations your application already uses; for a quick start,
`nyholm/psr7` covers the HTTP messages.

!!! note
    Pre-releases are published on
    [Packagist](https://packagist.org/packages/lambda-twelve/one-record); the
    changelog records any public API change between betas.
