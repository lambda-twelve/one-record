<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Change;

use LambdaTwelve\OneRecord\Api\Error;
use RuntimeException;

/**
 * A well-formed Change that cannot be applied: wrong revision, a value that
 * is not there to delete, a property the class does not accept. The errors
 * go on the ChangeRequest (api:hasError) with status REQUEST_FAILED, as the
 * spec's asynchronous error handling describes.
 */
final class ChangeRejected extends RuntimeException
{
    /**
     * @param non-empty-list<Error> $errors
     */
    public function __construct(public readonly array $errors)
    {
        parent::__construct(implode('; ', array_map(static fn(Error $e): string => $e->title . ($e->details !== [] && $e->details[0]->message !== null ? ': ' . $e->details[0]->message : ''), $errors)));
    }
}
