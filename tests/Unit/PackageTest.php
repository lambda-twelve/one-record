<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;

/**
 * Smoke test for the package skeleton: the Composer manifest stays what the
 * repository documents (name, licence, PHP floor, PSR-only runtime).
 */
#[CoversNothing]
final class PackageTest extends TestCase
{
    public function testComposerManifestIsConsistent(): void
    {
        $json = file_get_contents(\dirname(__DIR__, 2) . '/composer.json');
        self::assertNotFalse($json);
        /** @var array{name: string, license: string, require: array<string, string>} $manifest */
        $manifest = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        self::assertSame('lambda-twelve/one-record', $manifest['name']);
        self::assertSame('Apache-2.0', $manifest['license']);
        self::assertSame('>=8.3', $manifest['require']['php']);

        foreach (array_keys($manifest['require']) as $package) {
            self::assertTrue(
                $package === 'php' || str_starts_with($package, 'ext-') || str_starts_with($package, 'psr/'),
                "Runtime dependency {$package} is not a PSR interface; the SDK must stay framework-free.",
            );
        }
    }
}
