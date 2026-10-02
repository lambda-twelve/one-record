# JSON-LD subset

ONE Record bodies are JSON-LD, but the standard uses a small, predictable
part of it. This package implements exactly that part, in `JsonLd\Expander`,
`JsonLd\Writer` and `JsonLd\Comparer`, and refuses everything else with a
clear error rather than guessing. There is no dependency on a general JSON-LD
processor.

## What is supported

| Construct | Notes |
| --- | --- |
| One `@context` object at the document root | Prefix definitions (`"cargo": "https://onerecord.iata.org/ns/cargo#"`), `@vocab`, `@base`, `@language`, `@version`, and term definitions of the form `{"@id": …, "@type": …}` where `@type` is `@id` (values are IRIs) or a datatype IRI |
| `@id` | Absolute IRI, compact IRI, or blank node identifier (`_:b0`); a root without `@id` is a blank node (as in a `POST` body) |
| `@type` | One string or a list; terms, compact IRIs or absolute IRIs |
| Properties | Terms, compact IRIs (`cargo:grossWeight`) or absolute IRIs as keys |
| Native values | Strings (plain, or tagged with the context's `@language`), booleans (`xsd:boolean`), integers (`xsd:integer`), decimals (`xsd:double`) |
| Value objects | `{"@value": …, "@type": …}` and `{"@value": …, "@language": …}` |
| Arrays | Multi-valued properties (sets); `null` values are ignored |
| Embedded objects | Blank nodes, or nodes with their own `@id` (for example `internal:` embedded-object ids) |
| References | `{"@id": …}` and typed references `{"@id": …, "@type": …}` (the type is recorded as a fact about the referenced node) |

## What is rejected, and why

| Construct | Reason |
| --- | --- |
| A remote context (a URL string) or an array of contexts | Would require fetching and merging contexts; IATA's documents inline one object |
| `@context` inside an embedded object | Scoped contexts change the meaning of keys below them |
| `@graph` inside a node, `@included` | Named graphs: a logistics object is one graph. A *top-level* `@graph` (the flattened form NE:ONE answers with) is read as one document whose root is the node asked for, else the one node nothing references |
| `@list` | Ordered lists have no place in the ONE Record data model |
| `@set`, `@nest`, `@index`, `@reverse`, `@json`, `@direction`, `@container` | Not used by the standard; silently accepting them would misread data |
| Nested arrays | List-of-lists semantics |
| Undefined prefixes and bare terms without `@vocab` | A general processor drops them silently; here `api:Change` with a forgotten `api` prefix is an error, so a typo cannot become a lost field. Only well-known schemes (`http`, `https`, `urn`, `mailto`, `tel`, `did`, `internal`, `neone`, `local`, …) are taken as absolute IRIs |
| IRIs containing whitespace or other forbidden characters | Not IRIs |

Errors are `JsonLd\JsonLdException` and name the path, e.g.
`Ordered lists (@list) is outside the JSON-LD subset this package supports at cargo:pieces[0].cargo:x.@list`.

## Expansion

```php
use LambdaTwelve\OneRecord\JsonLd\JsonLd;

$document = JsonLd::expand($jsonText);   // or a decoded array
$document->graph;        // Rdf\Graph of triples
$document->root;         // the node the document is about (Iri or BlankNode)
$document->rootTypes();  // its rdf:type IRIs
$document->context;      // the parsed context, to write a reply in the partner's terms
```

Blank nodes are labelled in document order, so expanding the same text twice
gives the same graph. Doubles take JSON-LD's canonical form (`20.0` becomes
`"2.0E1"^^xsd:double`); strings, booleans and integers keep their lexical
form.

## Writing

```php
$json = JsonLd::compactToJson($graph, $root, $context);
```

The writer embeds every node that has triples of its own where it is first
referenced (keeping `@id` for identified nodes), writes later references as
`{"@id": …}`, writes nodes that only have types as typed references, sorts
keys and values, and uses native JSON values only where the lexical form
survives a round trip. Every `@id` is compacted against the context's
prefixes, so a code-list or named-individual reference comes out as
`{"@id": "cargo:ACTUAL"}` while object IRIs under a server stay absolute. Context coercions (`"@type": "xsd:anyURI"`,
`"@type": "@id"`) and the default `@language` are honoured, so a response can
be written in the same shape a partner used. Output always expands back to
the same graph.

## Comparing graphs

```php
use LambdaTwelve\OneRecord\JsonLd\Comparer;

$diff = (new Comparer())->compare($graphA, $graphB);
$diff->isEqual();
echo $diff->describe();   // "- <…>" / "+ <…>" lines in canonical form
```

Two graphs are equal when they are isomorphic: identical up to a relabelling
of blank nodes. By default IRIs under the `internal:` prefix are compared as
blank nodes, because servers mint their own embedded-object ids; pass
`blankNodePrefixes: ['internal:', 'neone:']` to compare with NE:ONE. Numeric
literals are compared by value (`20.0`, `"20.0"^^xsd:double` and `2.0E1` are
the same) unless `normaliseNumbers: false`; `ignoreLanguageTags: true` treats
`"x"` and `"x"@en-US` as equal. Blank nodes are labelled by iterated hashing
of their neighbourhood; nodes the hashing cannot separate are structurally
identical in ONE Record's tree-shaped documents and receive ordinal labels.

## Conformance against IATA's examples

Every example document published with the API specification (both editions)
is a fixture under `tests/Fixtures/spec/<tag>/` and must read, expand, write
and re-expand to an isomorphic graph. The handful of defective upstream
examples are listed with their reason in `SpecExamplesTest`; see
[Open specification questions](../spec-questions.md), entry 13.
