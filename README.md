# lambda-twelve/one-record

[![CI](https://github.com/lambda-twelve/one-record/actions/workflows/ci.yml/badge.svg?branch=main)](https://github.com/lambda-twelve/one-record/actions/workflows/ci.yml)
[![Coverage](https://img.shields.io/endpoint?url=https%3A%2F%2Flambda-twelve.github.io%2Fone-record%2Fcoverage.json)](https://lambda-twelve.github.io/one-record/coverage/)
[![Architecture](https://github.com/lambda-twelve/one-record/actions/workflows/architecture.yml/badge.svg?branch=main)](https://github.com/lambda-twelve/one-record/actions/workflows/architecture.yml)
[![Docs](https://github.com/lambda-twelve/one-record/actions/workflows/docs.yml/badge.svg?branch=main)](https://lambda-twelve.github.io/one-record/)
[![PHP ^8.3](https://img.shields.io/badge/php-%5E8.3-777BB4?logo=php&logoColor=white)](composer.json)

A framework-agnostic PHP implementation of **IATA ONE Record**: the server side
(a data holder publishing logistics objects to partners) and the client side
(talking to other parties' ONE Record servers), as one Composer package.

> **Status:** under construction. Nothing is released yet. See the
> [roadmap](https://lambda-twelve.github.io/one-record/roadmap/).

## What it supports

| Specification | Versions |
| --- | --- |
| ONE Record API | 2.2.0 and 2.3.0, negotiated per request (`Accept: application/ld+json; version=…`) |
| ONE Record cargo ontology (data model) | 3.2 and 3.3 |
| ONE Record code lists | 1.1.0 |

The server is a single PSR-15 request handler you mount under any base path.
Everything a host must supply (storage, authentication, access policy,
outgoing notifications) is an interface with an in-memory reference
implementation, so the package runs with no framework at all. Wrappers for
Laravel and Drupal live in their own packages.

## Requirements

PHP 8.3 or newer with `ext-json` and `ext-openssl`. Runtime dependencies are
PSR interfaces only.

## Installation

```sh
composer require lambda-twelve/one-record
```

## Documentation

Full documentation, including a server quick start, a client quick start, the
SPI a host implements, spec coverage and open specification questions, is at
<https://lambda-twelve.github.io/one-record/>.

## Licence

Apache-2.0. The ONE Record specification and ontologies are IATA's, licensed
under the MIT License; see `NOTICE`.
