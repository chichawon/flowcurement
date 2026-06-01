<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\UserManagement\Models\Permission;
use App\Modules\UserManagement\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['admin', 'sales', 'purchasing', 'inventory', 'manager'];

        foreach ($roles as $roleName) {
            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }

        $modulePermissions = [
            'dashboard.view',
            'business-partners.view',
            'inventory.view',
            'items.view',
            'purchasing.view',
            'sales.view',
            'reports.view',
            'user-management.view',
            'quotations.view',
            'quotations.print',
            'sales-orders.view',
            'sales-orders.print',
            'delivery-receipts.view',
            'delivery-receipts.print',
        ];

        $adminRole = Role::where('name', 'admin')->where('guard_name', 'web')->firstOrFail();
        $adminRole->syncPermissions(Permission::all());

        $managerRole = Role::where('name', 'manager')->where('guard_name', 'web')->first();
        $managerRole?->syncPermissions(
            Permission::whereIn('name', $modulePermissions)->get()
        );

        $salesRole = Role::where('name', 'sales')->where('guard_name', 'web')->first();
        $salesRole?->syncPermissions(Permission::whereIn('name', [
            'dashboard.view',
            'sales.view',
            'quotations.view',
            'quotations.create',
            'quotations.update',
            'quotations.print',
            'sales-orders.view',
            'sales-orders.create',
            'sales-orders.update',
            'sales-orders.delete',
            'sales-orders.approve',
            'sales-orders.cancel',
            'sales-orders.print',
            'delivery-receipts.view',
            'delivery-receipts.create',
            'delivery-receipts.update',
            'delivery-receipts.cancel',
            'delivery-receipts.print',
            'business-partners.view',
            'business-partners.create',
            'business-partners.update',
        ])->get());

        $purchasingRole = Role::where('name', 'purchasing')->where('guard_name', 'web')->first();
        $purchasingRole?->syncPermissions(Permission::whereIn('name', [
            'dashboard.view',
            'purchasing.view',
            'items.view',
            'items.create',
            'items.update',
            'items.delete',
            'items.restore',
            'business-partners.view',
            'business-partners.create',
            'business-partners.update',
        ])->get());

        $inventoryRole = Role::where('name', 'inventory')->where('guard_name', 'web')->first();
        $inventoryRole?->syncPermissions(Permission::whereIn('name', [
            'dashboard.view',
            'inventory.view',
            'items.update',
        ])->get());

        $admin = User::query()
            ->where('username', 'admin')
            ->orWhere('email', 'admin@flowcurement.test')
            ->first();

        if (! $admin) {
            $admin = User::create([
                'name' => 'Flowcurement Admin',
                'username' => 'admin',
                'email' => 'admin@flowcurement.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]);
        } else {
            $admin->forceFill([
                'name' => 'Flowcurement Admin',
                'username' => 'admin',
                'email' => 'admin@flowcurement.test',
                'email_verified_at' => $admin->email_verified_at ?? now(),
                'status' => 'active',
            ])->save();
        }

        if (! $admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }
    }
}
