# lambda-twelve/one-record

[![CI](https://github.com/lambda-twelve/one-record/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/lambda-twelve/one-record/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/endpoint?url=https%3A%2F%2Flambda-twelve.github.io%2Fone-record%2Fcoverage.json)](https://lambda-twelve.github.io/one-record/coverage/)
[![Architecture](https://github.com/lambda-twelve/one-record/actions/workflows/architecture.yml/badge.svg?branch=main)](https://github.com/lambda-twelve/one-record/actions/workflows/architecture.yml)
[![Docs](https://github.com/lambda-twelve/one-record/actions/workflows/docs.yml/badge.svg?branch=main)](https://lambda-twelve.github.io/one-record/)
[![PHP ^8.3](https://img.shields.io/badge/php-%5E8.3-777BB4?logo=php&logoColor=white)](composer.json)

A framework-agnostic PHP implementation of **IATA ONE Record**: the server side
(a data holder publishing logistics objects to partners) and the client side
(talking to other parties' ONE Record servers), as one Composer package.

> **Status:** feature complete for API 2.2.0 and 2.3.0, heading for a first
> release tagged `1.0.0-beta1`. Not on Packagist yet; install from Git. The
> [roadmap](https://lambda-twelve.github.io/one-record/roadmap/) has the detail.

## What it supports

| Specification | Versions |
| --- | --- |
| ONE Record API | 2.2.0 and 2.3.0, negotiated per request (`Accept: application/ld+json; version=…`) and per partner in the client |
| ONE Record cargo ontology (data model) | 3.2 and 3.3 |
| ONE Record code lists | 1.1.0 |

Every endpoint of both API editions, the action-request lifecycle, notification
fan-out, access delegations, verification requests and the optional 2.3 bulk
events; a client for all of it; RS256 JWT verification and a client-credentials
token endpoint. Proven by 460+ PHPUnit tests, a newman compliance collection run
per API version, and an interoperability suite against
[NE:ONE](https://git.openlogisticsfoundation.org/wg-digitalaircargo/ne-one)
comparing both servers' answers as RDF.

## Requirements

PHP 8.3 or newer with `ext-json` and `ext-openssl`. Runtime dependencies are
PSR interfaces only; you bring the implementations your application already
uses (PSR-7/17 messages, PSR-18 client, PSR-14 dispatcher, PSR-20 clock,
PSR-3 logger, PSR-16 cache).

## Installation

```sh
composer require lambda-twelve/one-record
```

## Server in five lines

```php
$server = new InMemoryServer(new ServerConfig('https://1r.example.com', $holderIri), $authenticator, new SystemClock(), $dispatcher, $psr17, $psr17);
$server->policy->addInternal($holderIri);
(new DataHolder($server->services))->create($piece);          // publish your data in PHP
$server->policy->allow($partnerIri, $piece->iri, [Permission::GetLogisticsObject]);
$response = $server->handler->handle($request);               // the PSR-15 handler you mount
```

`InMemoryServer` wires in-memory implementations of every SPI interface. A
host replaces them one at a time with its own (a database, a queue) and keeps
the same contract tests green; `Testing\Contract` ships them.

## Client in three lines

```php
$client = new OneRecordClient($psr18, $psr17, $psr17, $tokens, 'https://1r.partner.example');
$piece = $client->getLogisticsObject($iri);                   // negotiates the API version first
$request = $client->requestChange((new ChangeBuilder())->diff($piece->object, $wanted, $piece->revision));
```

## What a host implements

Eight small interfaces in `Server\Spi`: object, event, action-request,
subscription and access-delegation stores, a notification outbox, an
authenticator and an access policy, plus an optional unit of work for
transactions. Everything else, including every endpoint, the lifecycle rules
and the JSON-LD, is the SDK's. The
[SDK boundary](https://lambda-twelve.github.io/one-record/sdk-boundary/) page
draws the line and PHPStan enforces it. Wrappers for Laravel
(`lambda-twelve/one-record-laravel`) and Drupal are built on exactly this.

## Documentation

Getting started for the server and the client, the SPI guide, the JSON-LD
subset, action requests, notifications, spec coverage per endpoint, the NE:ONE
interoperability results and every open specification question:
<https://lambda-twelve.github.io/one-record/>.

## Licence

Apache-2.0. The ONE Record specification and ontologies are IATA's, licensed
under the MIT License; see `NOTICE`. NE:ONE, used only as a test oracle, is the
Open Logistics Foundation's (OLFL-1.3) and is not part of this package.
