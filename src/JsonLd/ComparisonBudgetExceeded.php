<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use RuntimeException;

/**
 * The graphs are too symmetric to canonicalise within the comparer's budget.
 * Thrown instead of guessing: a wrong "not isomorphic" would be a false
 * change, a wrong "isomorphic" a missed one.
 */
final class ComparisonBudgetExceeded extends RuntimeException {}
