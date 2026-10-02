<?php

declare(strict_types=1);

namespace LambdaTwelve\OneRecord\Server\Spi;

use LambdaTwelve\OneRecord\Api\Permission;

/**
 * Everything an access policy may be asked about. The first four are the
 * spec's permissions on a logistics object; the rest are the server's other
 * decisions, including the two endpoints the spec marks "internal only".
 */
enum Action: string
{
    case ReadLogisticsObject = 'GET_LOGISTICS_OBJECT';
    case ChangeLogisticsObject = 'PATCH_LOGISTICS_OBJECT';
    case PostLogisticsEvent = 'POST_LOGISTICS_EVENT';
    case ReadLogisticsEvent = 'GET_LOGISTICS_EVENT';

    /** GET /logistics-objects/{id}/audit-trail: inherits from the object unless the host separates them. */
    case ReadAuditTrail = 'READ_AUDIT_TRAIL';

    /** POST /logistics-objects: "internal only" per spec; denied unless the policy says otherwise. */
    case CreateLogisticsObject = 'CREATE_LOGISTICS_OBJECT';

    /** PATCH /action-requests/{id}: the holder accepting or rejecting over HTTP; "internal only". */
    case DecideActionRequest = 'DECIDE_ACTION_REQUEST';

    /** GET/HEAD /action-requests/{id} by someone other than its requestor. */
    case ReadActionRequest = 'READ_ACTION_REQUEST';

    /** DELETE /action-requests/{id} by someone other than its requestor (the holder's side). */
    case RevokeActionRequest = 'REVOKE_ACTION_REQUEST';

    public static function fromPermission(Permission $permission): self
    {
        return match ($permission) {
            Permission::GetLogisticsObject => self::ReadLogisticsObject,
            Permission::PatchLogisticsObject => self::ChangeLogisticsObject,
            Permission::PostLogisticsEvent => self::PostLogisticsEvent,
            Permission::GetLogisticsEvent => self::ReadLogisticsEvent,
        };
    }

    public function permission(): ?Permission
    {
        return match ($this) {
            self::ReadLogisticsObject => Permission::GetLogisticsObject,
            self::ChangeLogisticsObject => Permission::PatchLogisticsObject,
            self::PostLogisticsEvent => Permission::PostLogisticsEvent,
            self::ReadLogisticsEvent => Permission::GetLogisticsEvent,
            default => null,
        };
    }
}
