# Changes and change requests

ONE Record updates logistics objects with `api:Change` documents: lists of
ADD and DELETE operations on triples, sent with `PATCH
/logistics-objects/{id}` and processed by the holder as a change request. The
`Change` namespace models those documents, computes them, and applies them.

## The Change model

`Change\Change` carries the target object, the revision it was written
against, the operations, an optional description, the
`notifyRequestStatusChange` flag and linked verification requests.
`Change::fromJsonLd()` reads a partner's PATCH body and raises
`ChangeException` (a 400) for anything malformed; `toJsonLd()` writes the
shape IATA's examples use (`api:o` as a one-element list, `api:hasRevision`
as a typed `xsd:positiveInteger`).

An operation object is a (datatype, value) pair. An XSD datatype means a
literal; any other datatype is the class of the node the value names: a
logistics object URI, an embedded object id (`internal:…`), or a blank node
label (`_:b0`) for an embedded object the change introduces.

## Revisions

`api:hasRevision` is the revision of the logistics object the change was
written against, which is the revision the requester last read. The holder
applies the change only while that is still the current revision, and the
result becomes revision + 1. This is what NE:ONE enforces (spec question 14).

## Computing a change

`ChangeBuilder::diff($from, $to, $revision)` returns the change that turns one
version of an object into another, or `null` when they are the same graph.

- Plain values and references become DELETE/ADD pairs (replace is delete
  plus add, as the spec says).
- Embedded objects are matched by content. An unchanged one produces nothing.
  When a slot holds exactly one old and one new object of the same type, the
  old one is edited in place through its embedded id (spec example C3).
  Otherwise the old object is deleted together with its triples (example C4)
  and the new one added as a blank node with its triples (example C2).
- `cargo:events` is never part of a change; the spec forbids patching events.
- A change of type is refused; a logistics object keeps its class.

Hosts use this when republishing: resolve the new graph, diff against the
stored version, apply as the holder. Partners use it to request corrections.

## Applying a change

`ChangeApplier::apply($current, $currentRevision, $change)` implements the
spec's rules for the holder:

1. `api:hasLogisticsObject` must be the object being changed.
2. The revision must match.
3. No operation may touch `cargo:events` or the object's type.
4. Subjects must be the object, one of its embedded objects, or a blank node
   that an ADD in the same change introduces.
5. Deletes run first and must match an existing value (numbers compared by
   value, so `20.0` deletes `2.0E1`); then adds, which must not duplicate an
   existing value.
6. Every added value is checked against the ontology: the property must be
   accepted by the subject's class, literals go to datatype properties and
   nodes to object properties, and booleans, numbers and date-times must be
   well-formed.
7. New embedded objects receive stable `internal:` ids and the class named by
   the operation's datatype; embedded objects left unreachable are removed
   with their triples.

The result is the new object and the list of changed properties (for
`api:hasChangedProperty` in notifications). Any failure raises
`ChangeRejected` with `api:Error` objects and leaves the object untouched; the
server records them on the change request as `REQUEST_FAILED`.
