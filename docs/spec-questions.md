# Open specification questions

Where the ONE Record specification is ambiguous or inconsistent, this page
records the question, where it appears, what NE:ONE does, and what this package
chose. Entries are added as they are met; none is removed, only resolved.

| # | Question | Where | NE:ONE | Our choice |
| --- | --- | --- | --- | --- |
| 1 | `PATCH /logistics-objects/{id}` success status: the 2.2 status table says 201 Created, its examples show 204 No Content. | API 2.2 *Update a Logistics Object*; fixed to 201 in 2.3 | 201 | 201 with `Location` and `Type` |
| 2 | Denied access to a logistics object: 403 Forbidden per the access-control page, but 403 confirms the object exists. | *Access control*, *Get a Logistics Object* | 403 | 403 by default; an `AccessPolicy` may answer `hide`, which yields 404 |
| 3 | Embedded-object identifiers: the spec recommends `internal:<uuid5>` but leaves the scheme to the implementor. | *Implementation guidelines*, blank nodes | `neone:<n>` | `internal:<uuid5>`; the RDF comparer treats both prefixes as blank nodes |
| 4 | Changing embedded objects: new embedded objects are blank nodes in operations, existing ones are addressed by embedded id. | *Update a Logistics Object* examples C2–C4 | Supported | Implemented as specified; earlier Lambda Twelve code refused such changes |
| 5 | Numeric literal forms: `20.0`, `"20.0"^^xsd:double` and `2.0E1` are the same RDF value with different lexical forms. | JSON-LD/RDF | Stores lexical form as received | Comparison normalises by datatype; storage keeps the lexical form as received |
| 6 | `?at=` and audit-trail timestamps use `YYYYMMDDThhmmssZ` while bodies use RFC 3339. | *Retrieve a historical Logistics Object* | Basic ISO format | Accept both forms on input; emit the spec's basic format in links |
| 7 | The audit-trail example uses `api:REQUEST_STATUS_ACCEPTED`, which does not exist in the ontology. | *Get Audit Trail* example D1 | `api:REQUEST_ACCEPTED` | Ontology names (`api:REQUEST_ACCEPTED`) |
| 8 | `hasSupportedOntology` must be unversioned IRIs per the guidelines, but the server-information example lists versioned ones. | *Server information* example A1 vs *Versioning* | Versioned | Unversioned in `hasSupportedOntology`, versioned in `hasSupportedOntologyVersion` |
| 9 | Access delegation `isRequestedFor`: a list in 2.2, exactly one organisation in 2.3. | *Access delegations* | List | List accepted at 2.2; one required at 2.3 |
| 10 | `Content-Type` version parameter: the spec requires echoing the negotiated version; NE:ONE ignores the parameter and answers `;charset=UTF-8`. | *Versioning* | Ignores | Echo the negotiated version; accept partners that do not |
| 11 | Data-model mapping questions for forwarders: unit IRIs (`code-lists/MeasurementUnitCode#KGM` vs UN/CEFACT), currency IRIs on the open `CurrencyCode` list, countries and airports as `CodeListElement`, party placement (shipment vs waybill), booking reference (`shippingRefNo` vs `Booking`), pieces vs piece groups. | Data model 3.2/3.3 | n/a | Left to the mapping layer in wrappers; the SDK supports every form |
| 12 | IATA's release tags (`2025-07`, `2026-07`) carry release-candidate ontologies (`3.2-rc2`, `3.3.0 RC1`); the `<edition>-standard` folders were corrected on master after tagging. | Repository tags vs master | Builds from its own copy | Ontologies pinned to master commit `ad5f40d5…` (versionInfo `3.2` / `3.3.0`); specification text and examples pinned to the tags |
