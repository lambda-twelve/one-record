<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

use RuntimeException;

/**
 * A token that must not be accepted. The reason is a stable code for logs and
 * metrics; the message never echoes token content.
 */
final class JwtException extends RuntimeException
{
    public const string MALFORMED = 'malformed';
    public const string UNSUPPORTED_ALGORITHM = 'unsupported_algorithm';
    public const string UNKNOWN_ISSUER = 'unknown_issuer';
    public const string BAD_SIGNATURE = 'bad_signature';
    public const string EXPIRED = 'expired';
    public const string NOT_YET_VALID = 'not_yet_valid';
    public const string AUDIENCE_MISMATCH = 'audience_mismatch';
    public const string MISSING_CLAIM = 'missing_claim';
    public const string INVALID_CLAIM = 'invalid_claim';

    public function __construct(public readonly string $reason, string $message)
    {
        parent::__construct($message);
    }
}
