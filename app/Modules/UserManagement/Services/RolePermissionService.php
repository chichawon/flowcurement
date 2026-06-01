<?php

namespace App\Modules\UserManagement\Services;

use App\Modules\UserManagement\Models\Permission;
use App\Modules\UserManagement\Models\Role;
use Illuminate\Support\Collection;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionService
{
    private const MODULE_VIEW_PERMISSIONS = [
        'dashboard.view',
        'business-partners.view',
        'business-partners.create',
        'business-partners.update',
        'business-partners.delete',
        'business-partners.restore',
        'business-partners.force-delete',
        'inventory.view',
        'items.view',
        'items.create',
        'items.update',
        'items.delete',
        'items.restore',
        'items.force-delete',
        'purchasing.view',
        'sales.view',
        'reports.view',
        'user-management.view',
        'quotations.view',
        'quotations.create',
        'quotations.update',
        'quotations.delete',
        'quotations.print',
        'quotations.approve',
        'quotations.convert',
    ];

    /**
     * @return Collection<int, Role>
     */
    public function roles(): Collection
    {
        return Role::query()
            ->with('permissions:id,name')
            ->orderBy('name')
            ->get();
    }

    /**
     * @return Collection<int, Permission>
     */
    public function permissions(): Collection
    {
        return Permission::query()
            ->whereIn('name', self::MODULE_VIEW_PERMISSIONS)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param array<int, string> $permissionNames
     */
    public function syncRolePermissions(Role $role, array $permissionNames): Role
    {
        $role->syncPermissions($permissionNames);
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role->refresh();
    }
}
