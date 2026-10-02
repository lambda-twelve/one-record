<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

/**
 * api:Error: a title that stays the same for every occurrence of a problem,
 * plus details specific to this one. Used for HTTP error bodies, for the
 * errors recorded on failed action requests, and for verification requests.
 */
final readonly class Error
{
    /**
     * @param list<ErrorDetail> $details
     */
    public function __construct(
        public string $title,
        public array $details = [],
        public Severity $severity = Severity::Error,
    ) {}

    public static function of(string $title, ?string $code = null, ?string $message = null, ?string $property = null, ?string $resource = null): self
    {
        return new self($title, [new ErrorDetail($code, $message, $property, $resource)]);
    }
}
