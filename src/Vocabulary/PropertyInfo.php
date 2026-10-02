<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Vocabulary;

final readonly class PropertyInfo
{
    /** Domain marker meaning "usable on any class". */
    public const string ANY_DOMAIN = '*';

    /**
     * @param list<string> $ranges IRIs (classes, code lists or XSD datatypes)
     * @param list<string> $domains class IRIs, or [ANY_DOMAIN]
     */
    public function __construct(
        public string $iri,
        public string $name,
        public PropertyKind $kind,
        public array $ranges,
        public array $domains,
        public string $since,
        public ?string $deprecatedIn,
        public ?string $removedIn,
    ) {}

    public function isDeprecated(): bool
    {
        return $this->deprecatedIn !== null || $this->removedIn !== null;
    }

    public function acceptsAnyDomain(): bool
    {
        return \in_array(self::ANY_DOMAIN, $this->domains, true);
    }
}
