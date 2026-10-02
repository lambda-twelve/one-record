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
 * The SDK must stay usable from any host, so nothing under src/ may depend on a
 * framework, a storage layer or any other third-party code. Only the package's
 * own namespace, PSR interfaces and PHP itself are allowed. Wrappers
 * (one-record-laravel, one-record-drupal, ...) live in their own packages.
 *
 * @implements Rule<Name>
 */
final class SdkBoundaryRule implements Rule
{
    private const array ALLOWED_PREFIXES = ['LambdaTwelve\\OneRecord\\', 'Psr\\'];

    public function getNodeType(): string
    {
        return Name::class;
    }

    /**
     * @return list<IdentifierRuleError>
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (!str_contains(str_replace('\\', '/', $scope->getFile()), '/src/')) {
            return [];
        }

        $name = $node->toString();
        // Unqualified names are PHP's own classes (Throwable, DateTimeImmutable, ...).
        if (!str_contains($name, '\\') && !$node->isFullyQualified()) {
            return [];
        }
        $resolved = $scope->resolveName($node);
        if (!str_contains($resolved, '\\')) {
            return [];
        }
        foreach (self::ALLOWED_PREFIXES as $prefix) {
            if (str_starts_with($resolved, $prefix)) {
                return [];
            }
        }

        return [
            RuleErrorBuilder::message(\sprintf(
                'src/ may only depend on LambdaTwelve\OneRecord, PSR interfaces and PHP itself; found %s. Framework or storage specifics belong in a wrapper package.',
                $resolved,
            ))->identifier('oneRecord.sdkBoundary')->build(),
        ];
    }
}
