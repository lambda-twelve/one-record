<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Api;

use LambdaTwelve\OneRecord\Vocabulary\Generated\Api;

/**
 * api:RequestStatus, with the two state machines of the spec's action-request
 * page: accept/reject/revoke for change, subscription and access-delegation
 * requests (only the latter two revocable after acceptance), acknowledge/
 * reject/revoke/fail for verification requests.
 */
enum RequestStatus: string
{
    case Pending = Api::REQUEST_PENDING;
    case Accepted = Api::REQUEST_ACCEPTED;
    case Acknowledged = Api::REQUEST_ACKNOWLEDGED;
    case Rejected = Api::REQUEST_REJECTED;
    case Failed = Api::REQUEST_FAILED;
    case Revoked = Api::REQUEST_REVOKED;

    /**
     * Full IRI, "api:REQUEST_ACCEPTED" or bare "REQUEST_ACCEPTED" (and the
     * spec's audit-trail short forms PENDING/ACCEPTED/REJECTED).
     */
    public static function tryFromString(string $value): ?self
    {
        $value = trim($value);
        if (str_starts_with($value, 'api:')) {
            $value = substr($value, 4);
        }
        if (!str_contains($value, ':') && !str_starts_with($value, 'REQUEST_')) {
            $value = 'REQUEST_' . $value;
        }
        if (!str_contains($value, ':')) {
            $value = Api::NAMESPACE . $value;
        }

        return self::tryFrom($value);
    }

    public function shortName(): string
    {
        return substr($this->value, \strlen(Api::NAMESPACE));
    }

    public function isFinal(): bool
    {
        return $this !== self::Pending && $this !== self::Accepted;
    }

    public function canTransitionTo(self $next, ActionRequestType $type): bool
    {
        if ($type === ActionRequestType::Verification) {
            return $this === self::Pending && \in_array($next, [self::Acknowledged, self::Rejected, self::Revoked, self::Failed], true);
        }

        return match ($this) {
            self::Pending => \in_array($next, [self::Accepted, self::Rejected, self::Revoked], true),
            self::Accepted => $next === self::Failed || ($next === self::Revoked && $type->revocableAfterAcceptance()),
            default => false,
        };
    }
}
