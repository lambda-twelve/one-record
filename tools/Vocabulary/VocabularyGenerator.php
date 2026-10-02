<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary;

use FilesystemIterator;
use LambdaTwelve\OneRecord\Spec\Edition;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\ClassDef;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\CodeListDef;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\IndividualDef;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\OntologyModel;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\OntologyReader;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\PropertyDef;
use LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle\TurtleParser;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;

/**
 * Turns IATA's ontologies, across every supported edition, into the PHP under
 * src/Vocabulary/Generated.
 *
 * All editions are merged into one term set: a term's definition comes from
 * the newest edition that has it, and every term is stamped with the version
 * it first appeared in, the version that deprecated it and the version that
 * removed it. That is what lets one generated vocabulary serve a 3.2 partner
 * and a 3.3 partner at the same time, and what makes a new IATA edition a
 * regeneration rather than a rewrite.
 *
 * Output is fully deterministic (sorted keys, no timestamps) so CI can
 * regenerate and fail on any diff.
 */
final class VocabularyGenerator
{
    private const string NAMESPACE = 'LambdaTwelve\\OneRecord\\Vocabulary\\Generated';

    public function __construct(
        private readonly OntologySource $source,
        private readonly string $outputDir,
    ) {}

    /**
     * @param non-empty-list<Edition> $editions oldest first
     * @return list<string> written file paths
     */
    public function generate(array $editions): array
    {
        $parser = new TurtleParser();
        $reader = new OntologyReader();

        /** @var array<string, array<string, OntologyModel>> $models edition tag => file => model */
        $models = [];
        foreach ($editions as $edition) {
            foreach (OntologyFile::cases() as $file) {
                $models[$edition->value][$file->name] = $reader->read($parser->parse($this->source->fetch($edition, $file)));
            }
        }

        $this->resetOutputDir();
        $written = [];
        $written[] = $this->writeManifest($editions, $models);

        $cargo = $this->mergeFamily($editions, $models, OntologyFile::Cargo, static fn(Edition $e): string => $e->dataModelVersion()->value);
        $written[] = $this->writeTerms('Cargo', 'the ONE Record cargo ontology (data model)', $cargo);
        $written[] = $this->writeSchema('CargoSchema', $cargo);

        $api = $this->mergeFamily($editions, $models, OntologyFile::Api, static fn(Edition $e): string => $e->apiVersion()->value);
        $written[] = $this->writeTerms('Api', 'the ONE Record API ontology', $api);
        $written[] = $this->writeSchema('ApiSchema', $api);

        $lists = $this->mergeCodeLists($editions, $models);
        foreach ($lists as $list) {
            $written[] = $this->writeCodeList($list);
        }
        $written[] = $this->writeCodeListSchema($lists);

        sort($written, SORT_STRING);

        return $written;
    }

    /**
     * @param non-empty-list<Edition> $editions
     * @param array<string, array<string, OntologyModel>> $models
     * @param callable(Edition): string $version
     * @return array{
     *   classes: array<string, array{def: ClassDef, since: string, deprecatedIn: ?string, removedIn: ?string, properties: array<string, string>}>,
     *   properties: array<string, array{def: PropertyDef, since: string, deprecatedIn: ?string, removedIn: ?string}>,
     *   individuals: array<string, array{def: IndividualDef, since: string, deprecatedIn: ?string, removedIn: ?string}>,
     *   namespace: string,
     *   versions: list<string>
     * }
     */
    private function mergeFamily(array $editions, array $models, OntologyFile $file, callable $version): array
    {
        $classes = [];
        $properties = [];
        $individuals = [];
        $versions = [];
        $namespace = '';
        /** @var array<string, list<string>> $presence term IRI => versions it appears in */
        $presence = [];

        foreach ($editions as $edition) {
            $v = $version($edition);
            $versions[] = $v;
            $model = $models[$edition->value][$file->name];
            $namespace = $model->ontologyIri;

            foreach ($model->classes as $iri => $def) {
                $entry = $classes[$iri] ?? ['def' => $def, 'since' => $v, 'deprecatedIn' => null, 'removedIn' => null, 'properties' => []];
                $entry['def'] = $def;
                if ($def->deprecated && $entry['deprecatedIn'] === null) {
                    $entry['deprecatedIn'] = $v;
                }
                foreach ($def->properties as $property => $_) {
                    $entry['properties'][$property] ??= $v;
                }
                $classes[$iri] = $entry;
            }
            foreach ($model->properties as $iri => $def) {
                $entry = $properties[$iri] ?? ['def' => $def, 'since' => $v, 'deprecatedIn' => null, 'removedIn' => null];
                $entry['def'] = $def;
                if ($def->deprecated && $entry['deprecatedIn'] === null) {
                    $entry['deprecatedIn'] = $v;
                }
                $properties[$iri] = $entry;
            }
            foreach ($model->individuals as $iri => $def) {
                $entry = $individuals[$iri] ?? ['def' => $def, 'since' => $v, 'deprecatedIn' => null, 'removedIn' => null];
                $entry['def'] = $def;
                $individuals[$iri] = $entry;
            }

            foreach ([...array_keys($model->classes), ...array_keys($model->properties), ...array_keys($model->individuals)] as $iri) {
                $presence[$iri][] = $v;
            }
        }

        // A term absent from the newest edition was removed in the first version after it was last seen.
        foreach ([&$classes, &$properties, &$individuals] as &$family) {
            foreach ($family as $iri => &$entry) {
                $entry['removedIn'] = $this->removedIn($presence[$iri] ?? [], $versions);
            }
            unset($entry);
        }
        unset($family);

        ksort($classes, SORT_STRING);
        ksort($properties, SORT_STRING);
        ksort($individuals, SORT_STRING);
        foreach ($classes as &$entry) {
            ksort($entry['properties'], SORT_STRING);
        }
        unset($entry);

        return ['classes' => $classes, 'properties' => $properties, 'individuals' => $individuals, 'namespace' => $namespace, 'versions' => $versions];
    }

    /**
     * @param list<string> $presentIn versions the term appears in, oldest first
     * @param list<string> $versions every merged version, oldest first
     */
    private function removedIn(array $presentIn, array $versions): ?string
    {
        $last = $presentIn === [] ? null : $presentIn[\count($presentIn) - 1];
        if ($last === null || $last === $versions[\count($versions) - 1]) {
            return null;
        }
        $index = array_search($last, $versions, true);

        return $index === false ? null : $versions[$index + 1] ?? null;
    }

    /**
     * @param non-empty-list<Edition> $editions
     * @param array<string, array<string, OntologyModel>> $models
     * @return array<string, array{def: CodeListDef, since: string, codes: array<int|string, array{since: string, comment: ?string}>}>
     */
    private function mergeCodeLists(array $editions, array $models): array
    {
        $lists = [];
        foreach ($editions as $edition) {
            $v = $edition->dataModelVersion()->value;
            foreach ($models[$edition->value][OntologyFile::CodeLists->name]->codeLists as $iri => $def) {
                $entry = $lists[$iri] ?? ['def' => $def, 'since' => $v, 'codes' => []];
                $entry['def'] = $def;
                foreach ($def->codes as $code => $comment) {
                    $entry['codes'][$code] = ['since' => $entry['codes'][$code]['since'] ?? $v, 'comment' => $comment];
                }
                $lists[$iri] = $entry;
            }
        }
        ksort($lists, SORT_STRING);
        foreach ($lists as &$entry) {
            ksort($entry['codes'], SORT_STRING);
        }
        unset($entry);

        return $lists;
    }

    /**
     * @param non-empty-list<Edition> $editions
     * @param array<string, array<string, OntologyModel>> $models
     */
    private function writeManifest(array $editions, array $models): string
    {
        $data = [];
        foreach ($editions as $edition) {
            $data[$edition->value] = [
                'ontologyCommit' => $edition->ontologyCommit(),
                'documentationCommit' => $edition->documentationCommit(),
                'apiVersion' => $edition->apiVersion()->value,
                'dataModelVersion' => $edition->dataModelVersion()->value,
                'codeListVersion' => $edition->codeListVersion(),
                'versionInfo' => [
                    'cargo' => $models[$edition->value][OntologyFile::Cargo->name]->versionInfo,
                    'api' => $models[$edition->value][OntologyFile::Api->name]->versionInfo,
                    'codeLists' => $models[$edition->value][OntologyFile::CodeLists->name]->versionInfo,
                ],
                'files' => [
                    'cargo' => OntologyFile::Cargo->rawUrl($edition),
                    'api' => OntologyFile::Api->rawUrl($edition),
                    'codeLists' => OntologyFile::CodeLists->rawUrl($edition),
                ],
            ];
        }
        $latest = $editions[\count($editions) - 1];

        $body = "    /**\n     * Every edition merged into this vocabulary, oldest first, with the commits the ontologies and the specification text were read from.\n     *\n"
            . "     * @var array<string, array{ontologyCommit: string, documentationCommit: string, apiVersion: string, dataModelVersion: string, codeListVersion: string, versionInfo: array{cargo: string, api: string, codeLists: string}, files: array{cargo: string, api: string, codeLists: string}}>\n     */\n"
            . '    public const array EDITIONS = ' . PhpWriter::array($data) . ";\n\n"
            . '    public const string LATEST_EDITION = ' . PhpWriter::string($latest->value) . ";\n";

        return $this->writeClass('Manifest', 'Provenance of the generated vocabulary.', $body);
    }

    /**
     * @param array{classes: array<string, array{def: ClassDef, since: string, deprecatedIn: ?string, removedIn: ?string}>, properties: array<string, array{def: PropertyDef, since: string, deprecatedIn: ?string, removedIn: ?string}>, individuals: array<string, array{def: IndividualDef, since: string, deprecatedIn: ?string, removedIn: ?string}>, namespace: string, versions: list<string>} $family
     */
    private function writeTerms(string $className, string $description, array $family): string
    {
        $firstVersion = $family['versions'][0];
        $used = [];
        $sections = [];

        foreach (['classes' => 'Classes', 'properties' => 'Properties', 'individuals' => 'Named individuals'] as $key => $title) {
            $out = "    // {$title}\n\n";
            foreach ($family[$key] as $iri => $entry) {
                $def = $entry['def'];
                $name = PhpWriter::constantName($def->name);
                if (isset($used[$name])) {
                    throw new RuntimeException(\sprintf('Constant name collision in %s: %s (%s and %s)', $className, $name, $used[$name], $iri));
                }
                $used[$name] = $iri;
                $tags = [];
                if ($entry['since'] !== $firstVersion) {
                    $tags[] = '@since ' . $entry['since'];
                }
                if ($entry['removedIn'] !== null) {
                    $tags[] = '@deprecated removed in ' . $entry['removedIn'];
                } elseif ($entry['deprecatedIn'] !== null) {
                    $tags[] = '@deprecated since ' . $entry['deprecatedIn'];
                }
                $out .= PhpWriter::docblock($def->comment, '    ', ...$tags);
                $out .= '    public const string ' . $name . ' = ' . PhpWriter::string($iri) . ";\n\n";
            }
            $sections[] = $out;
        }

        $body = '    public const string NAMESPACE = ' . PhpWriter::string($this->termNamespace($family['namespace'])) . ";\n\n" . implode("\n", $sections);

        return $this->writeClass($className, "Term IRIs of {$description}: classes, properties and named individuals.", rtrim($body) . "\n");
    }

    /**
     * @param array{classes: array<string, array{def: ClassDef, since: string, deprecatedIn: ?string, removedIn: ?string, properties: array<string, string>}>, properties: array<string, array{def: PropertyDef, since: string, deprecatedIn: ?string, removedIn: ?string}>, individuals: array<string, array{def: IndividualDef, since: string, deprecatedIn: ?string, removedIn: ?string}>, namespace: string, versions: list<string>} $family
     */
    private function writeSchema(string $className, array $family): string
    {
        $classes = [];
        foreach ($family['classes'] as $iri => $entry) {
            $classes[$iri] = [
                'name' => $entry['def']->name,
                'parents' => $entry['def']->parents,
                'properties' => $entry['properties'],
                'since' => $entry['since'],
                'deprecatedIn' => $entry['deprecatedIn'],
                'removedIn' => $entry['removedIn'],
            ];
        }
        $properties = [];
        foreach ($family['properties'] as $iri => $entry) {
            $properties[$iri] = [
                'name' => $entry['def']->name,
                'kind' => $entry['def']->kind->value,
                'ranges' => $entry['def']->ranges,
                'domains' => $entry['def']->domains,
                'since' => $entry['since'],
                'deprecatedIn' => $entry['deprecatedIn'],
                'removedIn' => $entry['removedIn'],
            ];
        }
        $individuals = [];
        foreach ($family['individuals'] as $iri => $entry) {
            $individuals[$iri] = [
                'name' => $entry['def']->name,
                'types' => $entry['def']->types,
                'since' => $entry['since'],
                'removedIn' => $entry['removedIn'],
            ];
        }

        $body = '    public const string NAMESPACE = ' . PhpWriter::string($this->termNamespace($family['namespace'])) . ";\n\n"
            . "    /** @var list<string> versions merged into this schema, oldest first */\n"
            . '    public const array VERSIONS = ' . PhpWriter::array($family['versions']) . ";\n\n"
            . "    /**\n     * Keyed by class IRI. `properties` maps the property IRIs this class restricts (its own, not inherited) to the version that attached them.\n     *\n"
            . "     * @var array<string, array{name: string, parents: list<string>, properties: array<string, string>, since: string, deprecatedIn: ?string, removedIn: ?string}>\n     */\n"
            . '    public const array CLASSES = ' . PhpWriter::array($classes) . ";\n\n"
            . "    /**\n     * Keyed by property IRI. `domains` is the declared domain(s); '*' means any class.\n     *\n"
            . "     * @var array<string, array{name: string, kind: 'object'|'datatype', ranges: list<string>, domains: list<string>, since: string, deprecatedIn: ?string, removedIn: ?string}>\n     */\n"
            . '    public const array PROPERTIES = ' . PhpWriter::array($properties) . ";\n\n"
            . "    /**\n     * Keyed by individual IRI.\n     *\n"
            . "     * @var array<string, array{name: string, types: list<string>, since: string, removedIn: ?string}>\n     */\n"
            . '    public const array INDIVIDUALS = ' . PhpWriter::array($individuals) . ";\n";

        return $this->writeClass($className, 'Structural facts about the ontology, for runtime validation. Read through Vocabulary, not directly.', $body);
    }

    /**
     * @param array{def: CodeListDef, since: string, codes: array<int|string, array{since: string, comment: ?string}>} $list
     */
    private function writeCodeList(array $list): string
    {
        $def = $list['def'];
        $used = [];
        $body = '    public const string IRI = ' . PhpWriter::string($def->iri) . ";\n\n"
            . "    /** Open lists accept codes beyond the published members (ISO currencies, UN/CEFACT units, ...). */\n"
            . '    public const bool OPEN = ' . ($def->open ? 'true' : 'false') . ";\n\n";
        /** @var list<array{string, string}> $all code and constant reference, in order */
        $all = [];
        foreach ($list['codes'] as $code => $info) {
            // PHP turns numeric string keys ("0", "10") into integers; codes are strings.
            $code = (string) $code;
            $name = PhpWriter::constantName($code);
            if (isset($used[$name])) {
                throw new RuntimeException(\sprintf('Constant name collision in code list %s: %s', $def->name, $name));
            }
            $used[$name] = true;
            $tags = $info['since'] !== $list['since'] ? ['@since ' . $info['since']] : [];
            $body .= PhpWriter::docblock($info['comment'], '    ', ...$tags);
            $body .= '    public const string ' . $name . ' = ' . PhpWriter::string($def->iri . '#' . $code) . ";\n\n";
            $all[] = [$code, 'self::' . $name];
        }
        $body .= "    /** @var array<string, string> code => IRI */\n    public const array ALL = [\n";
        foreach ($all as [$code, $ref]) {
            $body .= '        ' . PhpWriter::string($code) . ' => ' . $ref . ",\n";
        }
        $body .= "    ];\n";

        return $this->writeClass('CodeLists\\' . PhpWriter::constantName($def->name), 'Code list ' . $def->name . ($def->comment !== null ? ': ' . $def->comment : '') . '.', $body);
    }

    /**
     * @param array<string, array{def: CodeListDef, since: string, codes: array<int|string, array{since: string, comment: ?string}>}> $lists
     */
    private function writeCodeListSchema(array $lists): string
    {
        $data = [];
        foreach ($lists as $iri => $list) {
            $codes = [];
            foreach ($list['codes'] as $code => $info) {
                $codes[$code] = $info['since'];
            }
            $data[$iri] = ['name' => $list['def']->name, 'open' => $list['def']->open, 'since' => $list['since'], 'codes' => $codes];
        }
        $body = "    /**\n     * Keyed by code list IRI. `codes` maps each published code to the data model version that introduced it.\n     *\n"
            . "     * @var array<string, array{name: string, open: bool, since: string, codes: array<string, string>}>\n     */\n"
            . '    public const array LISTS = ' . PhpWriter::array($data) . ";\n";

        return $this->writeClass('CodeListSchema', 'Structural facts about the code lists, for runtime validation. Read through Vocabulary, not directly.', $body);
    }

    private function writeClass(string $relativeClass, string $description, string $body): string
    {
        $parts = explode('\\', $relativeClass);
        $short = array_pop($parts);
        $namespace = self::NAMESPACE . ($parts !== [] ? '\\' . implode('\\', $parts) : '');
        $path = $this->outputDir . '/' . str_replace('\\', '/', $relativeClass) . '.php';
        if (!is_dir(\dirname($path)) && !mkdir(\dirname($path), 0o775, true) && !is_dir(\dirname($path))) {
            throw new RuntimeException('Could not create ' . \dirname($path));
        }

        $content = "<?php\n\n"
            . "/*\n * GENERATED FILE. Do not edit: run `bin/generate-vocabulary` instead.\n *\n"
            . " * Derived from the IATA ONE Record ontologies (MIT License, (c) IATA), merged across\n"
            . " * the editions listed in Manifest::EDITIONS at their pinned commits.\n */\n\n"
            . "declare(strict_types=1);\n\n"
            . "namespace {$namespace};\n\n"
            . PhpWriter::docblock($description, '')
            . "final class {$short}\n{\n"
            . $body
            . "}\n";
        file_put_contents($path, $content);

        return $path;
    }

    private function termNamespace(string $ontologyIri): string
    {
        return match ($ontologyIri) {
            'https://onerecord.iata.org/ns/cargo' => 'https://onerecord.iata.org/ns/cargo#',
            'https://onerecord.iata.org/ns/api' => 'https://onerecord.iata.org/ns/api#',
            default => $ontologyIri . '#',
        };
    }

    private function resetOutputDir(): void
    {
        if (is_dir($this->outputDir)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->outputDir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $item) {
                /** @var SplFileInfo $item */
                $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
            }
        } elseif (!mkdir($this->outputDir, 0o775, true) && !is_dir($this->outputDir)) {
            throw new RuntimeException('Could not create ' . $this->outputDir);
        }
    }
}
