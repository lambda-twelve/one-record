# Installation

```sh
composer require lambda-twelve/one-record
```

Requirements: PHP 8.3 or newer, `ext-json`, `ext-openssl`. Runtime dependencies
are PSR interfaces only (`psr/http-message`, `psr/http-factory`,
`psr/http-server-handler`, `psr/event-dispatcher`, `psr/log`, `psr/clock`);
`psr/http-client` and `psr/simple-cache` are suggested for the client and the
JWKS resolver, `phpunit/phpunit` for the shipped store contract tests. You
bring the implementations your application already uses; for a quick start,
`nyholm/psr7` covers the HTTP messages.

!!! note
    The package is not yet published on Packagist. Until the first release,
    install from the Git repository.
