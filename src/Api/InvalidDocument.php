<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use RuntimeException;

/**
 * A request body that is well-formed JSON-LD but not a valid instance of the
 * API class the endpoint expects (400 Bad Request).
 */
class InvalidDocument extends RuntimeException
{
    /**
     * @param list<Error> $errors
     */
    final public function __construct(string $message, public readonly array $errors = [])
    {
        parent::__construct($message);
    }

    public static function because(string $title, ?string $message = null, ?string $property = null): static
    {
        return new static($message === null ? $title : $title . ': ' . $message, [Error::of($title, '400', $message ?? $title, $property)]);
    }
}
