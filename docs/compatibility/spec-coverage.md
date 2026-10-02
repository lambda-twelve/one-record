# Spec coverage

Every endpoint, header, query parameter and lifecycle rule of the ONE Record
API specification, with its status in this package. Status values:
**implemented**, **partial** (with a note), **planned**, **not planned** (with
a reason). Each implemented row links to the test that proves it.

Spec sources: [2025-07 edition](https://iata-cargo.github.io/ONE-Record/2025-07/API-Security/)
(API 2.2.0) and [2026-07 edition](https://iata-cargo.github.io/ONE-Record/2026-07/API-Security/)
(API 2.3.0). Pinned commits are recorded in `src/Vocabulary/Generated/Manifest.php`.

## Compliance collection

Besides the PHPUnit tests linked below, `tests/Compliance/` holds a newman
collection generated from the specification's example bodies with assertions
taken from its MUST tables (status codes, `Location`, `Type`, `Content-Type`
version echo, `Content-Language`, revision headers, RFC 1123 dates, `api:Error`
bodies, the action-request lifecycle end to end). CI runs it against
`bin/serve` once for API 2.2.0 and once for 2.3.0; locally, `ddev compliance`.
The request set mirrors IATA's own Postman collection, which carries no
assertions of its own.

## Endpoints

| Endpoint | API | Status | Notes / test |
| --- | --- | --- | --- |
| `GET /` server information | 2.2, 2.3 | implemented | [ServerInformationAndNegotiationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ServerInformationAndNegotiationTest.php) |
| `POST /logistics-objects` | 2.2, 2.3 | implemented | Internal-only per spec; exposed only when the access policy allows. [CreateObjectAndNotificationsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/CreateObjectAndNotificationsTest.php) |
| `GET /logistics-objects/{id}` | 2.2, 2.3 | implemented | incl. `?at=` historical reads and `?embedded=true`. [LogisticsObjectReadTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsObjectReadTest.php) |
| `HEAD /logistics-objects/{id}` | 2.2, 2.3 | implemented | [LogisticsObjectReadTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsObjectReadTest.php) |
| `PATCH /logistics-objects/{id}` (Change → ChangeRequest) | 2.2, 2.3 | implemented | 201 + `Location` + `Type`; the holder's own changes are applied at once. [ChangeRequestsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ChangeRequestsTest.php) |
| `POST /logistics-objects/{id}` (Verification → VerificationRequest) | 2.2, 2.3 | implemented | [SubscriptionsDelegationsAndVerificationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/SubscriptionsDelegationsAndVerificationTest.php) |
| `GET /logistics-objects/{id}/audit-trail` | 2.2, 2.3 | implemented | `updated-from`, `updated-to`, `status`. [LogisticsObjectReadTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsObjectReadTest.php), [ChangeRequestsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ChangeRequestsTest.php) |
| `POST /logistics-objects/{id}/logistics-events` | 2.2, 2.3 | implemented | [LogisticsEventsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsEventsTest.php) |
| `GET /logistics-objects/{id}/logistics-events` | 2.2, 2.3 | implemented | `event-code`, `created-after/-before`, `occurred-after/-before`, `sort`, `limit`, `skip`; `api:Collection` body. [LogisticsEventsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsEventsTest.php) |
| `HEAD /logistics-objects/{id}/logistics-events` | 2.2, 2.3 | implemented | [LogisticsEventsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsEventsTest.php) |
| `GET /logistics-objects/{id}/logistics-events/{eventId}` | 2.2, 2.3 | implemented | [LogisticsEventsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsEventsTest.php) |
| `POST /logistics-events` (bulk, 207 Multi-Status) | 2.3 | implemented | Optional in the spec; off by default (`ServerConfig::$bulkLogisticsEvents`); 404 at 2.2. [SubscriptionsDelegationsAndVerificationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/SubscriptionsDelegationsAndVerificationTest.php) |
| `POST /notifications` | 2.2, 2.3 | implemented | 204; raises `NotificationReceived`. [CreateObjectAndNotificationsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/CreateObjectAndNotificationsTest.php) |
| `GET /subscriptions?topicType&topic` | 2.2, 2.3 | implemented | Answered from the host's own subscription interests; several as `api:Collection` (question 17). [SubscriptionsDelegationsAndVerificationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/SubscriptionsDelegationsAndVerificationTest.php) |
| `POST /subscriptions` (Subscription → SubscriptionRequest) | 2.2, 2.3 | implemented | [SubscriptionsDelegationsAndVerificationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/SubscriptionsDelegationsAndVerificationTest.php) |
| `POST /access-delegations` (AccessDelegation → AccessDelegationRequest) | 2.2, 2.3 | implemented | `isRequestedFor` read as list or single value; `api:expiresAt` honoured. [SubscriptionsDelegationsAndVerificationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/SubscriptionsDelegationsAndVerificationTest.php) |
| `GET /action-requests/{id}` | 2.2, 2.3 | implemented | Status-since and history at 2.3 only. [ChangeRequestsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ChangeRequestsTest.php) |
| `HEAD /action-requests/{id}` | 2.2, 2.3 | implemented | [ChangeRequestsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ChangeRequestsTest.php) |
| `PATCH /action-requests/{id}?status=` | 2.2, 2.3 | implemented | Internal-only per spec; exposed only when the access policy allows. [ChangeRequestsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ChangeRequestsTest.php) |
| `DELETE /action-requests/{id}` (revoke) | 2.2, 2.3 | implemented | 422 on a state that cannot be revoked at 2.3; 400 at 2.2. [ChangeRequestsTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ChangeRequestsTest.php) |
| `POST /oauth/token` (client credentials) | n/a | implemented | Not part of the ONE Record API; an optional helper component (`Auth\TokenEndpoint`). [TokenEndpointAndAuthenticatorTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Unit/Auth/TokenEndpointAndAuthenticatorTest.php) |

## Client

`Client\OneRecordClient` covers the same endpoints from the consuming side,
negotiating the API version from the partner's server information. Proven by
[OneRecordClientTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Unit/Client/OneRecordClientTest.php)
against scripted answers and by
[ClientAgainstServerTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ClientAgainstServerTest.php)
against this package's own server at both versions.

| Client capability | Status | Notes |
| --- | --- | --- |
| Server information discovery, cached (PSR-16) | implemented | |
| API version negotiation (highest common), forced version | implemented | 2.3-only properties read as optional |
| Read / HEAD logistics objects, `?at=`, `?embedded=` | implemented | Revision metadata on the response object, stripped from the body |
| Create object, change request, verification request | implemented | |
| Audit trail with filters, parsed action requests | implemented | |
| Post event, list / filter events, read event | implemented | |
| Bulk events with per-object fallback | implemented | Falls back on 404/405 or at 2.2.0 |
| Subscribe, query offered subscriptions, access delegation | implemented | Single `Subscription` or `Collection` answers |
| Action requests: read, decide, revoke | implemented | |
| Send notification | implemented | |
| OAuth 2.0 client credentials token provider (PSR-18 + PSR-16) | implemented | `client_secret_post` and `client_secret_basic`, refresh before expiry |
| Typed errors with the partner's `api:Error` | implemented | No automatic retries, by design |

## Cross-cutting

| Rule | Status | Notes |
| --- | --- | --- |
| `Accept` version negotiation and `Content-Type` echo | implemented | [ServerInformationAndNegotiationTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/ServerInformationAndNegotiationTest.php) |
| `Content-Language` on every response | implemented | Languages from `ServerConfig::$languages` |
| `Type` header with the most specific class | implemented | On every response, errors included (`api:Error`) |
| `Revision`, `Latest-Revision`, `Last-Modified` (RFC 1123) | implemented | [LogisticsObjectReadTest](https://github.com/lambda-twelve/one-record/blob/main/tests/Integration/LogisticsObjectReadTest.php) |
| `api:hasRevision` / `api:hasLatestRevision` in logistics-object bodies | implemented | |
| `api:Error` body with `api:ErrorDetail` on every 4xx/5xx; 2.3 standard titles | implemented | `api:hasSeverity` written at 2.3 only |
| Access control per logistics object with the four permissions, default deny (403) | implemented | A policy may answer 404 instead (`Decision::Hide`); grants from accepted access delegations honoured |
| Stable embedded-object ids (`internal:<uuid5>`) | implemented | Minted when an object is first stored; blank nodes in changes minted on application |
| Change application: atomic, deletes before adds, revision check, no `events` edits, subject validation | implemented | A failed application leaves the request `REQUEST_FAILED` with errors |
| Action-request state machines (change/subscription/delegation and verification) | implemented | Other pending changes on the replaced revision rejected with 409 |
| Notification fan-out: by identifier or type, event-type filter, `notifyRequestStatusChange`, `sendLogisticsObjectBody` | implemented | Queued in the `NotificationOutbox`; the host sends |
| Request size limit, UTF-8 bodies, no 301 redirects | implemented | 413 above `ServerConfig::$maxBodyBytes` |
| `text/turtle` content type | not planned | JSON-LD is the mandatory serialisation; Turtle may follow after 1.0.0 |
