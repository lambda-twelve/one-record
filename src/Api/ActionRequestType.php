<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

enum ActionRequestType: string
{
    case Change = Api::ChangeRequest;
    case Subscription = Api::SubscriptionRequest;
    case AccessDelegation = Api::AccessDelegationRequest;
    case Verification = Api::VerificationRequest;

    /**
     * Subscriptions and delegations are standing arrangements, so they can be
     * ended after acceptance; an applied change or an acknowledged
     * verification cannot be undone that way.
     */
    public function revocableAfterAcceptance(): bool
    {
        return $this === self::Subscription || $this === self::AccessDelegation;
    }

    /** The property that carries the request's payload (api:hasChange, ...). */
    public function payloadProperty(): string
    {
        return match ($this) {
            self::Change => Api::hasChange,
            self::Subscription => Api::hasSubscription,
            self::AccessDelegation => Api::hasAccessDelegation,
            self::Verification => Api::hasVerification,
        };
    }

    /**
     * The notification event type announcing a status (CHANGE_REQUEST_ACCEPTED, ...).
     */
    public function notificationFor(RequestStatus $status): NotificationEventType
    {
        $prefix = match ($this) {
            self::Change => 'CHANGE_REQUEST_',
            self::Subscription => 'SUBSCRIPTION_REQUEST_',
            self::AccessDelegation => 'ACCESS_DELEGATION_REQUEST_',
            self::Verification => 'VERIFICATION_REQUEST_',
        };

        return NotificationEventType::from(Api::NAMESPACE . $prefix . substr($status->shortName(), \strlen('REQUEST_')));
    }
}
