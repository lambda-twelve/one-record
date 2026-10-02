<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use RuntimeException;

/**
 * A document is outside the JSON-LD subset ONE Record uses, or malformed.
 *
 * The subset is deliberate (see docs/guide/json-ld.md): anything this package
 * cannot represent faithfully is refused with a message naming the construct
 * and where it was found, instead of being silently misread.
 */
final class JsonLdException extends RuntimeException
{
    public static function at(string $path, string $message): self
    {
        return new self(\sprintf('%s at %s', $message, $path === '' ? 'the document root' : $path));
    }

    public static function unsupported(string $path, string $keyword): self
    {
        return self::at($path, \sprintf('%s is outside the JSON-LD subset this package supports', $keyword));
    }

    public static function invalidJson(string $reason): self
    {
        return new self('The document is not valid JSON: ' . $reason);
    }
}
