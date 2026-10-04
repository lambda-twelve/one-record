<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use LambdaTwelve\OneRecord\Rdf\Graph;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Vocabulary\Vocabulary;
use Psr\Log\LoggerInterface;

/**
 * Deprecated terms are accepted, as the compatibility guide promises, and
 * their use is logged so a host can see what to migrate before an edition
 * removes them.
 */
final class Deprecations
{
    public static function log(Graph $graph, Iri $about, Vocabulary $vocabulary, LoggerInterface $logger): void
    {
        $seen = [];
        foreach ($graph as $triple) {
            $predicate = $triple->predicate->value;
            if (isset($seen[$predicate])) {
                continue;
            }
            $seen[$predicate] = true;
            $info = $vocabulary->property($predicate);
            if ($info?->deprecatedIn !== null) {
                $logger->notice(\sprintf('%s uses %s, deprecated in data model %s.', $about->value, $predicate, $info->deprecatedIn), ['object' => $about->value, 'property' => $predicate, 'deprecatedIn' => $info->deprecatedIn]);
            }
        }
    }
}
