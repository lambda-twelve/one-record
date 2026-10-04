<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server;

use InvalidArgumentException;
use LambdaTwelve\OneRecord\Rdf\Iri;
use LambdaTwelve\OneRecord\Spec\ApiVersion;
use LambdaTwelve\OneRecord\Spec\DataModelVersion;

/**
 * What a host tells the server about itself. Everything else comes through
 * the SPI.
 */
final readonly class ServerConfig
{
    /** @var non-empty-list<ApiVersion> */
    public array $apiVersions;

    /** @var non-empty-list<DataModelVersion> */
    public array $dataModelVersions;

    /**
     * @param string $baseUrl scheme and host, e.g. https://1r.example.com
     * @param Iri $dataHolder the Organization URI of the data holder (also a logistics object this server serves)
     * @param string $basePath path prefix the server is mounted under, e.g. /onerecord (empty for the root)
     * @param ?list<ApiVersion> $apiVersions API versions served, highest first; default: every version the package knows
     * @param ?list<DataModelVersion> $dataModelVersions ontology versions advertised; the newest is used for validation
     * @param list<string> $languages at least en-US, which the spec requires
     * @param int $maxBodyBytes request bodies above this are refused with 413
     * @param int $embeddedDepth how deep ?embedded=true follows links on the same server
     * @param bool $bulkLogisticsEvents serve the optional 2.3 POST /logistics-events endpoint
     */
    public function __construct(
        public string $baseUrl,
        public Iri $dataHolder,
        public string $basePath = '',
        ?array $apiVersions = null,
        ?array $dataModelVersions = null,
        public array $languages = ['en-US'],
        public int $maxBodyBytes = 1_048_576,
        public int $embeddedDepth = 3,
        public bool $bulkLogisticsEvents = false,
        public ?string $dataHolderType = null,
    ) {
        $problems = self::problems(['baseUrl' => $baseUrl, 'dataHolder' => $dataHolder, 'basePath' => $basePath, 'apiVersions' => $apiVersions, 'dataModelVersions' => $dataModelVersions, 'languages' => $languages, 'maxBodyBytes' => $maxBodyBytes, 'embeddedDepth' => $embeddedDepth]);
        if ($problems !== []) {
            throw new InvalidArgumentException($problems[0]);
        }
        $versions = $apiVersions ?? ApiVersion::allDescending();
        usort($versions, static fn(ApiVersion $a, ApiVersion $b): int => version_compare($b->value, $a->value));
        $this->apiVersions = $versions === [] ? ApiVersion::allDescending() : $versions;
        $models = $dataModelVersions ?? array_reverse(DataModelVersion::cases());
        usort($models, static fn(DataModelVersion $a, DataModelVersion $b): int => version_compare($b->value, $a->value));
        $this->dataModelVersions = $models === [] ? array_reverse(DataModelVersion::cases()) : $models;
    }

    /**
     * What is wrong with a set of settings, in plain sentences, without
     * constructing anything: for a host's status page on an install that is
     * not configured yet. Empty means the constructor would accept them.
     * Values may be the typed objects the constructor takes or the raw
     * strings a settings form holds (version strings, an IRI as a string).
     *
     * @param array<string, mixed> $settings keys as the constructor's parameters; a missing key means "not set"
     * @return list<string>
     */
    public static function problems(array $settings): array
    {
        $problems = [];
        $baseUrl = $settings['baseUrl'] ?? null;
        if (!\is_string($baseUrl) || $baseUrl === '') {
            $problems[] = 'The base URL is not set.';
        } elseif (preg_match('#^https?://[^/\s]+$#', $baseUrl) !== 1) {
            $problems[] = \sprintf('The base URL must be scheme and host only, got "%s".', $baseUrl);
        }
        $holder = $settings['dataHolder'] ?? null;
        if ($holder === null || $holder === '') {
            $problems[] = 'The data holder is not set: the IRI of the organisation this server speaks for.';
        } elseif (!$holder instanceof Iri) {
            if (!\is_string($holder)) {
                $problems[] = 'The data holder must be an IRI.';
            } else {
                try {
                    new Iri($holder);
                } catch (InvalidArgumentException $e) {
                    $problems[] = 'The data holder is not a valid IRI: ' . $e->getMessage();
                }
            }
        }
        $basePath = $settings['basePath'] ?? '';
        if (!\is_string($basePath)) {
            $problems[] = 'The base path must be a string.';
        } elseif ($basePath !== '' && (!str_starts_with($basePath, '/') || str_ends_with($basePath, '/'))) {
            $problems[] = 'The base path must start with "/" and not end with one.';
        }
        $versions = $settings['apiVersions'] ?? null;
        if ($versions !== null) {
            if (!\is_array($versions) || $versions === []) {
                $problems[] = 'At least one API version must be served.';
            } else {
                foreach ($versions as $version) {
                    if (!$version instanceof ApiVersion && (!\is_string($version) || ApiVersion::tryFrom($version) === null)) {
                        $problems[] = \sprintf('Unknown API version "%s"; this package knows %s.', \is_scalar($version) ? (string) $version : \gettype($version), implode(', ', array_map(static fn(ApiVersion $v): string => $v->value, ApiVersion::cases())));
                    }
                }
            }
        }
        $models = $settings['dataModelVersions'] ?? null;
        if ($models !== null) {
            if (!\is_array($models) || $models === []) {
                $problems[] = 'At least one data model version must be advertised.';
            } else {
                foreach ($models as $model) {
                    if (!$model instanceof DataModelVersion && (!\is_string($model) || DataModelVersion::tryFrom($model) === null)) {
                        $problems[] = \sprintf('Unknown data model version "%s"; this package knows %s.', \is_scalar($model) ? (string) $model : \gettype($model), implode(', ', array_map(static fn(DataModelVersion $v): string => $v->value, DataModelVersion::cases())));
                    }
                }
            }
        }
        $languages = $settings['languages'] ?? ['en-US'];
        if (!\is_array($languages) || !\in_array('en-US', $languages, true)) {
            $problems[] = 'en-US must be among the supported languages (the spec requires it).';
        }
        $maxBody = $settings['maxBodyBytes'] ?? 1;
        if (!\is_int($maxBody) || $maxBody < 1) {
            $problems[] = 'The request body limit must be a positive number of bytes.';
        }
        $depth = $settings['embeddedDepth'] ?? 0;
        if (!\is_int($depth) || $depth < 0) {
            $problems[] = 'The embedding depth must be zero or more.';
        }

        return $problems;
    }

    /**
     * The URL every resource path hangs off (base URL plus base path, no trailing slash).
     */
    public function endpoint(): string
    {
        return $this->baseUrl . $this->basePath;
    }

    public function highestApiVersion(): ApiVersion
    {
        return $this->apiVersions[0];
    }

    public function supports(ApiVersion $version): bool
    {
        return \in_array($version, $this->apiVersions, true);
    }

    public function validationModel(): DataModelVersion
    {
        return $this->dataModelVersions[0];
    }

    public function logisticsObjectIri(string $id): Iri
    {
        return new Iri($this->endpoint() . '/logistics-objects/' . $id);
    }

    public function actionRequestIri(string $id): Iri
    {
        return new Iri($this->endpoint() . '/action-requests/' . $id);
    }

    public function logisticsEventIri(string $objectId, string $eventId): Iri
    {
        return new Iri($this->endpoint() . '/logistics-objects/' . $objectId . '/logistics-events/' . $eventId);
    }

    /**
     * The path-relative part of a URI under this server, or null if it is not ours.
     */
    public function relativePath(Iri $iri): ?string
    {
        $prefix = $this->endpoint() . '/';
        if (!str_starts_with($iri->value, $prefix)) {
            return null;
        }
        $rest = substr($iri->value, \strlen($prefix));
        $query = strpos($rest, '?');

        return $query === false ? $rest : substr($rest, 0, $query);
    }

    public function isLocal(Iri $iri): bool
    {
        return $this->relativePath($iri) !== null;
    }
}
