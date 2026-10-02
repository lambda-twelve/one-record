<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * The four permissions every server must support, per logistics object.
 */
enum Permission: string
{
    case GetLogisticsObject = Api::GET_LOGISTICS_OBJECT;
    case PatchLogisticsObject = Api::PATCH_LOGISTICS_OBJECT;
    case PostLogisticsEvent = Api::POST_LOGISTICS_EVENT;
    case GetLogisticsEvent = Api::GET_LOGISTICS_EVENT;

    public static function tryFromString(string $value): ?self
    {
        $value = str_starts_with($value, 'api:') ? Api::NAMESPACE . substr($value, 4) : $value;

        return self::tryFrom(str_contains($value, ':') ? $value : Api::NAMESPACE . $value);
    }
}
