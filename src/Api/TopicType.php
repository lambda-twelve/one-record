<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

enum TopicType: string
{
    /** The topic is a logistics object URI. */
    case Identifier = Api::LOGISTICS_OBJECT_IDENTIFIER;

    /** The topic is a class IRI (every object of that type). */
    case Type = Api::LOGISTICS_OBJECT_TYPE;

    /**
     * The spelling used in query strings: LOGISTICS_OBJECT_IDENTIFIER or LOGISTICS_OBJECT_TYPE.
     */
    public function shortName(): string
    {
        return substr($this->value, \strlen(Api::NAMESPACE));
    }

    public static function tryFromString(string $value): ?self
    {
        $value = str_starts_with($value, 'api:') ? Api::NAMESPACE . substr($value, 4) : $value;
        // The spec allows "#" to be written as "/" in query strings.
        $value = str_replace('https://onerecord.iata.org/ns/api/', Api::NAMESPACE, $value);

        return self::tryFrom(str_contains($value, ':') ? $value : Api::NAMESPACE . $value);
    }
}
