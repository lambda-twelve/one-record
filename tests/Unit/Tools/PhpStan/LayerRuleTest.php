<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Tools\PhpStan;

use LambdaTwelve\OneRecord\Tools\PhpStan\LayerRule;
use PHPStan\Rules\Rule;
use PHPStan\Testing\RuleTestCase;

/**
 * The layer rule must catch each kind of forbidden dependency and stay quiet
 * on the allowed ones, including the declared Change/Api pair. The fixtures
 * live under a directory named src so the rule treats them as package code.
 *
 * @extends RuleTestCase<LayerRule>
 */
final class LayerRuleTest extends RuleTestCase
{
    private const string FIXTURES = __DIR__ . '/../../../Fixtures/Layers/src/';

    protected function getRule(): Rule
    {
        return new LayerRule('Fixtures/Layers/src');
    }

    public function testModelMayNotDependOnApi(): void
    {
        $errors = $this->gatherErrors(self::FIXTURES . 'Model/UpwardDependency.php');
        self::assertNotSame([], $errors);
        foreach ($errors as $error) {
            self::assertSame('Layer Model may not depend on Api (found LambdaTwelve\OneRecord\Api\Error). Allowed: Spec, Rdf, Vocabulary, JsonLd. See docs/sdk-boundary.md.', $error);
        }
    }

    public function testAuthMayUseTheSpiOnly(): void
    {
        $errors = $this->gatherErrors(self::FIXTURES . 'Auth/ServerInternals.php');
        self::assertNotSame([], $errors);
        foreach ($errors as $error) {
            self::assertSame('Layer Auth may not depend on Server (found LambdaTwelve\OneRecord\Server\ServerConfig). Allowed: Spec, Rdf, Server\Spi. See docs/sdk-boundary.md.', $error);
        }
    }

    public function testTheSpiNeverImportsEndpoints(): void
    {
        $errors = $this->gatherErrors(self::FIXTURES . 'Server/Spi/EndpointLeak.php');
        self::assertNotSame([], $errors);
        foreach ($errors as $error) {
            self::assertStringStartsWith('Layer Server\Spi may not depend on Server (found LambdaTwelve\OneRecord\Server\Endpoint\SubscriptionsEndpoint)', $error);
        }
    }

    public function testInMemoryMayUseTheWiringButNoEndpoint(): void
    {
        $errors = $this->gatherErrors(self::FIXTURES . 'Server/InMemory/EndpointLeak.php');
        self::assertNotSame([], $errors);
        foreach ($errors as $error) {
            self::assertStringStartsWith('Layer Server\InMemory may not depend on Server (found LambdaTwelve\OneRecord\Server\Endpoint\LogisticsObjectEndpoint)', $error);
        }
    }

    public function testTheClientIsBarredFromTheWholeServer(): void
    {
        $errors = $this->gatherErrors(self::FIXTURES . 'Client/ServerReach.php');
        self::assertNotSame([], $errors);
        foreach ($errors as $error) {
            self::assertStringStartsWith('Layer Client may not depend on Server\Spi (found LambdaTwelve\OneRecord\Server\Spi\LogisticsObjectStore)', $error);
        }
    }

    public function testTheDeclaredChangeApiPairIsAllowed(): void
    {
        $this->analyse([self::FIXTURES . 'Change/DeclaredPair.php'], []);
    }

    public function testEveryLayerListsOnlyKnownLayersOrClasses(): void
    {
        foreach (LayerRule::LAYERS as $layer => $allowed) {
            foreach ($allowed as $entry) {
                self::assertNotNull(LayerRule::layerOf($entry), \sprintf('%s allows "%s", which is in no layer', $layer, $entry));
                self::assertNotSame($layer, LayerRule::layerOf($entry), \sprintf('%s lists itself', $layer));
            }
        }
        self::assertSame('Server\Spi', LayerRule::layerOf('Server\Spi\Agent'), 'longest prefix wins');
        self::assertSame('Server', LayerRule::layerOf('Server\Endpoint\Endpoint'));
        self::assertSame('Server', LayerRule::layerOf('Server\Services'));
        self::assertNull(LayerRule::layerOf('Tools\PhpStan\LayerRule'));
    }

    /**
     * @return list<string>
     */
    private function gatherErrors(string $file): array
    {
        $messages = [];
        foreach ($this->gatherAnalyserErrors([$file]) as $error) {
            $messages[] = $error->getMessage();
        }

        return $messages;
    }
}
