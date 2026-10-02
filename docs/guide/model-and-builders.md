# Model and builders

The model layer is how a host turns its own data into ONE Record logistics
objects without writing JSON-LD by hand, and how it reads what partners send.
It depends on nothing but the [vocabulary](../compatibility/data-model.md) and
the [RDF layer](json-ld.md).

## Logistics objects

`Model\LogisticsObject` is immutable: a URI and the graph of everything said
about it, including its embedded objects (`cargo:Value`, `Party`, `Address`,
… as blank nodes, or under `internal:` ids once a server has stored them).
Revision numbers and timestamps are not part of the object; a server keeps
them on the stored revision and sends them as headers.

```php
$piece = LogisticsObject::fromJsonLd($json);            // a GET response
$piece->types();                                        // ['https://onerecord.iata.org/ns/cargo#Piece']
$piece->mostSpecificType();                             // for the Type header
$piece->literal(Cargo::goodsDescription);               // 'Books'
$piece->values(Cargo::specialHandlingCodes);            // list of Rdf\Term
$piece->toJson();                                       // compacted JSON-LD
$piece->isSameAs($other);                               // graph isomorphism
```

## Building objects

`ObjectBuilder` checks every property against the ontology as you set it: the
class must be a logistics object class, the property must be accepted by the
class (own, inherited, or declared for any class), and the value must match
the property's kind. Give it a version-limited vocabulary to refuse terms a
partner on data model 3.2 would not understand.

--8<-- "docs/examples/build-graph.php"

Value helpers cover the shapes every mapper needs:

| Helper | Produces |
| --- | --- |
| `Values::quantity(190.5, MeasurementUnitCode::KGM)` | `cargo:Value` with `numericalValue` and `unit` |
| `Values::money(5000, 'EUR')` | `cargo:CurrencyValue` with `currencyUnit` as `…/code-lists/CurrencyCode#EUR` (spec question 11) |
| `Values::code('ParticipantIdentifier', 'SHP')` | the code-list member IRI, checked against closed lists |
| `Values::codeListElement('ATH', 'IATA three-letter location code')` | `cargo:CodeListElement` for lists ONE Record does not publish |
| `Values::individual(Cargo::MASTER)` | a named individual, checked |
| `Values::dateTime($dt)`, `Values::date($dt)` | `xsd:dateTime` in UTC, `xsd:date` |
| `Values::ref('shipment')` | a reference to another object of the same graph |
| `Values::iri($uri)` | a reference to an object published elsewhere |

Plain PHP values are typed automatically: strings become `xsd:string`,
booleans `xsd:boolean`, integers `xsd:integer`, floats `xsd:double`,
`DateTimeInterface` becomes `xsd:dateTime`.

`Embedded::of(Cargo::Party)->set(...)` builds an embedded object; builders
refuse to embed a logistics object class (publish it on its own and reference
it) and refuse to build an embedded class as a logistics object.
`ObjectBuilder::unchecked()` skips the ontology for host extensions.

## Graphs of linked objects

A `LocalGraph` holds several objects that refer to each other by local key
(`local:<key>` until resolution). `resolve()` mints a URI for each object with
an `IriMinter` and rewrites every reference. `UuidIriMinter` with a seed gives
the same graph the same URIs every time, so republishing is idempotent; keys
already published can be passed in so they keep their URIs.

```php
$resolved = $graph->resolve(new UuidIriMinter('https://1r.example.com', seed: 'shipment-AER-1'));
$resolved->root();          // the waybill
$resolved->iris();          // ['waybill' => Iri, 'shipment' => Iri, ...]
```

Dangling references are refused before any URI is minted.

## Embedded object ids

Servers must give embedded objects ids that never change, because change
requests address them. `EmbeddedIdMinter` (default `Uuid5EmbeddedIdMinter`,
the spec's recommended `internal:<uuid5>` scheme) mints them when a blank
node enters a stored object; they are kept in the stored graph from then on.
