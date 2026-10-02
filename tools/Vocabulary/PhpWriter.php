<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Tools\Vocabulary;

use InvalidArgumentException;

/**
 * Small helpers for emitting deterministic, readable PHP.
 */
final class PhpWriter
{
    private function __construct() {}

    public static function string(string $value): string
    {
        return var_export($value, true);
    }

    /**
     * A PHP constant name for an ontology local name. Names are kept exactly
     * when PHP allows them (PHP identifiers may contain any UTF-8 byte above
     * 0x7F, so "Löwchen" survives); a leading digit gets an underscore, other
     * illegal characters become underscores, and the one reserved word PHP
     * forbids as a constant name ("class") gets a suffix.
     */
    public static function constantName(string $localName): string
    {
        $name = preg_replace('/[^A-Za-z0-9_\x80-\xff]/', '_', $localName) ?? $localName;
        if (preg_match('/^[0-9]/', $name) === 1) {
            $name = '_' . $name;
        }
        if (strtolower($name) === 'class') {
            $name .= '_';
        }

        return $name;
    }

    /**
     * A docblock from an ontology comment: one paragraph, closing sequences neutralised.
     */
    public static function docblock(?string $comment, string $indent = '    ', string ...$tags): string
    {
        $lines = [];
        if ($comment !== null && $comment !== '') {
            $text = str_replace('*/', '* /', (string) preg_replace('/\s+/', ' ', trim($comment)));
            foreach (self::wrap($text, 100) as $line) {
                $lines[] = $line;
            }
        }
        if ($tags !== []) {
            if ($lines !== []) {
                $lines[] = '';
            }
            foreach ($tags as $tag) {
                $lines[] = $tag;
            }
        }
        if ($lines === []) {
            return '';
        }
        if (\count($lines) === 1) {
            return $indent . '/** ' . $lines[0] . " */\n";
        }
        $out = $indent . "/**\n";
        foreach ($lines as $line) {
            $out .= $indent . ' *' . ($line === '' ? '' : ' ' . $line) . "\n";
        }

        return $out . $indent . " */\n";
    }

    /**
     * @param array<array-key, mixed> $value
     */
    public static function array(array $value, int $depth = 1): string
    {
        if ($value === []) {
            return '[]';
        }
        $indent = str_repeat('    ', $depth);
        $isList = array_is_list($value);
        $out = "[\n";
        foreach ($value as $key => $item) {
            $out .= $indent . '    ';
            if (!$isList) {
                // Always quote keys: numeric codes ("0", "10") stay visibly strings in the source.
                $out .= self::string((string) $key) . ' => ';
            }
            $out .= self::value($item, $depth + 1) . ",\n";
        }

        return $out . $indent . ']';
    }

    public static function value(mixed $value, int $depth): string
    {
        return match (true) {
            \is_array($value) => self::array($value, $depth),
            \is_string($value) => self::string($value),
            \is_bool($value) => $value ? 'true' : 'false',
            $value === null => 'null',
            \is_int($value) => (string) $value,
            default => throw new InvalidArgumentException('Unsupported value type ' . get_debug_type($value)),
        };
    }

    /**
     * @return list<string>
     */
    private static function wrap(string $text, int $width): array
    {
        $lines = [];
        $current = '';
        foreach (explode(' ', $text) as $word) {
            if ($current !== '' && \strlen($current) + 1 + \strlen($word) > $width) {
                $lines[] = $current;
                $current = $word;
            } else {
                $current = $current === '' ? $word : $current . ' ' . $word;
            }
        }
        if ($current !== '') {
            $lines[] = $current;
        }

        return $lines;
    }
}
