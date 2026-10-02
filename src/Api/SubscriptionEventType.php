<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * What a subscriber wants to hear about. Each maps to the notification it produces.
 */
enum SubscriptionEventType: string
{
    case LogisticsObjectCreated = Api::LOGISTICS_OBJECT_CREATED;
    case LogisticsObjectUpdated = Api::LOGISTICS_OBJECT_UPDATED;
    case LogisticsEventReceived = Api::LOGISTICS_EVENT_RECEIVED;

    public static function tryFromString(string $value): ?self
    {
        $value = str_starts_with($value, 'api:') ? Api::NAMESPACE . substr($value, 4) : $value;

        return self::tryFrom(str_contains($value, ':') ? $value : Api::NAMESPACE . $value);
    }

    public function notification(): NotificationEventType
    {
        return NotificationEventType::from($this->value);
    }
}
