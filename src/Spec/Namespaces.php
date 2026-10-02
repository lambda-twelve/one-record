<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Spec;

/**
 * The IRIs ONE Record is built from. Kept in one place so a typo cannot hide
 * in a handler, and so the unversioned ontology IRIs (what server information
 * advertises) stay distinct from the `#`-terminated term namespaces.
 */
final class Namespaces
{
    /** Term namespace of the cargo ontology, e.g. cargo:Piece. */
    public const string CARGO = 'https://onerecord.iata.org/ns/cargo#';

    /** Term namespace of the API ontology, e.g. api:Change. */
    public const string API = 'https://onerecord.iata.org/ns/api#';

    /** Base of every code list, e.g. .../MeasurementUnitCode#KGM. */
    public const string CODE_LISTS = 'https://onerecord.iata.org/ns/code-lists/';

    public const string XSD = 'http://www.w3.org/2001/XMLSchema#';

    public const string RDF = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';

    /** Unversioned ontology IRI, for api:hasSupportedOntology. */
    public const string CARGO_ONTOLOGY = 'https://onerecord.iata.org/ns/cargo';

    public const string API_ONTOLOGY = 'https://onerecord.iata.org/ns/api';

    public const string CODE_LISTS_ONTOLOGY = 'https://onerecord.iata.org/ns/code-lists';

    /** Prefix the spec recommends for stable ids of embedded (non-logistics-object) nodes. */
    public const string EMBEDDED = 'internal:';

    /** Prefix of unresolved local references inside a graph being built. */
    public const string LOCAL = 'local:';

    private function __construct() {}

    public static function cargo(string $term): string
    {
        return self::CARGO . $term;
    }

    public static function api(string $term): string
    {
        return self::API . $term;
    }

    public static function xsd(string $term): string
    {
        return self::XSD . $term;
    }

    public static function codeList(string $list, string $code): string
    {
        return self::CODE_LISTS . $list . '#' . $code;
    }
}
