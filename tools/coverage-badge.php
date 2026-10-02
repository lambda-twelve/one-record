<?php

declare(strict_types=1);

/*
 * Turns a Clover coverage report into a shields.io endpoint document, so the
 * README badge can read the number from the published documentation site
 * instead of a third-party coverage service.
 *
 *   php tools/coverage-badge.php coverage.xml site/coverage.json
 */

if ($argc !== 3) {
    fwrite(STDERR, "Usage: coverage-badge.php <clover.xml> <output.json>\n");
    exit(64);
}
$xml = simplexml_load_file($argv[1]);
if ($xml === false) {
    fwrite(STDERR, "Cannot read {$argv[1]}\n");
    exit(1);
}
$found = $xml->xpath('/coverage/project/metrics');
$metrics = is_array($found) ? ($found[0] ?? null) : null;
if (!$metrics instanceof SimpleXMLElement) {
    fwrite(STDERR, "No project metrics in {$argv[1]}\n");
    exit(1);
}
$statements = (int) $metrics['statements'];
$covered = (int) $metrics['coveredstatements'];
$percent = $statements === 0 ? 0.0 : round($covered / $statements * 100, 1);
$color = match (true) {
    $percent >= 90 => 'brightgreen',
    $percent >= 80 => 'green',
    $percent >= 70 => 'yellowgreen',
    $percent >= 60 => 'yellow',
    default => 'orange',
};
$document = ['schemaVersion' => 1, 'label' => 'coverage', 'message' => $percent . '%', 'color' => $color];
file_put_contents($argv[2], json_encode($document, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . "\n");
fwrite(STDOUT, sprintf("Statement coverage: %s%% (%d of %d)\n", $percent, $covered, $statements));
