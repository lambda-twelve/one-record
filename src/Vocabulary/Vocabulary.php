<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary;

use LambdaTwelve\OneRecord\Spec\DataModelVersion;
use LambdaTwelve\OneRecord\Spec\Namespaces;
use LambdaTwelve\OneRecord\Vocabulary\Generated\ApiSchema;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CargoSchema;
use LambdaTwelve\OneRecord\Vocabulary\Generated\CodeListSchema;

/**
 * Runtime questions about the ONE Record ontologies: is this a class, which
 * properties may a Piece carry, is this IRI a member of a code list, which of
 * these types is the most specific one.
 *
 * The default instance knows every term of every merged edition. A view
 * limited to one data model version (`for(DataModelVersion::V3_2)`) hides
 * terms introduced later, so a document meant for a partner on an older
 * ontology can be validated against what that partner understands. The API
 * ontology is not filtered here: API versions are negotiated per request and
 * handled by the server and client layers.
 */
final class Vocabulary
{
    public const string LOGISTICS_OBJECT = Namespaces::CARGO . 'LogisticsObject';

    /** @var array<string, ClassInfo> */
    private array $classes = [];

    /** @var array<string, PropertyInfo> */
    private array $properties = [];

    /** @var array<string, IndividualInfo> */
    private array $individuals = [];

    /** @var array<string, CodeListInfo> */
    private array $codeLists = [];

    /** @var array<string, array<string, PropertyInfo>> class IRI => accepted properties, memoised */
    private array $acceptedCache = [];

    /** @var array<string, list<string>> class IRI => ancestors, memoised */
    private array $ancestorCache = [];

    /** @var list<PropertyInfo> */
    private array $anyDomainProperties = [];

    private static ?self $default = null;

    private function __construct(private readonly ?DataModelVersion $ceiling)
    {
        $this->load();
    }

    /**
     * Every term of every merged edition.
     */
    public static function default(): self
    {
        return self::$default ??= new self(null);
    }

    /**
     * Only the terms a partner on this data model version understands.
     */
    public static function for(DataModelVersion $version): self
    {
        return new self($version);
    }

    public function ceiling(): ?DataModelVersion
    {
        return $this->ceiling;
    }

    public function class(string $iri): ?ClassInfo
    {
        return $this->classes[$iri] ?? null;
    }

    public function isClass(string $iri): bool
    {
        return isset($this->classes[$iri]);
    }

    public function property(string $iri): ?PropertyInfo
    {
        return $this->properties[$iri] ?? null;
    }

    public function isProperty(string $iri): bool
    {
        return isset($this->properties[$iri]);
    }

    public function individual(string $iri): ?IndividualInfo
    {
        return $this->individuals[$iri] ?? null;
    }

    public function codeList(string $iri): ?CodeListInfo
    {
        return $this->codeLists[$iri] ?? null;
    }

    /**
     * True for a published member of a code list. Members of open lists that
     * are not published (an ISO currency, say) are not "known" but may still
     * be acceptable; callers decide with isOpenCodeList().
     */
    public function isCodeListMember(string $iri): bool
    {
        [$list, $code] = $this->splitCodeIri($iri);

        return $list !== null && $code !== null && isset($this->codeLists[$list]) && $this->codeLists[$list]->hasCode($code);
    }

    /**
     * True when the IRI points into an open code list, whether or not the code is published.
     */
    public function isOpenCodeListIri(string $iri): bool
    {
        [$list, $code] = $this->splitCodeIri($iri);

        return $list !== null && $code !== null && isset($this->codeLists[$list]) && $this->codeLists[$list]->open;
    }

    /**
     * @return list<string> ancestor class IRIs, nearest first
     */
    public function ancestors(string $classIri): array
    {
        if (isset($this->ancestorCache[$classIri])) {
            return $this->ancestorCache[$classIri];
        }
        $out = [];
        $queue = $this->classes[$classIri]->parents ?? [];
        while ($queue !== []) {
            $parent = array_shift($queue);
            if (\in_array($parent, $out, true)) {
                continue;
            }
            $out[] = $parent;
            foreach ($this->classes[$parent]->parents ?? [] as $grand) {
                $queue[] = $grand;
            }
        }

        return $this->ancestorCache[$classIri] = $out;
    }

    public function isSubclassOf(string $classIri, string $ancestorIri): bool
    {
        return $classIri === $ancestorIri || \in_array($ancestorIri, $this->ancestors($classIri), true);
    }

    /**
     * Whether instances of this class are logistics objects (published under
     * their own URI) rather than embedded objects.
     */
    public function isLogisticsObjectClass(string $classIri): bool
    {
        return $this->isSubclassOf($classIri, self::LOGISTICS_OBJECT);
    }

    /**
     * Drops every type that is an ancestor of another type in the list, so a
     * body typed [Company, Organization, LogisticsAgent, LogisticsObject]
     * yields [Company]. Unknown types are kept: the server must not drop what
     * it does not understand.
     *
     * @param list<string> $classIris
     * @return list<string>
     */
    public function mostSpecific(array $classIris): array
    {
        $out = [];
        foreach ($classIris as $candidate) {
            $dominated = false;
            foreach ($classIris as $other) {
                if ($other !== $candidate && \in_array($candidate, $this->ancestors($other), true)) {
                    $dominated = true;
                    break;
                }
            }
            if (!$dominated && !\in_array($candidate, $out, true)) {
                $out[] = $candidate;
            }
        }

        return $out;
    }

    /**
     * Every property an instance of the class may carry: its own restrictions,
     * those inherited from ancestors, and properties declared for any class.
     *
     * @return array<string, PropertyInfo> keyed by property IRI
     */
    public function propertiesOf(string $classIri): array
    {
        if (isset($this->acceptedCache[$classIri])) {
            return $this->acceptedCache[$classIri];
        }
        $out = [];
        foreach ([$classIri, ...$this->ancestors($classIri)] as $iri) {
            foreach ($this->classes[$iri]->ownProperties ?? [] as $propertyIri => $_) {
                if (isset($this->properties[$propertyIri])) {
                    $out[$propertyIri] = $this->properties[$propertyIri];
                }
            }
        }
        foreach ($this->properties as $propertyIri => $property) {
            if (!isset($out[$propertyIri]) && \in_array($classIri, $property->domains, true)) {
                $out[$propertyIri] = $property;
            }
        }
        foreach ($this->anyDomainProperties as $property) {
            $out[$property->iri] ??= $property;
        }
        ksort($out, SORT_STRING);

        return $this->acceptedCache[$classIri] = $out;
    }

    /**
     * May an instance of any of these classes carry this property?
     *
     * @param list<string> $classIris
     */
    public function accepts(array $classIris, string $propertyIri): bool
    {
        foreach ($classIris as $classIri) {
            if (isset($this->propertiesOf($classIri)[$propertyIri])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return list<ClassInfo>
     */
    public function classes(): array
    {
        return array_values($this->classes);
    }

    /**
     * @return list<PropertyInfo>
     */
    public function properties(): array
    {
        return array_values($this->properties);
    }

    /**
     * @return list<CodeListInfo>
     */
    public function codeLists(): array
    {
        return array_values($this->codeLists);
    }

    private function load(): void
    {
        // The cargo ontology is filtered by the ceiling; the API ontology is negotiated per request instead.
        $this->loadClasses(CargoSchema::CLASSES, true);
        $this->loadProperties(CargoSchema::PROPERTIES, true);
        $this->loadIndividuals(CargoSchema::INDIVIDUALS, true);
        $this->loadClasses(ApiSchema::CLASSES, false);
        $this->loadProperties(ApiSchema::PROPERTIES, false);
        $this->loadIndividuals(ApiSchema::INDIVIDUALS, false);
        foreach (CodeListSchema::LISTS as $iri => $row) {
            if (!$this->visible($row['since'], null)) {
                continue;
            }
            // Numeric codes ("0", "10") come back as integer keys from PHP; normalise to strings.
            $codes = [];
            foreach ($row['codes'] as $code => $since) {
                if ($this->visible($since, null)) {
                    $codes[(string) $code] = $since;
                }
            }
            $this->codeLists[$iri] = new CodeListInfo($iri, $row['name'], $row['open'], $row['since'], $codes);
        }
        // Properties attached to a class in a later version are hidden with the version.
        if ($this->ceiling !== null) {
            foreach ($this->classes as $iri => $class) {
                $own = array_filter($class->ownProperties, fn(string $since): bool => $this->visible($since, null));
                $this->classes[$iri] = new ClassInfo($iri, $class->name, $class->parents, $own, $class->since, $class->deprecatedIn, $class->removedIn);
            }
        }
    }

    /**
     * @param array<string, array{name: string, parents: list<string>, properties: array<string, string>, since: string, deprecatedIn: ?string, removedIn: ?string}> $rows
     */
    private function loadClasses(array $rows, bool $filtered): void
    {
        foreach ($rows as $iri => $row) {
            if ($filtered && !$this->visible($row['since'], $row['removedIn'])) {
                continue;
            }
            $this->classes[$iri] = new ClassInfo($iri, $row['name'], $row['parents'], $row['properties'], $row['since'], $row['deprecatedIn'], $row['removedIn']);
        }
    }

    /**
     * @param array<string, array{name: string, kind: 'object'|'datatype', ranges: list<string>, domains: list<string>, since: string, deprecatedIn: ?string, removedIn: ?string}> $rows
     */
    private function loadProperties(array $rows, bool $filtered): void
    {
        foreach ($rows as $iri => $row) {
            if ($filtered && !$this->visible($row['since'], $row['removedIn'])) {
                continue;
            }
            $info = new PropertyInfo($iri, $row['name'], PropertyKind::from($row['kind']), $row['ranges'], $row['domains'], $row['since'], $row['deprecatedIn'], $row['removedIn']);
            $this->properties[$iri] = $info;
            if ($info->acceptsAnyDomain()) {
                $this->anyDomainProperties[] = $info;
            }
        }
    }

    /**
     * @param array<string, array{name: string, types: list<string>, since: string, removedIn: ?string}> $rows
     */
    private function loadIndividuals(array $rows, bool $filtered): void
    {
        foreach ($rows as $iri => $row) {
            if ($filtered && !$this->visible($row['since'], $row['removedIn'])) {
                continue;
            }
            $this->individuals[$iri] = new IndividualInfo($iri, $row['name'], $row['types'], $row['since'], $row['removedIn']);
        }
    }

    private function visible(string $since, ?string $removedIn): bool
    {
        if ($this->ceiling === null) {
            return true;
        }
        if (version_compare($since, $this->ceiling->value, '>')) {
            return false;
        }

        return $removedIn === null || version_compare($removedIn, $this->ceiling->value, '>');
    }

    /**
     * @return array{?string, ?string}
     */
    private function splitCodeIri(string $iri): array
    {
        if (!str_starts_with($iri, Namespaces::CODE_LISTS)) {
            return [null, null];
        }
        $hash = strpos($iri, '#');
        if ($hash === false) {
            return [null, null];
        }

        return [substr($iri, 0, $hash), substr($iri, $hash + 1)];
    }
}
