# Data model terms

The vocabulary under `src/Vocabulary/Generated` is produced by
`bin/generate-vocabulary` from IATA's ontologies for every supported edition
and merged into **one term set**. Each term records the version it first
appeared in (`since`), the version that deprecated it (`deprecatedIn`) and, if
IATA ever removes a term, the version that removed it (`removedIn`). Nothing is
dropped: a term removed in a later edition stays available so partners on an
older ontology keep working.

| Family | Source | Generated |
| --- | --- | --- |
| Cargo ontology (data model 3.2 and 3.3) | `IATA-1R-DM-Ontology.ttl` | `Generated\Cargo` (constants with the ontology's own comments as docblocks), `Generated\CargoSchema` (class hierarchy, accepted properties per class with the version that attached them, ranges, domains, deprecations) |
| API ontology (2.2.0 and 2.3.0) | `ONE-Record-API-Ontology.ttl` | `Generated\Api`, `Generated\ApiSchema` |
| Code lists (1.1.0) | `IATA-1R-CL-Ontology.ttl` | one class per list under `Generated\CodeLists` (`MeasurementUnitCode::KGM` is the member IRI; `OPEN` says whether unpublished codes are allowed; `ALL` maps codes to IRIs), `Generated\CodeListSchema` |
| Provenance | all three | `Generated\Manifest`: editions, versions and the exact commits read |

## What the vocabulary says about a class

IATA attaches properties to classes with `owl:Restriction` blocks rather than
`rdfs:domain`; the generator reads those, walks `rdfs:subClassOf` for
inheritance and honours the ontology's "Domain owl:Thing" annotations (49
properties such as `goodsDescription` or `waybillNumber` are declared usable
on any class). `Vocabulary::propertiesOf()` and `Vocabulary::accepts()` answer
"may a Piece carry this property" from that.

`Vocabulary::isLogisticsObjectClass()` tells logistics objects (published under
their own URI: Piece, Shipment, Waybill, Company, …) from embedded objects
(Value, Dimensions, Party, Address, …). Logistics events are neither.

## 3.2 versus 3.3

Data model 3.3 adds terms and deprecates two; it removes nothing.

- **New classes:** `StatusUpdateEvent` (a subclass of `LogisticsEvent`).
- **New properties, among others:** `agentReference`, `valuationCharge` and
  `otherIdentifiers` on `Waybill`, `securityDeclarations` and `totalVolume` on
  `Shipment`, `contactDetails` on `Organization`, the `StatusUpdateEvent`
  properties (`recordedPieceCount`, `recordedWeight`, `transferredTo`, …).
- **Deprecated in 3.3:** `totalDimensions`, `receivedFrom`.

`Vocabulary::for(DataModelVersion::V3_2)` returns a view that hides everything
introduced in 3.3 (including property attachments added in 3.3, such as
`securityDeclarations` on `Shipment`), so a document for a 3.2 partner can be
validated against what that partner understands. Deprecated terms remain
valid in every view; the SDK accepts them on input and logs their use.

## Where the ontologies come from

IATA's release tags were cut while the ontologies still said "release
candidate" (`3.2-rc2`, `3.3.0 RC1`); the `<edition>-standard` folders were
corrected on `master` afterwards. The generator therefore reads the ontologies
from master commit `ad5f40d539b29692881672cf91878a4d2dbe59c2` and the
specification text and examples from the tags. See
[Open specification questions](../spec-questions.md), entry 12.

## Regenerating

```sh
ddev vocab          # or: composer vocab
```

Downloads are cached under `.cache/ontologies/<commit>/`. CI regenerates on
every push and fails if the committed files differ.
