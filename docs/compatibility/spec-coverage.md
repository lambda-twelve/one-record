# Spec coverage

Every endpoint, header, query parameter and lifecycle rule of the ONE Record
API specification, with its status in this package. Status values:
**implemented**, **partial** (with a note), **planned**, **not planned** (with
a reason). Each implemented row links to the test that proves it.

Spec sources: [2025-07 edition](https://iata-cargo.github.io/ONE-Record/2025-07/API-Security/)
(API 2.2.0) and [2026-07 edition](https://iata-cargo.github.io/ONE-Record/2026-07/API-Security/)
(API 2.3.0). Pinned commits are recorded in `src/Vocabulary/Generated/Manifest.php`.

## Endpoints

| Endpoint | API | Status | Notes / test |
| --- | --- | --- | --- |
| `GET /` server information | 2.2, 2.3 | planned | |
| `POST /logistics-objects` | 2.2, 2.3 | planned | Internal-only per spec; exposed only when the access policy allows |
| `GET /logistics-objects/{id}` | 2.2, 2.3 | planned | incl. `?at=` historical reads and `?embedded=true` |
| `HEAD /logistics-objects/{id}` | 2.2, 2.3 | planned | |
| `PATCH /logistics-objects/{id}` (Change → ChangeRequest) | 2.2, 2.3 | planned | 201 + `Location` + `Type` |
| `POST /logistics-objects/{id}` (Verification → VerificationRequest) | 2.2, 2.3 | planned | |
| `GET /logistics-objects/{id}/audit-trail` | 2.2, 2.3 | planned | `updated-from`, `updated-to`, `status` |
| `POST /logistics-objects/{id}/logistics-events` | 2.2, 2.3 | planned | |
| `GET /logistics-objects/{id}/logistics-events` | 2.2, 2.3 | planned | `event-code`, `created-after/-before`, `occurred-after/-before`, `sort`, `limit`, `skip`; `api:Collection` body |
| `HEAD /logistics-objects/{id}/logistics-events` | 2.2, 2.3 | planned | |
| `GET /logistics-objects/{id}/logistics-events/{eventId}` | 2.2, 2.3 | planned | |
| `POST /logistics-events` (bulk, 207 Multi-Status) | 2.3 | planned | Optional in the spec; off by default |
| `POST /notifications` | 2.2, 2.3 | planned | 204 |
| `GET /subscriptions?topicType&topic` | 2.2, 2.3 | planned | Answered from the host's own subscription interests |
| `POST /subscriptions` (Subscription → SubscriptionRequest) | 2.2, 2.3 | planned | |
| `POST /access-delegations` (AccessDelegation → AccessDelegationRequest) | 2.2, 2.3 | planned | 2.2 allows several `isRequestedFor`; 2.3 one |
| `GET /action-requests/{id}` | 2.2, 2.3 | planned | |
| `HEAD /action-requests/{id}` | 2.2, 2.3 | planned | |
| `PATCH /action-requests/{id}?status=` | 2.2, 2.3 | planned | Internal-only per spec; exposed only when the access policy allows |
| `DELETE /action-requests/{id}` (revoke) | 2.2, 2.3 | planned | 422 on a state that cannot be revoked (2.3 wording) |
| `POST /oauth/token` (client credentials) | n/a | planned | Not part of the ONE Record API; an optional helper component |

## Cross-cutting

| Rule | Status | Notes |
| --- | --- | --- |
| `Accept` version negotiation and `Content-Type` echo | planned | |
| `Content-Language` on every response; `Accept-Language` honoured for `en-US` | planned | |
| `Type` header with the most specific class | planned | |
| `Revision`, `Latest-Revision`, `Last-Modified` (RFC 1123) | planned | |
| `api:hasRevision` / `api:hasLatestRevision` in logistics-object bodies | planned | |
| `api:Error` body with `api:ErrorDetail` on every 4xx/5xx; 2.3 standard titles | planned | |
| Access control per logistics object with the four permissions, default deny (403) | planned | A policy may answer 404 instead to avoid confirming existence |
| Stable embedded-object ids (`internal:<uuid5>`) | planned | |
| Change application: atomic, deletes before adds, revision check, no `events` edits, subject validation | planned | |
| Action-request state machines (change/subscription/delegation and verification) | planned | |
| Notification fan-out: by identifier or type, event-type filter, `notifyRequestStatusChange`, `sendLogisticsObjectBody` | planned | |
| Request size limit, UTF-8 bodies, no 301 redirects | planned | |
| `text/turtle` content type | not planned | JSON-LD is the mandatory serialisation; Turtle may follow after 1.0.0 |
