<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Docs;

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every example included in the documentation site must run: a broken
 * example teaches a wrong API. Examples print to stdout, which is captured.
 */
#[CoversNothing]
final class ExamplesTest extends TestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function examples(): iterable
    {
        $files = glob(\dirname(__DIR__, 2) . '/docs/examples/*.php');
        foreach ($files === false ? [] : $files as $file) {
            yield basename($file) => [$file];
        }
    }

    #[DataProvider('examples')]
    public function testExampleRuns(string $file): void
    {
        ob_start();
        try {
            require $file;
        } finally {
            $output = (string) ob_get_clean();
        }
        self::assertNotSame('', $output, 'an example should show its result');
        self::assertStringNotContainsString('Fatal', $output);
    }
}
