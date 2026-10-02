<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Model;

use LambdaTwelve\OneRecord\Rdf\Iri;

/**
 * Logistics object URIs as {base}/logistics-objects/{uuid}.
 *
 * With a seed, ids are UUID v5 of (seed, local key): the same graph published
 * for the same seed (a shipment reference, say) always gets the same URIs,
 * which is what makes republishing idempotent. Without a seed, ids are
 * random UUID v4 from the injected byte source.
 */
final class UuidIriMinter implements IriMinter
{
    /** @var callable(int): string */
    private $randomBytes;

    /**
     * @param ?callable(int): string $randomBytes defaults to random_bytes
     */
    public function __construct(
        private readonly string $baseUrl,
        private readonly ?string $seed = null,
        ?callable $randomBytes = null,
    ) {
        if (!str_starts_with($baseUrl, 'http://') && !str_starts_with($baseUrl, 'https://')) {
            throw new ModelException(\sprintf('The base URL must be absolute: "%s".', $baseUrl));
        }
        $this->randomBytes = $randomBytes ?? static fn(int $length): string => random_bytes(max(1, $length));
    }

    public function mint(string $localKey, array $types): Iri
    {
        $id = $this->seed === null
            ? Uuid::v4($this->randomBytes)
            : Uuid::v5(Uuid::NAMESPACE_URL, $this->seed . '|' . $localKey);

        return new Iri(rtrim($this->baseUrl, '/') . '/logistics-objects/' . $id);
    }
}
