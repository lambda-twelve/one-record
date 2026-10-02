<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use InvalidArgumentException;

/**
 * The two UUID flavours the package needs: version 5 (name-based, so the same
 * input always yields the same id) and version 4 (random, from an injectable
 * byte source so tests stay deterministic).
 */
final class Uuid
{
    /** RFC 4122 namespace for URLs; our names are IRIs. */
    public const string NAMESPACE_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';

    private function __construct() {}

    public static function v5(string $namespace, string $name): string
    {
        $hash = sha1(self::bytes($namespace) . $name);

        return self::format(substr($hash, 0, 32), 5);
    }

    /**
     * @param callable(int): string $randomBytes
     */
    public static function v4(callable $randomBytes): string
    {
        return self::format(bin2hex($randomBytes(16)), 4);
    }

    public static function isValid(string $value): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-8][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $value) === 1;
    }

    private static function format(string $hex, int $version): string
    {
        $hex = substr($hex, 0, 12) . dechex($version) . substr($hex, 13, 3)
            . dechex((hexdec($hex[16]) & 0x3) | 0x8) . substr($hex, 17, 15);

        return \sprintf('%s-%s-%s-%s-%s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20, 12));
    }

    private static function bytes(string $uuid): string
    {
        $hex = str_replace('-', '', $uuid);
        if (\strlen($hex) !== 32 || !ctype_xdigit($hex)) {
            throw new InvalidArgumentException(\sprintf('Not a UUID: "%s".', $uuid));
        }

        return (string) hex2bin($hex);
    }
}
