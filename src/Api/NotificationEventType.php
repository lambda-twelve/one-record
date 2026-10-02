<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

enum NotificationEventType: string
{
    case LogisticsObjectCreated = Api::LOGISTICS_OBJECT_CREATED;
    case LogisticsObjectUpdated = Api::LOGISTICS_OBJECT_UPDATED;
    case LogisticsObjectAvailable = Api::LOGISTICS_OBJECT_AVAILABLE;
    case LogisticsObjectAccessGranted = Api::LOGISTICS_OBJECT_ACCESS_GRANTED;
    case LogisticsEventReceived = Api::LOGISTICS_EVENT_RECEIVED;
    case ChangeRequestPending = Api::CHANGE_REQUEST_PENDING;
    case ChangeRequestAccepted = Api::CHANGE_REQUEST_ACCEPTED;
    case ChangeRequestRejected = Api::CHANGE_REQUEST_REJECTED;
    case ChangeRequestFailed = Api::CHANGE_REQUEST_FAILED;
    case ChangeRequestRevoked = Api::CHANGE_REQUEST_REVOKED;
    case AccessDelegationRequestPending = Api::ACCESS_DELEGATION_REQUEST_PENDING;
    case AccessDelegationRequestAccepted = Api::ACCESS_DELEGATION_REQUEST_ACCEPTED;
    case AccessDelegationRequestRejected = Api::ACCESS_DELEGATION_REQUEST_REJECTED;
    case AccessDelegationRequestFailed = Api::ACCESS_DELEGATION_REQUEST_FAILED;
    case AccessDelegationRequestRevoked = Api::ACCESS_DELEGATION_REQUEST_REVOKED;
    case SubscriptionRequestPending = Api::SUBSCRIPTION_REQUEST_PENDING;
    case SubscriptionRequestAccepted = Api::SUBSCRIPTION_REQUEST_ACCEPTED;
    case SubscriptionRequestRejected = Api::SUBSCRIPTION_REQUEST_REJECTED;
    case SubscriptionRequestFailed = Api::SUBSCRIPTION_REQUEST_FAILED;
    case SubscriptionRequestRevoked = Api::SUBSCRIPTION_REQUEST_REVOKED;
    case VerificationRequestPending = Api::VERIFICATION_REQUEST_PENDING;
    case VerificationRequestAcknowledged = Api::VERIFICATION_REQUEST_ACKNOWLEDGED;
    case VerificationRequestRejected = Api::VERIFICATION_REQUEST_REJECTED;
    case VerificationRequestFailed = Api::VERIFICATION_REQUEST_FAILED;
    case VerificationRequestRevoked = Api::VERIFICATION_REQUEST_REVOKED;

    public static function tryFromString(string $value): ?self
    {
        $value = str_starts_with($value, 'api:') ? Api::NAMESPACE . substr($value, 4) : $value;

        return self::tryFrom(str_contains($value, ':') ? $value : Api::NAMESPACE . $value);
    }
}
