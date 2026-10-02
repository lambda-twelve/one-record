<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Spec;

use LogicException;

/**
 * A version of the ONE Record cargo ontology (the data model).
 *
 * The API is independent of the data model version by specification: terms are
 * never versioned in payloads, and a server validates against the newest
 * ontology it knows. The version still matters when publishing to a partner
 * that runs an older ontology, so vocabulary terms record the version they
 * appeared in and builders can be given a ceiling.
 */
enum DataModelVersion: string
{
    case V3_2 = '3.2';
    case V3_3 = '3.3';

    public static function latest(): self
    {
        $cases = self::cases();

        return $cases[\count($cases) - 1];
    }

    public static function tryFromString(string $version): ?self
    {
        $normalised = ltrim(trim($version), 'vV');
        // IATA writes "3.2" in owl:versionInfo but "3.2.0" in some examples.
        if (preg_match('/^(\d+\.\d+)\.0$/', $normalised, $matches) === 1) {
            $normalised = $matches[1];
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
            if ($edition->dataModelVersion() === $this) {
                return $edition;
            }
        }

        throw new LogicException("No edition declares data model version {$this->value}.");
    }

    /**
     * The owl:versionIRI of the cargo ontology at this version, as advertised
     * in api:hasSupportedOntologyVersion.
     */
    public function ontologyVersionIri(): string
    {
        return Namespaces::CARGO_ONTOLOGY . '/' . $this->value;
    }
}
