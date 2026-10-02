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
        if (preg_match('#^https?://[^/\s]+$#', $baseUrl) !== 1) {
            throw new InvalidArgumentException(\sprintf('The base URL must be scheme and host only, got "%s".', $baseUrl));
        }
        if ($basePath !== '' && (!str_starts_with($basePath, '/') || str_ends_with($basePath, '/'))) {
            throw new InvalidArgumentException('The base path must start with "/" and not end with one.');
        }
        $versions = $apiVersions ?? ApiVersion::allDescending();
        if ($versions === []) {
            throw new InvalidArgumentException('At least one API version must be served.');
        }
        usort($versions, static fn(ApiVersion $a, ApiVersion $b): int => version_compare($b->value, $a->value));
        $this->apiVersions = $versions;
        $models = $dataModelVersions ?? array_reverse(DataModelVersion::cases());
        if ($models === []) {
            throw new InvalidArgumentException('At least one data model version must be advertised.');
        }
        usort($models, static fn(DataModelVersion $a, DataModelVersion $b): int => version_compare($b->value, $a->value));
        $this->dataModelVersions = $models;
        if (!\in_array('en-US', $languages, true)) {
            throw new InvalidArgumentException('en-US must be among the supported languages (the spec requires it).');
        }
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
