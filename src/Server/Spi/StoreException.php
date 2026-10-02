<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Rdf\Iri;
use RuntimeException;

/**
 * The two conditions a store must detect itself, because only it can do so
 * atomically: creating an object that exists (409) and saving a revision
 * against a stale current revision.
 */
final class StoreException extends RuntimeException
{
    public const string ALREADY_EXISTS = 'already_exists';
    public const string REVISION_CONFLICT = 'revision_conflict';
    public const string NOT_FOUND = 'not_found';
    public const string STATUS_CONFLICT = 'status_conflict';

    public function __construct(public readonly string $kind, string $message)
    {
        parent::__construct($message);
    }

    public static function alreadyExists(Iri $iri): self
    {
        return new self(self::ALREADY_EXISTS, \sprintf('"%s" already exists.', $iri->value));
    }

    public static function revisionConflict(Iri $iri, int $expected, int $actual): self
    {
        return new self(self::REVISION_CONFLICT, \sprintf('"%s" is at revision %d, not %d.', $iri->value, $actual, $expected));
    }

    public static function statusConflict(Iri $iri, string $expected, string $actual): self
    {
        return new self(self::STATUS_CONFLICT, \sprintf('"%s" is %s, not %s.', $iri->value, $actual, $expected));
    }

    public static function notFound(Iri $iri): self
    {
        return new self(self::NOT_FOUND, \sprintf('"%s" does not exist.', $iri->value));
    }
}
