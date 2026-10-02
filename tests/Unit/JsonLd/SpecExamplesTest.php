<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\JsonLd;

use LambdaTwelve\OneRecord\JsonLd\Comparer;
use LambdaTwelve\OneRecord\JsonLd\JsonLd;
use LambdaTwelve\OneRecord\JsonLd\JsonLdException;
use LambdaTwelve\OneRecord\JsonLd\Writer;
use LambdaTwelve\OneRecord\Spec\Edition;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Every example document IATA publishes with the API specification must read,
 * expand, write and re-expand to an isomorphic graph. Documents IATA
 * published with defects are listed explicitly with the reason, so a fixed
 * upstream example shows up as a failing expectation here.
 */
#[CoversNothing]
final class SpecExamplesTest extends TestCase
{
    private const string FIXTURES = __DIR__ . '/../../Fixtures/spec';

    /** @var array<string, string> file => reason */
    private const array KNOWN_DEFECTS = [
        '2025-07/AuditTrail.json' => 'not JSON: contains a "...this contains a complete Change object..." placeholder',
        '2025-07/AuditTrail_example2.json' => 'not JSON: contains a placeholder',
        '2025-07/Piece_with_id.json' => '@id has a leading space',
        '2025-07/Piece_with_id.rev3.json' => '@id has a leading space',
        '2025-07/Shipment_with_Piece.embedded.json' => '@id has a leading space',
        '2026-07/Piece_with_id.json' => '@id has a leading space',
        '2026-07/Piece_with_id.rev3.json' => '@id has a leading space',
        '2026-07/Shipment_with_Piece.embedded.json' => '@id has a leading space',
        '2026-07/AuditTrail.json' => 'uses an undefined "_comment" key as a placeholder',
        '2026-07/AuditTrail_example2.json' => 'uses an undefined "_comment" key as a placeholder',
        '2025-07/ChangeRequest_with_error.json' => '"api#errors" is a typo for api:hasError (an undefined term)',
        '2025-07/Subscriptions_example2.json' => '"@type": "Subscription" is a bare term with no @vocab',
        '2026-07/Subscriptions_example2.json' => '"@type": "Subscription" is a bare term with no @vocab',
        '2025-07/VerificationRequest.json' => 'uses xsd:anyURI without declaring the xsd prefix',
        '2026-07/VerificationRequest.json' => 'uses xsd:anyURI without declaring the xsd prefix',
    ];

    /**
     * @return iterable<string, array{string}>
     */
    public static function examples(): iterable
    {
        foreach (Edition::cases() as $edition) {
            $files = glob(self::FIXTURES . '/' . $edition->value . '/*.json');
            foreach ($files === false ? [] : $files as $file) {
                $relative = $edition->value . '/' . basename($file);
                yield $relative => [$relative];
            }
        }
    }

    #[DataProvider('examples')]
    public function testExampleRoundTrips(string $relative): void
    {
        $json = (string) file_get_contents(self::FIXTURES . '/' . $relative);

        if (isset(self::KNOWN_DEFECTS[$relative])) {
            $this->expectException(JsonLdException::class);
            JsonLd::expand($json);

            return;
        }

        $document = JsonLd::expand($json);
        self::assertGreaterThan(0, \count($document->graph), 'an example without triples would be meaningless');

        $written = (new Writer())->write($document->graph, $document->root, $document->context);
        $again = JsonLd::expand($written);
        $diff = (new Comparer(blankNodePrefixes: []))->compare($document->graph, $again->graph);
        self::assertTrue($diff->isEqual(), $relative . "\n" . $diff->describe());
    }

    public function testEveryKnownDefectStillExists(): void
    {
        foreach (array_keys(self::KNOWN_DEFECTS) as $relative) {
            self::assertFileExists(self::FIXTURES . '/' . $relative);
        }
    }
}
