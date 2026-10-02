<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Api\Error;
use RuntimeException;

/**
 * A Change document that cannot be understood (400 Bad Request at the API).
 */
final class ChangeException extends RuntimeException
{
    /**
     * @param list<Error> $errors
     */
    public function __construct(string $message, public readonly array $errors = [])
    {
        parent::__construct($message);
    }

    public static function because(string $title, ?string $message = null, ?string $property = null): self
    {
        return new self($message === null ? $title : $title . ': ' . $message, [Error::of($title, '400', $message ?? $title, $property)]);
    }
}
