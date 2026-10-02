<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary\Turtle;

use RuntimeException;

final class TurtleSyntaxError extends RuntimeException
{
    public static function at(string $message, int $line, int $column): self
    {
        return new self(\sprintf('%s (line %d, column %d)', $message, $line, $column));
    }
}
