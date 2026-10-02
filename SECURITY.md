# Security policy

## Reporting a vulnerability

Please report security issues privately to Lambda Twelve rather than through a
public issue. Use GitHub's private vulnerability reporting on this repository
("Report a vulnerability" under the Security tab). Include the affected
version, a description of the issue and, where possible, a proof of concept.

You will receive an acknowledgement within five working days. We aim to publish
a fix and a security advisory within 90 days of the report, sooner for issues
that are being exploited.

## Scope

In scope: everything under `src/` and `bin/` of this package, including the
RS256 JWT verifier, the client-credentials token endpoint, the JSON-LD parser,
the change applier and every HTTP endpoint of the PSR-15 server.

Out of scope: the wrapper packages (report those to their own repositories),
NE:ONE, and deployments of this package by third parties.

## Supported versions

Before 1.0.0, only the latest pre-release (1.0.0-betaN, then 1.0.0-rcN)
receives security fixes. From 1.0.0 on, the latest minor release of the current
major version is supported.
