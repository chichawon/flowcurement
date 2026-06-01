<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\BusinessPartners\Models\BusinessPartner;
use Illuminate\Database\Seeder;

class BusinessPartnerStarterSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = User::query()
            ->where('username', 'admin')
            ->value('id');

        $partners = [
            [
                'type' => 'client',
                'company_name' => 'Default Client',
                'company_code' => 'CLI-DEFAULT',
                'tin_number' => '000-000-000-000',
                'contact_person' => 'Default Contact',
                'contact_no' => '09000000000',
                'credit_limit' => 0,
                'company_address' => 'Default client address',
                'under_pesa' => 'no',
                'vatable' => 'non_vat',
                'terms' => 30,
                'status' => 'active',
            ],
            [
                'type' => 'supplier',
                'company_name' => 'Default Supplier',
                'company_code' => 'SUP-DEFAULT',
                'tin_number' => '000-000-000-000',
                'contact_person' => 'Default Contact',
                'contact_no' => '09000000001',
                'credit_limit' => 0,
                'company_address' => 'Default supplier address',
                'under_pesa' => 'no',
                'vatable' => 'non_vat',
                'terms' => 30,
                'status' => 'active',
            ],
        ];

        foreach ($partners as $partner) {
            BusinessPartner::query()->updateOrCreate(
                ['company_code' => $partner['company_code']],
                $partner + [
                    'created_by' => $adminId,
                    'updated_by' => $adminId,
                ]
            );
        }
    }
}
