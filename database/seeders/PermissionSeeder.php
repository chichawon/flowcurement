<?php

namespace Database\Seeders;

use App\Modules\UserManagement\Helpers\UserManagementPermissions;
use App\Modules\BusinessPartners\Helpers\BusinessPartnerPermissions;
use App\Modules\Items\Helpers\ItemPermissions;
use App\Modules\Quotations\Helpers\QuotationPermissions;
use App\Modules\Sales\Helpers\DeliveryReceiptPermissions;
use App\Modules\Sales\Helpers\SalesInvoicePermissions;
use App\Modules\Sales\Helpers\SalesOrderPermissions;
use App\Modules\UserManagement\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $modules = [
            'dashboard',
            'user-management',
            'business-partners',
            'items',
            'quotations',
            'sales',
            'purchasing',
            'inventory',
            'reports',
        ];

        $actions = ['view', 'create', 'update', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::firstOrCreate([
                    'name' => $module.'.'.$action,
                    'guard_name' => 'web',
                ]);
            }
        }

        foreach (UserManagementPermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (BusinessPartnerPermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (ItemPermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (QuotationPermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (SalesOrderPermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (DeliveryReceiptPermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach (SalesInvoicePermissions::all() as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        foreach ([
            'sales-collections.view',
            'sales-collections.create',
            'sales-collections.update',
            'sales-collections.delete',
            'sales-collections.print',
        ] as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }
    }
}
