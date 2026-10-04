<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Spec;

use LogicException;

/**
 * A ONE Record API specification version this package can speak.
 *
 * The version travels in the media type parameter of Accept and Content-Type
 * (`application/ld+json; version=2.3.0`). The server negotiates it per request
 * and the client picks it per partner from their server information, so a 2.2
 * partner and a 2.3 partner are both served without configuration.
 */
enum ApiVersion: string
{
    case V2_2_0 = '2.2.0';
    case V2_3_0 = '2.3.0';

    public static function latest(): self
    {
        $cases = self::cases();

        return $cases[\count($cases) - 1];
    }

    /**
     * Highest first, the order server information advertises and negotiation prefers.
     *
     * @return non-empty-list<self>
     */
    public static function allDescending(): array
    {
        $cases = array_reverse(self::cases());
        \assert($cases !== []);

        return $cases;
    }

    /**
     * Lenient parsing for the media type parameter: "2.3", "2.3.0" and "v2.3.0"
     * all mean the same thing to a partner, and a version we do not know is null.
     */
    public static function tryFromString(string $version): ?self
    {
        $normalised = ltrim(trim($version), 'vV');
        if (preg_match('/^\d+\.\d+$/', $normalised) === 1) {
            $normalised .= '.0';
        }

        return self::tryFrom($normalised);
    }

    public function isAtLeast(self $other): bool
    {
        return version_compare($this->value, $other->value, '>=');
    }

    public function edition(): Edition
    {
        foreach (Edition::cases() as $edition) {
            if ($edition->apiVersion() === $this) {
                return $edition;
            }
        }

        throw new LogicException("No edition declares API version {$this->value}.");
    }

    /**
     * The owl:versionIRI of the API ontology at this version.
     */
    public function ontologyVersionIri(): string
    {
        return Namespaces::API_ONTOLOGY . '/' . $this->value;
    }
}
