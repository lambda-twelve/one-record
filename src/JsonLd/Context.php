<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use LambdaTwelve\OneRecord\Spec\Namespaces;

/**
 * The part of a JSON-LD @context that ONE Record uses: prefix definitions,
 * @vocab, @base, a default @language, and term definitions that coerce a
 * property's values to IRIs (`"@type": "@id"`) or to a datatype.
 *
 * Remote contexts (a URL string), arrays of contexts, @container, @reverse,
 * @nest, @protected and @propagate are refused: IATA's documents inline a
 * small object context, and nothing in the standard needs more.
 */
final readonly class Context
{
    public const string JSON_LD_ID = '@id';

    /**
     * @param array<string, string> $prefixes prefix => IRI (the empty prefix is allowed)
     * @param array<string, array{id: string, type: ?string}> $terms term => expanded IRI and coercion ("@id" or a datatype IRI)
     */
    public function __construct(
        public array $prefixes = [],
        public array $terms = [],
        public ?string $vocab = null,
        public ?string $base = null,
        public ?string $language = null,
    ) {}

    /**
     * The context every object this package emits starts from.
     */
    public static function oneRecord(): self
    {
        return new self(['cargo' => Namespaces::CARGO, 'api' => Namespaces::API]);
    }

    /**
     * @param mixed $raw the value of "@context"
     */
    public static function fromRaw(mixed $raw, string $path = '@context'): self
    {
        if ($raw === null) {
            return new self();
        }
        if (\is_string($raw)) {
            throw JsonLdException::unsupported($path, 'A remote context (a URL)');
        }
        if (!\is_array($raw) || array_is_list($raw)) {
            throw JsonLdException::unsupported($path, 'A context that is not a single JSON object');
        }

        /** @var array<string, string> $prefixes */
        $prefixes = [];
        /** @var array<string, array{id: string, type: ?string}> $terms */
        $terms = [];
        $vocab = null;
        $base = null;
        $language = null;
        foreach ($raw as $key => $value) {
            $key = (string) $key;
            $here = $path . '.' . $key;
            if ($key === '@vocab') {
                $vocab = self::requireString($value, $here);
            } elseif ($key === '@base') {
                $base = self::requireString($value, $here);
            } elseif ($key === '@language') {
                $language = self::requireString($value, $here);
            } elseif ($key === '@version') {
                // JSON-LD 1.1 processing mode marker; nothing to do.
            } elseif (str_starts_with($key, '@')) {
                throw JsonLdException::unsupported($here, "The context keyword {$key}");
            } elseif (\is_string($value)) {
                if (!self::isAbsoluteIri($value) && !str_contains($value, ':')) {
                    throw JsonLdException::at($here, 'A term must map to an IRI or a compact IRI');
                }
                $prefixes[$key] = $value;
            } elseif (\is_array($value) && !array_is_list($value)) {
                /** @var array<string, mixed> $value */
                $terms[$key] = self::termDefinition($value, $here, $key);
            } elseif ($value === null) {
                // Explicitly undefining a term: nothing is defined, so nothing to record.
            } else {
                throw JsonLdException::at($here, 'A term definition must be an IRI string or an object');
            }
        }

        // Term ids are compact IRIs of the prefixes ("cargo:Piece"), resolved once here; a
        // term cannot refer to itself or to another term, so resolution sees prefixes only.
        $context = new self($prefixes, [], $vocab, $base, $language);
        /** @var array<string, array{id: string, type: ?string}> $resolvedTerms */
        $resolvedTerms = [];
        foreach ($terms as $term => $definition) {
            $resolvedTerms[$term] = [
                'id' => $context->expandIri($definition['id'], $path . '.' . $term, vocabRelative: true),
                'type' => $definition['type'] === null || $definition['type'] === self::JSON_LD_ID
                    ? $definition['type']
                    : $context->expandIri($definition['type'], $path . '.' . $term . '.@type', vocabRelative: true),
            ];
        }
        /** @var array<string, string> $resolvedPrefixes */
        $resolvedPrefixes = [];
        foreach ($prefixes as $prefix => $iri) {
            $resolvedPrefixes[$prefix] = $context->expandIri($iri, $path . '.' . $prefix, vocabRelative: false);
        }

        return new self($resolvedPrefixes, $resolvedTerms, $vocab, $base, $language);
    }

    /**
     * Expands a key or value to a full IRI: term definitions first, then
     * compact IRIs, then absolute IRIs, then @vocab for bare words (keys and
     * types) or @base for relative references (values).
     */
    public function expandIri(string $value, string $path, bool $vocabRelative): string
    {
        if ($value === '') {
            throw JsonLdException::at($path, 'An empty string is not an IRI');
        }
        if (preg_match('/[\s<>"{}|\\^`]/', $value) === 1) {
            throw JsonLdException::at($path, \sprintf('"%s" is not a valid IRI', $value));
        }
        if (isset($this->terms[$value])) {
            return $this->terms[$value]['id'];
        }
        if (isset($this->prefixes[$value]) && $vocabRelative) {
            return $this->prefixes[$value];
        }
        if (str_starts_with($value, '_:')) {
            return $value;
        }
        $colon = strpos($value, ':');
        if ($colon !== false) {
            $prefix = substr($value, 0, $colon);
            $local = substr($value, $colon + 1);
            if (isset($this->prefixes[$prefix]) && !str_starts_with($local, '//')) {
                return $this->prefixes[$prefix] . $local;
            }
            if (self::looksLikeAbsoluteIri($value)) {
                return $value;
            }
            throw JsonLdException::at($path, \sprintf('"%s" uses an undefined prefix', $value));
        }
        if ($vocabRelative) {
            if ($this->vocab !== null) {
                return $this->vocab . $value;
            }
            throw JsonLdException::at($path, \sprintf('"%s" is not a defined term and the context has no @vocab', $value));
        }
        if ($this->base !== null) {
            // RFC 3986 reference resolution, not concatenation: "../c" against "https://x/a/b" is "https://x/c" (AR-008).
            return self::resolveReference($this->base, $value);
        }

        throw JsonLdException::at($path, \sprintf('"%s" is a relative IRI and the context has no @base', $value));
    }

    /**
     * RFC 3986 §5.2 for the references JSON-LD allows against @base: absolute,
     * network-path, absolute-path, relative-path, query-only and fragment-only.
     */
    public static function resolveReference(string $base, string $reference): string
    {
        if (preg_match('/^[A-Za-z][A-Za-z0-9+.-]*:/', $reference) === 1) {
            return $reference;
        }
        $b = parse_url($base);
        if ($b === false || !isset($b['scheme'])) {
            return $base . $reference;
        }
        $authority = isset($b['host']) ? '//' . (isset($b['user']) ? $b['user'] . (isset($b['pass']) ? ':' . $b['pass'] : '') . '@' : '') . $b['host'] . (isset($b['port']) ? ':' . $b['port'] : '') : '';
        if (str_starts_with($reference, '//')) {
            // The reference supplies the authority; its path still gets dot-segment removal (RFC 3986 §5.2.2, R2-009).
            $n = parse_url($b['scheme'] . ':' . $reference);
            if ($n === false || !isset($n['host'])) {
                return $b['scheme'] . ':' . $reference;
            }
            $networkAuthority = '//' . (isset($n['user']) ? $n['user'] . (isset($n['pass']) ? ':' . $n['pass'] : '') . '@' : '') . $n['host'] . (isset($n['port']) ? ':' . $n['port'] : '');

            return $b['scheme'] . ':' . $networkAuthority . self::removeDotSegments($n['path'] ?? '') . (isset($n['query']) ? '?' . $n['query'] : '') . (isset($n['fragment']) ? '#' . $n['fragment'] : '');
        }
        $r = parse_url($reference);
        if ($r === false) {
            return $base . $reference;
        }
        $basePath = $b['path'] ?? '';
        if ($reference === '' || str_starts_with($reference, '#')) {
            return $b['scheme'] . ':' . $authority . self::removeDotSegments($basePath) . (isset($b['query']) ? '?' . $b['query'] : '') . $reference;
        }
        if (str_starts_with($reference, '?')) {
            return $b['scheme'] . ':' . $authority . $basePath . $reference;
        }
        $path = $r['path'] ?? '';
        if (!str_starts_with($path, '/')) {
            $directory = $authority !== '' && $basePath === '' ? '/' : substr($basePath, 0, (int) strrpos($basePath, '/') + 1);
            $path = $directory . $path;
        }
        return $b['scheme'] . ':' . $authority . self::removeDotSegments($path) . (isset($r['query']) ? '?' . $r['query'] : '') . (isset($r['fragment']) ? '#' . $r['fragment'] : '');
    }

    /**
     * RFC 3986 §5.2.4.
     */
    private static function removeDotSegments(string $path): string
    {
        if ($path === '') {
            return '';
        }
        $output = [];
        foreach (explode('/', $path) as $segment) {
            if ($segment === '.') {
                continue;
            }
            if ($segment === '..') {
                if (\count($output) > 1) {
                    array_pop($output);
                }
                continue;
            }
            $output[] = $segment;
        }
        $resolved = implode('/', $output);
        if (str_ends_with($path, '/.') || str_ends_with($path, '/..')) {
            $resolved .= '/';
        }

        return $resolved;
    }

    /**
     * The coercion the term used as a key declares ("@id", a datatype IRI, or
     * null). Only the active term counts: another alias of the same property
     * with a different coercion must not change how this key's values read (AR-008).
     */
    public function coercionOfTerm(string $key): ?string
    {
        return $this->terms[$key]['type'] ?? null;
    }

    /**
     * The coercion a term definition declares for a property: "@id", a
     * datatype IRI, or null.
     */
    public function coercionOf(string $propertyIri): ?string
    {
        foreach ($this->terms as $definition) {
            if ($definition['id'] === $propertyIri) {
                return $definition['type'];
            }
        }

        return null;
    }

    /**
     * The shortest compact form the context allows. For keys and types
     * ($vocabRelative): a defined term, @vocab, or a compact IRI through the
     * longest matching prefix. For node identifiers (@id values) terms and
     * @vocab do not apply (JSON-LD 1.1 IRI compaction), so only prefixes are
     * used; otherwise "Piece" would be written where a reader sees a relative
     * reference (AR-007).
     */
    /**
     * The shortest form of an IRI that this context reads back as that IRI.
     * In vocabulary position (keys and types) that is the first of
     * keyCandidates(); as a node identifier only compact IRIs and the IRI
     * itself qualify, since a bare term or prefix name would read as a
     * relative IRI (R2-002).
     */
    public function compactIri(string $iri, bool $vocabRelative = true): string
    {
        if ($vocabRelative) {
            return $this->keyCandidates($iri)[0];
        }
        $compact = $this->compactWithPrefix($iri);
        if ($compact !== null && $this->readsBackAs($compact, $iri, false)) {
            return $compact;
        }

        return $iri;
    }

    /**
     * Every way to write a property or type IRI in this context that expands
     * back to exactly that IRI, shortest first: term aliases, the bare prefix
     * name for a namespace IRI, the @vocab-relative name, the compact IRI, the
     * IRI itself. A candidate shadowed by a term definition for something else
     * is left out, because it would read back as that something else (R3-002).
     *
     * @return non-empty-list<string>
     */
    public function keyCandidates(string $iri): array
    {
        $candidates = [];
        $aliases = [];
        foreach ($this->terms as $term => $definition) {
            if ($definition['id'] === $iri) {
                $aliases[] = (string) $term;
            }
        }
        sort($aliases, SORT_STRING);
        array_push($candidates, ...$aliases);
        $exact = array_search($iri, $this->prefixes, true);
        if ($exact !== false && $exact !== '') {
            $candidates[] = (string) $exact;
        }
        if ($this->vocab !== null && str_starts_with($iri, $this->vocab)) {
            $local = substr($iri, \strlen($this->vocab));
            if ($local !== '' && !str_contains($local, ':') && !str_contains($local, '/') && !str_contains($local, '#')) {
                $candidates[] = $local;
            }
        }
        $compact = $this->compactWithPrefix($iri);
        if ($compact !== null) {
            $candidates[] = $compact;
        }
        $candidates[] = $iri;
        $valid = array_values(array_filter(array_unique($candidates), fn(string $c): bool => $this->readsBackAs($c, $iri, true)));
        if ($valid === []) {
            throw JsonLdException::at('', \sprintf('"%s" cannot be written in this context: every form of it reads back as something else.', $iri));
        }

        return $valid;
    }

    private function compactWithPrefix(string $iri): ?string
    {
        $best = null;
        $bestLength = 0;
        foreach ($this->prefixes as $prefix => $namespace) {
            if ($prefix !== '' && str_starts_with($iri, $namespace) && \strlen($namespace) > $bestLength && \strlen($iri) > \strlen($namespace)) {
                $best = $prefix . ':' . substr($iri, \strlen($namespace));
                $bestLength = \strlen($namespace);
            }
        }

        return $best;
    }

    private function readsBackAs(string $candidate, string $iri, bool $vocabRelative): bool
    {
        try {
            return $this->expandIri($candidate, '', $vocabRelative) === $iri;
        } catch (JsonLdException) {
            return false;
        }
    }

    /**
     * @return array<string, mixed> the @context value to write
     */
    public function toRaw(): array
    {
        $raw = [];
        foreach ($this->prefixes as $prefix => $iri) {
            $raw[$prefix] = $iri;
        }
        if ($this->vocab !== null) {
            $raw['@vocab'] = $this->vocab;
        }
        if ($this->base !== null) {
            $raw['@base'] = $this->base;
        }
        if ($this->language !== null) {
            $raw['@language'] = $this->language;
        }
        foreach ($this->terms as $term => $definition) {
            // Never compact a definition through the term it defines or through @vocab (AR-007).
            $entry = ['@id' => $this->compactIri($definition['id'], false)];
            if ($definition['type'] !== null) {
                $entry['@type'] = $definition['type'] === self::JSON_LD_ID ? self::JSON_LD_ID : $this->compactIri($definition['type']);
            }
            // A term whose name is already its compact IRI needs no @id (IATA writes {"api:p": {"@type": "xsd:anyURI"}}).
            if ($entry['@id'] === $term) {
                unset($entry['@id']);
            }
            $raw[$term] = $entry;
        }

        return $raw;
    }

    public function withLanguage(?string $language): self
    {
        return new self($this->prefixes, $this->terms, $this->vocab, $this->base, $language);
    }

    /**
     * @param array<string, mixed> $definition
     * @return array{id: string, type: ?string}
     */
    private static function termDefinition(array $definition, string $path, string $term): array
    {
        $id = $term;
        $type = null;
        foreach ($definition as $key => $value) {
            match ($key) {
                '@id' => $id = self::requireString($value, $path . '.@id'),
                '@type' => $type = self::requireString($value, $path . '.@type'),
                '@container', '@reverse', '@nest', '@context', '@protected', '@prefix', '@index', '@language', '@direction' => throw JsonLdException::unsupported($path . '.' . $key, "The term definition keyword {$key}"),
                default => throw JsonLdException::at($path . '.' . $key, 'Unknown key in a term definition'),
            };
        }

        return ['id' => $id, 'type' => $type];
    }

    private static function requireString(mixed $value, string $path): string
    {
        if (!\is_string($value) || $value === '') {
            throw JsonLdException::at($path, 'Expected a non-empty string');
        }

        return $value;
    }

    public static function isAbsoluteIri(string $value): bool
    {
        return preg_match('/^[A-Za-z][A-Za-z0-9+.-]*:/', $value) === 1 && !str_starts_with($value, '_:');
    }

    /**
     * Schemes a bare "scheme:rest" value is taken to be when the prefix is not
     * defined in the context. JSON-LD would treat any such value as an
     * absolute IRI; in ONE Record documents "api:Change" with a forgotten
     * "api" prefix is far more likely than a URI with scheme "api", so only
     * well-known schemes (and the embedded-object schemes servers use) pass.
     */
    private const array KNOWN_SCHEMES = ['http', 'https', 'urn', 'mailto', 'tel', 'did', 'file', 'ftp', 'ws', 'wss', 'internal', 'neone', 'local'];

    public static function looksLikeAbsoluteIri(string $value): bool
    {
        if (!self::isAbsoluteIri($value)) {
            return false;
        }
        if (str_contains($value, '//')) {
            return true;
        }

        return \in_array(strtolower(substr($value, 0, (int) strpos($value, ':'))), self::KNOWN_SCHEMES, true);
    }
}
