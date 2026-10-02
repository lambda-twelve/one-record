<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Spec;

use InvalidArgumentException;

/**
 * The one table of API features that depend on the negotiated API version.
 *
 * Serializers and endpoints ask here instead of comparing versions, so adding
 * an edition means adding rows, and a 2.2 partner never receives a property
 * that only exists in 2.3.
 */
final class ApiFeatures
{
    /** Action requests record when the current status started (api:hasRequestStatusSince). */
    public const string REQUEST_STATUS_SINCE = Namespaces::API . 'hasRequestStatusSince';

    /** Action requests expose previous statuses (api:hasRequestStatusHistory). */
    public const string REQUEST_STATUS_HISTORY = Namespaces::API . 'hasRequestStatusHistory';

    /** Errors carry a severity (api:hasSeverity). */
    public const string ERROR_SEVERITY = Namespaces::API . 'hasSeverity';

    /** Access delegations may expire (api:expiresAt on api:AccessDelegation). */
    public const string ACCESS_DELEGATION_EXPIRY = Namespaces::API . 'AccessDelegation' . '|' . Namespaces::API . 'expiresAt';

    /** Access delegations name exactly one organisation in api:isRequestedFor. */
    public const string SINGLE_DELEGATE = Namespaces::API . 'AccessDelegation' . '|single-delegate';

    /** POST /logistics-events creates one event on several objects (207 Multi-Status). */
    public const string BULK_LOGISTICS_EVENTS = Namespaces::API . 'MultiStatusResponse';

    /** Illegal action-request revocations answer 422 instead of 400. */
    public const string REVOCATION_422 = Namespaces::API . 'ActionRequest' . '|revocation-422';

    /** @var array<string, ApiVersion> feature => first version that has it */
    private const array SINCE = [
        self::REQUEST_STATUS_SINCE => ApiVersion::V2_3_0,
        self::REQUEST_STATUS_HISTORY => ApiVersion::V2_3_0,
        self::ERROR_SEVERITY => ApiVersion::V2_3_0,
        self::ACCESS_DELEGATION_EXPIRY => ApiVersion::V2_3_0,
        self::SINGLE_DELEGATE => ApiVersion::V2_3_0,
        self::BULK_LOGISTICS_EVENTS => ApiVersion::V2_3_0,
        self::REVOCATION_422 => ApiVersion::V2_3_0,
    ];

    private function __construct() {}

    public static function available(ApiVersion $version, string $feature): bool
    {
        $since = self::SINCE[$feature] ?? throw new InvalidArgumentException(\sprintf('Unknown API feature "%s".', $feature));

        return $version->isAtLeast($since);
    }

    /**
     * @return list<string>
     */
    public static function all(): array
    {
        return array_keys(self::SINCE);
    }
}
