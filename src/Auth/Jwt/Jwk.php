<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Auth\Jwt;

use InvalidArgumentException;

/**
 * Converts an RSA JSON Web Key (RFC 7517, kty "RSA" with n and e) to the PEM
 * SubjectPublicKeyInfo that OpenSSL verifies with. Done by hand because the
 * DER involved is small and a dependency for it would be the only one.
 */
final class Jwk
{
    private const string RSA_ENCRYPTION_OID = "\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01";

    private function __construct() {}

    /**
     * @param array<string, mixed> $jwk
     */
    public static function rsaToPem(array $jwk): string
    {
        if (($jwk['kty'] ?? null) !== 'RSA' || !\is_string($jwk['n'] ?? null) || !\is_string($jwk['e'] ?? null)) {
            throw new InvalidArgumentException('Not an RSA JWK with n and e.');
        }
        $modulus = self::base64UrlDecode($jwk['n']);
        $exponent = self::base64UrlDecode($jwk['e']);
        if ($modulus === '' || $exponent === '') {
            throw new InvalidArgumentException('Malformed RSA JWK.');
        }

        $rsaPublicKey = self::sequence(self::integer($modulus) . self::integer($exponent));
        $algorithm = self::sequence(self::RSA_ENCRYPTION_OID . "\x05\x00");
        $subjectPublicKeyInfo = self::sequence($algorithm . self::bitString($rsaPublicKey));

        return "-----BEGIN PUBLIC KEY-----\n" . chunk_split(base64_encode($subjectPublicKeyInfo), 64, "\n") . "-----END PUBLIC KEY-----\n";
    }

    public static function base64UrlDecode(string $data): string
    {
        $decoded = base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - \strlen($data) % 4) % 4), true);

        return $decoded === false ? '' : $decoded;
    }

    public static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function integer(string $bytes): string
    {
        // DER integers are signed: a leading 1 bit needs a zero byte so the value stays positive.
        if ((\ord($bytes[0]) & 0x80) !== 0) {
            $bytes = "\x00" . $bytes;
        }

        return "\x02" . self::length(\strlen($bytes)) . $bytes;
    }

    private static function sequence(string $content): string
    {
        return "\x30" . self::length(\strlen($content)) . $content;
    }

    private static function bitString(string $content): string
    {
        return "\x03" . self::length(\strlen($content) + 1) . "\x00" . $content;
    }

    private static function length(int $length): string
    {
        if ($length < 0x80) {
            return \chr($length);
        }
        $bytes = ltrim(pack('N', $length), "\x00");

        return \chr(0x80 | \strlen($bytes)) . $bytes;
    }
}
