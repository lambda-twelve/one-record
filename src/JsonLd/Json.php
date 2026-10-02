<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\JsonLd;

use JsonException;

/**
 * JSON decoding and encoding with the settings every ONE Record body needs:
 * objects as associative arrays, big integers kept exact, UTF-8 and slashes
 * left readable on output, and a depth bound so a hostile body cannot
 * exhaust the stack.
 */
final class Json
{
    public const int MAX_DEPTH = 64;

    private function __construct() {}

    /**
     * @return array<string, mixed>
     */
    public static function decodeObject(string $json): array
    {
        try {
            $decoded = json_decode($json, true, self::MAX_DEPTH, JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        } catch (JsonException $e) {
            throw JsonLdException::invalidJson($e->getMessage());
        }
        if (!\is_array($decoded) || array_is_list($decoded)) {
            throw JsonLdException::at('', 'A JSON-LD document must be a single JSON object');
        }

        /** @var array<string, mixed> $decoded */
        return $decoded;
    }

    /**
     * @param array<string, mixed> $document
     */
    public static function encode(array $document, bool $pretty = true): string
    {
        $flags = JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION;
        if ($pretty) {
            $flags |= JSON_PRETTY_PRINT;
        }

        return json_encode($document, $flags, self::MAX_DEPTH);
    }
}
