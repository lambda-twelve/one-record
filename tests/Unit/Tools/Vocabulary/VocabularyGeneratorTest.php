<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tests\Unit\Tools\Vocabulary;

use LambdaTwelve\OneRecord\Spec\Edition;
use LambdaTwelve\OneRecord\Tools\Vocabulary\DirectorySource;
use LambdaTwelve\OneRecord\Tools\Vocabulary\PhpWriter;
use LambdaTwelve\OneRecord\Tools\Vocabulary\VocabularyGenerator;
use PhpToken;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\TestCase;

/**
 * Generates from the miniature two-edition ontologies in tests/Fixtures and
 * checks the version stamps: that is the mechanism that lets one vocabulary
 * serve partners on different data model versions.
 */
#[CoversClass(VocabularyGenerator::class)]
#[CoversClass(PhpWriter::class)]
#[CoversClass(DirectorySource::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\OntologyFile::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle\TurtleParser::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\OntologyReader::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\OntologyModel::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\ClassDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\PropertyDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\IndividualDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Tools\Vocabulary\Ontology\CodeListDef::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Graph::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Iri::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\BlankNode::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Literal::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Rdf\Triple::class)]
#[UsesClass(Edition::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Spec\ApiVersion::class)]
#[UsesClass(\LambdaTwelve\OneRecord\Spec\DataModelVersion::class)]
final class VocabularyGeneratorTest extends TestCase
{
    private string $output;

    /** @var array<string, string> relative path => content */
    private array $files = [];

    protected function setUp(): void
    {
        $this->output = sys_get_temp_dir() . '/one-record-vocab-' . bin2hex(random_bytes(4));
        $generator = new VocabularyGenerator(new DirectorySource(__DIR__ . '/../../../Fixtures/ontologies'), $this->output);
        foreach ($generator->generate([Edition::E2025_07, Edition::E2026_07]) as $path) {
            $this->files[str_replace($this->output . '/', '', $path)] = (string) file_get_contents($path);
        }
    }

    protected function tearDown(): void
    {
        foreach (array_keys($this->files) as $relative) {
            unlink($this->output . '/' . $relative);
        }
        @rmdir($this->output . '/CodeLists');
        @rmdir($this->output);
    }

    public function testWritesOneFilePerArtefactAndValidPhp(): void
    {
        self::assertSame(
            ['Api.php', 'ApiSchema.php', 'Cargo.php', 'CargoSchema.php', 'CodeListSchema.php', 'CodeLists/DensityGroupCode.php', 'CodeLists/MeasurementUnitCode.php', 'CodeLists/WeightUnitCode.php', 'Manifest.php'],
            array_keys($this->files),
        );
        foreach ($this->files as $relative => $content) {
            self::assertStringStartsWith("<?php\n\n/*\n * GENERATED FILE.", $content, $relative);
            self::assertStringContainsString('declare(strict_types=1);', $content, $relative);
            $this->assertCompiles($relative, $content);
        }
    }

    public function testStampsTermsWithTheVersionsThatIntroducedDeprecatedOrRemovedThem(): void
    {
        $cargo = $this->files['Cargo.php'];
        self::assertStringContainsString("@since 3.3\n     */\n    public const string StatusUpdateEvent", $cargo);
        self::assertStringContainsString("@deprecated since 3.3 */\n    public const string coload", $cargo);
        self::assertStringContainsString("@deprecated removed in 3.3 */\n    public const string legacyField", $cargo);
        self::assertStringContainsString("/** Weight details */\n    public const string grossWeight", $cargo);
        self::assertStringNotContainsString('@since 3.2', $cargo, 'terms from the first edition carry no @since');

        $schema = $this->files['CargoSchema.php'];
        self::assertStringContainsString("'name' => 'legacyField',", $schema);
        self::assertMatchesRegularExpression("/'name' => 'legacyField',\n(?:.*\n){1,12}?\\s*'deprecatedIn' => null,\n\\s*'removedIn' => '3.3',/", $schema);
        self::assertMatchesRegularExpression("/'name' => 'OldThing',\n(?:.*\n){2,8}?\\s*'deprecatedIn' => '3.2',\n\\s*'removedIn' => null,/", $schema, 'deprecation recorded in the first version that flagged it, even if later undone');
        self::assertStringContainsString("public const array VERSIONS = [\n        '3.2',\n        '3.3',\n    ];", $schema);
        self::assertMatchesRegularExpression("/'https:\\/\\/onerecord.iata.org\\/ns\\/cargo#otherIdentifiers' => '3.3',/", $schema, 'a property attached to Piece in 3.3 is stamped 3.3 on the class');
        self::assertMatchesRegularExpression("/'https:\\/\\/onerecord.iata.org\\/ns\\/cargo#grossWeight' => '3.2',/", $schema);
    }

    public function testMergesCodeListsAndNamesConstantsSafely(): void
    {
        $units = $this->files['CodeLists/MeasurementUnitCode.php'];
        self::assertStringContainsString('public const bool OPEN = true;', $units);
        self::assertStringContainsString("/** Kilogram */\n    public const string KGM = 'https://onerecord.iata.org/ns/code-lists/MeasurementUnitCode#KGM';", $units);
        self::assertStringContainsString("     * Metre\n     *\n     * @since 3.3\n     */\n    public const string MTR", $units, 'a code added in a later edition is stamped');
        self::assertStringContainsString("'KGM' => self::KGM,", $units);

        $density = $this->files['CodeLists/DensityGroupCode.php'];
        self::assertStringContainsString('public const bool OPEN = false;', $density);
        self::assertStringContainsString("public const string _0 = 'https://onerecord.iata.org/ns/code-lists/DensityGroupCode#0';", $density);
        self::assertStringContainsString("'10' => self::_10,", $density);

        self::assertStringContainsString("'codes' => [\n                '0' => '3.2',\n                '10' => '3.2',\n            ],", $this->files['CodeListSchema.php']);
    }

    public function testRecordsProvenanceInTheManifest(): void
    {
        $manifest = $this->files['Manifest.php'];
        self::assertStringContainsString("'2025-07' => [\n            'ontologyCommit' => '" . Edition::E2025_07->ontologyCommit() . "',\n            'documentationCommit' => '" . Edition::E2025_07->documentationCommit() . "',", $manifest);
        self::assertStringContainsString("'cargo' => '3.2',", $manifest);
        self::assertStringContainsString("'cargo' => '3.3.0',", $manifest);
        self::assertStringContainsString("public const string LATEST_EDITION = '2026-07';", $manifest);
        self::assertStringContainsString('/2025-07-standard/Data-Model/IATA-1R-DM-Ontology.ttl', $manifest);
    }

    public function testApiFamilyIsStampedWithApiVersions(): void
    {
        $api = $this->files['Api.php'];
        self::assertStringContainsString("@since 2.3.0 */\n    public const string Severity", $api);
        self::assertStringContainsString("@since 2.3.0 */\n    public const string WARNING", $api);
        self::assertStringContainsString("public const array VERSIONS = [\n        '2.2.0',\n        '2.3.0',\n    ];", $this->files['ApiSchema.php']);
        self::assertStringContainsString("'domains' => [\n                'https://onerecord.iata.org/ns/api#Change',\n                'https://onerecord.iata.org/ns/api#Subscription',\n            ],", $this->files['ApiSchema.php']);
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function constantNames(): iterable
    {
        yield 'plain' => ['KGM', 'KGM'];
        yield 'leading digit' => ['10', '_10'];
        yield 'dash and dot' => ['a-b.c', 'a_b_c'];
        yield 'utf-8 kept' => ['LIVE_MMLS_DOGS_Löwchen', 'LIVE_MMLS_DOGS_Löwchen'];
        yield 'reserved word' => ['class', 'class_'];
    }

    #[DataProvider('constantNames')]
    public function testConstantNaming(string $local, string $expected): void
    {
        self::assertSame($expected, PhpWriter::constantName($local));
    }

    private function assertCompiles(string $relative, string $content): void
    {
        $tokens = PhpToken::tokenize($content);
        self::assertNotEmpty($tokens, $relative);
        // php -l catches what tokenizing cannot (unbalanced braces, bad constant names).
        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $process = proc_open([PHP_BINARY, '-l'], $descriptors, $pipes);
        self::assertIsResource($process);
        fwrite($pipes[0], $content);
        fclose($pipes[0]);
        $stdout = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        self::assertSame(0, proc_close($process), $relative . ': ' . $stdout . $stderr);
    }
}
