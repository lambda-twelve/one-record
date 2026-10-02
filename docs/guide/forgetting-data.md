# Forgetting data

The ONE Record API has no way to delete a logistics object. Revisions are
kept for the audit trail, and partners may hold the URI for years. Real
deployments still have obligations: a data-protection request, a contract
that ends, a test shipment that must not linger. The SDK therefore makes
forgetting an explicit host operation rather than something a partner can
trigger.

## What `forget` does

```php
$dataHolder->forget($objectIri);
```

`DataHolder::forget()` calls `LogisticsObjectStore::erase()`, which removes
every revision of the object. From then on the server answers 404 for the
object, its audit trail and its events, exactly as for a URI that never
existed. Action requests that referenced the object stay in their store (they
are part of other parties' history); a change request against a forgotten
object fails on acceptance with a 404 error.

## What the host must do

- **Close access first.** Erasing the data does not change the access policy.
  A host whose policy is table-driven should drop the grants, or the policy
  will keep answering "allowed" for a URI that then yields 404; harmless, but
  untidy.
- **Decide about events.** `erase()` is the object store's method; the event
  store is separate on purpose, because some hosts must keep status history
  for legal reasons after the object is gone. Call
  `LogisticsEventStore` cleanup yourself if events must go too.
- **Tell partners if you must.** There is no notification type for deletion.
  A host that wants to inform subscribers does so out of band.
- **Scheduling is yours.** Retention periods, legal holds and approval
  workflows belong in the wrapper or the application, not here.

## Why not a soft delete

A hidden object (`Decision::Hide` in the access policy) is the right tool for
"this partner may no longer see this". Forgetting is for "this must no longer
exist on this server", and the two are kept apart so neither is used for the
other by accident.
