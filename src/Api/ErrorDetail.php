<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

/**
 * api:ErrorDetail: where and why. For synchronous HTTP errors the code is the
 * status code; for action requests it may be a business code (Cargo-XML).
 */
final readonly class ErrorDetail
{
    public function __construct(
        public ?string $code = null,
        public ?string $message = null,
        public ?string $property = null,
        public ?string $resource = null,
    ) {}
}
