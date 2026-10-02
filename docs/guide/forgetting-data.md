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

`DataHolder::forget($object, events: true, grants: true)` erases every
revision (`LogisticsObjectStore::erase()`), the object's events
(`LogisticsEventStore::eraseFor()`) and the grants on it
(`AccessDelegationStore::eraseFor()`), in one unit of work. From then on the
server answers 404 for the object, its audit trail and its events, exactly as
for a URI that never existed. Pass `events: false` where status history must
outlive the object. Action requests that referenced the object stay in their
store (they are part of other parties' history); a change request against a
forgotten object fails on acceptance with a 404 error.

## What the host must do

- **Close access in your own rules.** Grants in the store are erased; a policy
  with rules of its own (ownership tables, say) must stop answering for the
  URI itself.
- **Tell partners if you must.** There is no notification type for deletion.
  A host that wants to inform subscribers does so out of band.
- **Scheduling is yours.** Retention periods, legal holds and approval
  workflows belong in the wrapper or the application, not here.

## Why not a soft delete

A hidden object (`Decision::Hide` in the access policy) is the right tool for
"this partner may no longer see this". Forgetting is for "this must no longer
exist on this server", and the two are kept apart so neither is used for the
other by accident.
