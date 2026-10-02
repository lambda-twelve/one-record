# Using the client

`Client\OneRecordClient` talks to one partner's ONE Record server over any
PSR-18 HTTP client. It discovers the partner's server information, picks the
highest API version both sides support, and parses answers into the same
model and API documents the server side uses.

## Construction

```php
use LambdaTwelve\OneRecord\Client\ClientCredentialsTokenProvider;
use LambdaTwelve\OneRecord\Client\OneRecordClient;

$tokens = new ClientCredentialsTokenProvider(
    $httpClient, $requestFactory, $streamFactory, $clock,
    tokenUrl: 'https://1r.partner.example/oauth/token',
    clientId: 'our-client-id',
    clientSecret: $secret,
    cache: $psr16Cache,          // optional: tokens survive the process
);
$client = new OneRecordClient(
    $httpClient, $requestFactory, $streamFactory, $tokens,
    serverEndpoint: 'https://1r.partner.example',
    cache: $psr16Cache,          // optional: caches the partner's server information
);
```

Everything is an interface: PSR-18 client, PSR-17 factories, PSR-20 clock,
PSR-16 cache, PSR-3 logger. The token provider decides where bearer tokens
come from: `ClientCredentialsTokenProvider` runs the OAuth 2.0 client
credentials flow the ONE Record security model prescribes and refreshes
before expiry; `StaticTokenProvider` is for fixed tokens and tests.

## Version negotiation

The first call fetches `GET /` and keeps the `ServerInformation`. The client
then speaks the highest API version both it and the partner list, in `Accept`
and `Content-Type`, and reads 2.3-only properties as optional so a 2.2 partner
parses fine. `withApiVersion()` forces a version; `apiVersion()` tells which
one is in use; a partner with no common version raises `ClientException`.

## A worked example

```php
--8<-- "docs/examples/use-client.php"
```

## What it can do

| Method | Endpoint | Returns |
| --- | --- | --- |
| `serverInformation()` | `GET /` | `Api\ServerInformation` |
| `getLogisticsObject($iri, at:, embedded:)` | `GET /logistics-objects/{id}` | `LogisticsObjectResponse` (object plus revision headers) |
| `headLogisticsObject($iri)` | `HEAD` | `LogisticsObjectResponse` without the object |
| `createLogisticsObject($object)` | `POST /logistics-objects` | the new URI |
| `requestChange(Change)` | `PATCH /logistics-objects/{id}` | the ChangeRequest URI |
| `requestVerification(Verification)` | `POST /logistics-objects/{id}` | the VerificationRequest URI |
| `getAuditTrail($iri, updatedFrom:, updatedTo:, status:)` | `GET …/audit-trail` | `AuditTrail` with parsed action requests |
| `postLogisticsEvent($iri, $event)` | `POST …/logistics-events` | the event URI |
| `postLogisticsEvents($event, $objects)` | `POST /logistics-events` (2.3), else per object | one `BulkEventResult` per object |
| `getLogisticsEvents($iri, EventFilter)` | `GET …/logistics-events` | `EventList` |
| `getLogisticsEvent($iri)` | `GET …/logistics-events/{id}` | `Model\LogisticsEvent` |
| `subscribe(Subscription)` | `POST /subscriptions` | the SubscriptionRequest URI |
| `getSubscriptions(TopicType, $topic)` | `GET /subscriptions?…` | `list<Subscription>` |
| `requestAccessDelegation(AccessDelegation)` | `POST /access-delegations` | the request URI |
| `getActionRequest($iri)` | `GET /action-requests/{id}` | `Api\ActionRequest` |
| `updateActionRequestStatus($iri, RequestStatus)` | `PATCH …?status=` | nothing |
| `revokeActionRequest($iri)` | `DELETE /action-requests/{id}` | nothing |
| `sendNotification(Notification)` | `POST /notifications` | nothing |

The object a `GET` returns has the server's `api:hasRevision` and
`api:hasLatestRevision` stripped (they are on the response object instead),
so it compares equal to what the holder stored and can be diffed with
`ChangeBuilder` straight away.

## Errors

A 4xx or 5xx answer is an `OneRecordHttpException` with the status, the
parsed `api:Error` when the partner sent one, and the raw response. Transport
failures, unreadable bodies and negotiation failures are `ClientException`.
Both are runtime exceptions; neither is retried, because whether and when to
retry is the host's decision.

## Delivering notifications

The server queues outgoing notifications in the `NotificationOutbox`; the
host drains it and calls `sendNotification()` on a client built for the
recipient's endpoint (`OutboundNotification::suggestedEndpoint()` derives it
from the recipient's agent URI). One client per partner, cached by the host,
keeps server information and tokens warm.
