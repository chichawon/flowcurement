<?php

namespace App\Modules\UserManagement\Helpers;

final class UserManagementPermissions
{
    public const VIEW = 'user-management.view';
    public const CREATE = 'user-management.create';
    public const UPDATE = 'user-management.update';
    public const DELETE = 'user-management.delete';
    public const RESTORE = 'user-management.restore';
    public const FORCE_DELETE = 'user-management.force-delete';
    public const ASSIGN_ROLES = 'user-management.assign-roles';
    public const ASSIGN_PERMISSIONS = 'user-management.assign-permissions';

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::VIEW,
            self::CREATE,
            self::UPDATE,
            self::DELETE,
            self::RESTORE,
            self::FORCE_DELETE,
            self::ASSIGN_ROLES,
            self::ASSIGN_PERMISSIONS,
        ];
    }
}
