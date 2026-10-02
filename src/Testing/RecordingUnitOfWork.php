<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Testing;

use LambdaTwelve\OneRecord\Server\Spi\UnitOfWork;
use Throwable;

/**
 * Counts units and tracks nesting, the way a host's transaction manager
 * would with a depth counter: only the outermost run() is a "transaction".
 */
final class RecordingUnitOfWork implements UnitOfWork
{
    public int $transactions = 0;
    public int $rolledBack = 0;
    public int $maxDepth = 0;
    private int $depth = 0;

    public function run(callable $work): mixed
    {
        if ($this->depth === 0) {
            $this->transactions++;
        }
        $this->depth++;
        $this->maxDepth = max($this->maxDepth, $this->depth);
        try {
            return $work();
        } catch (Throwable $e) {
            if ($this->depth === 1) {
                $this->rolledBack++;
            }
            throw $e;
        } finally {
            $this->depth--;
        }
    }
}
