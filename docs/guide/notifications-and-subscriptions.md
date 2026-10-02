# Notifications and subscriptions

ONE Record pushes: a holder tells subscribers when an object is created or
updated, when an event is posted on it, when access is granted, and when an
action request changes status. The SDK decides *who* must be told and *what*;
the host does the sending.

## Receiving

`POST /notifications` accepts an `api:Notification` from any authenticated
party, validates it, raises `Server\Event\NotificationReceived` with the
parsed `Api\Notification` and the sender, and answers 204. Nothing is stored:
what to do with a notification (fetch the object, update a status) is the
host's business, done in the event listener.

## Sending: the outbox

Every notification the server wants to send is queued in the
`NotificationOutbox` as an `OutboundNotification`: the recipient agent URI,
the `Api\Notification`, and when it was created. The host drains the outbox
(a queue job, a cron) and delivers each with the SDK client to the recipient's
`/notifications` endpoint. The server never opens a connection itself, so
egress stays under the host's control and a slow partner never slows a request.

```php
foreach ($outbox->drain() as $outbound) {
    $client->sendNotification($outbound->recipient, $outbound->notification);
}
```

## Who gets what

`Server\Notification\Fanout` applies the spec's rules:

| Trigger | Event type | Recipients |
| --- | --- | --- |
| Object created (`DataHolder::create`, `publish`) | `LOGISTICS_OBJECT_CREATED` | Subscribers to the object's type |
| Change request accepted and applied | `LOGISTICS_OBJECT_UPDATED` with `api:hasChangedProperty` | Subscribers to the object or its type |
| Logistics event posted | `LOGISTICS_EVENT_RECEIVED` with `api:hasLogisticsEvent` | Subscribers to the object or its type |
| Access delegation accepted | `LOGISTICS_OBJECT_ACCESS_GRANTED` | Each delegate |
| Action request status change, when the requestor asked | `*_REQUEST_ACCEPTED` / `_REJECTED` / `_REVOKED` / `_FAILED` / `_ACKNOWLEDGED` | The requestor |
| `DataHolder::announce` | `LOGISTICS_OBJECT_AVAILABLE` | The named partner |

A subscription lists the event types it wants (`api:includeSubscriptionEventType`);
others are not sent. With `api:sendLogisticsObjectBody: true` the object's
current JSON-LD is embedded in `api:hasLogisticsObject`; otherwise only the
URI is sent. Expired subscriptions (`api:expiresAt`) are ignored.

The changed properties of an update are the object's own properties, with an
edit inside an embedded node counted for the property it hangs off (changing a
weight's `numericalValue` reports `cargo:grossWeight`).

## Subscribing to this server

A partner sends `POST /subscriptions` with an `api:Subscription`: its own
agent URI as `api:hasSubscriber`, a topic type (`LOGISTICS_OBJECT_IDENTIFIER`
for one object, `LOGISTICS_OBJECT_TYPE` for every object of a class) and the
topic. The topic must be an object of this server (a hidden one looks like a
missing one) or a logistics-object class of the ontology. The result is a
pending `SubscriptionRequest` (201 with its `Location`) that the holder
[accepts or rejects](action-requests.md). Only accepted subscriptions receive
notifications; a subscriber revokes with `DELETE` on the request.

## Being asked to subscribe

The spec also lets a publisher ask *you* what you want: `GET
/subscriptions?topicType=…&topic=…`. The answer comes from
`SubscriptionStore::offered()`: the subscriptions the host registered as its
interests (`InMemorySubscriptionStore::offer()`). One offer is answered as an
`api:Subscription`; none or several as an `api:Collection`
([spec question 17](../spec-questions.md)).

## Subscriptions the holder sets up

When a partner has answered such a question to you, record the subscription
with `DataHolder::subscribe(Subscription)`: it becomes an accepted
`SubscriptionRequest`, so notifications reference it and the partner can
revoke it like any other.
