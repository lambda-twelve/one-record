<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\PhpStan;

use PhpParser\Node;
use PhpParser\Node\Name;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\IdentifierRuleError;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * The layering inside src/, as one table: each layer (a namespace under
 * LambdaTwelve\OneRecord) lists the layers and classes it may depend on.
 * Everything else is a violation. The table is the architecture document;
 * change it deliberately, with a note in docs/sdk-boundary.md.
 *
 * Keys and entries are relative to the package namespace. A key is a
 * namespace prefix and the longest match wins, so `Server\Spi` is its own
 * layer while `Server\Endpoint` falls under `Server`. An entry is a namespace
 * prefix or a single class (`Server\Services`), which is how the few precise
 * exceptions are written without special cases in the code.
 *
 * @implements Rule<Name>
 */
final class LayerRule implements Rule
{
    private const string PACKAGE = 'LambdaTwelve\\OneRecord\\';

    /** The lower layers every document and server layer may use. */
    private const array FOUNDATION = ['Spec', 'Rdf', 'Vocabulary', 'JsonLd'];

    /** The documents of the protocol: model objects, changes and API documents. */
    private const array DOCUMENTS = [...self::FOUNDATION, 'Model', 'Change', 'Api'];

    /**
     * @var array<string, list<string>>
     */
    public const array LAYERS = [
        'Spec' => [],
        'Rdf' => ['Spec'],
        'Vocabulary' => ['Spec', 'Rdf'],
        'JsonLd' => ['Spec', 'Rdf', 'Vocabulary'],
        'Model' => self::FOUNDATION,
        // api:Change is itself an API document, so Change and Api depend on each other by design:
        // an ActionRequest carries a Change; a Change rejection carries api:Error documents.
        'Change' => [...self::FOUNDATION, 'Model', 'Api'],
        'Api' => [...self::FOUNDATION, 'Model', 'Change'],
        // Auth implements the server's Authenticator SPI and nothing else of the server.
        'Auth' => ['Spec', 'Rdf', 'Server\\Spi'],
        // The SPI is what hosts implement: documents in, documents out, no server internals.
        'Server\\Spi' => self::DOCUMENTS,
        'Server\\Event' => [...self::DOCUMENTS, 'Server\\Spi'],
        // Reference implementations of the SPI plus the wiring to build a whole server; never endpoint code.
        'Server\\InMemory' => [...self::DOCUMENTS, 'Server\\Spi', 'Server\\Event', 'Server\\ServerConfig', 'Server\\Services', 'Server\\ServerBuilder', 'Server\\OneRecordServer', 'Server\\SystemClock', 'Server\\GrantAccessPolicy'],
        // The server proper: routing, HTTP, endpoints, lifecycle, fan-out. It may use everything below it.
        'Server' => [...self::DOCUMENTS, 'Server\\Spi', 'Server\\Event'],
        // The client talks to other servers; it must never reach into ours.
        'Client' => [...self::DOCUMENTS, 'Auth'],
    ];

    public function __construct(private readonly string $sourceDirectory = 'src') {}

    public function getNodeType(): string
    {
        return Name::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!str_contains(str_replace('\\', '/', $scope->getFile()), '/' . trim($this->sourceDirectory, '/') . '/')) {
            return [];
        }
        $from = $scope->getNamespace();
        if ($from === null || !str_starts_with($from . '\\', self::PACKAGE)) {
            return [];
        }
        $resolved = $scope->resolveName($node);
        if (!str_starts_with($resolved, self::PACKAGE)) {
            return [];
        }
        $fromLayer = self::layerOf(substr($from, \strlen(self::PACKAGE)));
        $target = substr($resolved, \strlen(self::PACKAGE));
        $toLayer = self::layerOf($target);
        if ($fromLayer === null || $toLayer === null || $fromLayer === $toLayer) {
            return [];
        }
        foreach (self::LAYERS[$fromLayer] as $allowed) {
            if ($target === $allowed || str_starts_with($target, $allowed . '\\')) {
                return [];
            }
        }

        return [
            RuleErrorBuilder::message(\sprintf(
                'Layer %s may not depend on %s (found %s). Allowed: %s. See docs/sdk-boundary.md.',
                $fromLayer,
                $toLayer,
                $resolved,
                self::LAYERS[$fromLayer] === [] ? 'nothing' : implode(', ', self::LAYERS[$fromLayer]),
            ))->identifier('oneRecord.layer')->build(),
        ];
    }

    /**
     * The layer a package-relative name belongs to: the longest table key that
     * is a namespace prefix of it. Null for names outside every layer.
     */
    public static function layerOf(string $relativeName): ?string
    {
        $best = null;
        foreach (array_keys(self::LAYERS) as $layer) {
            if (($relativeName === $layer || str_starts_with($relativeName, $layer . '\\')) && ($best === null || \strlen($layer) > \strlen($best))) {
                $best = $layer;
            }
        }

        return $best;
    }
}
