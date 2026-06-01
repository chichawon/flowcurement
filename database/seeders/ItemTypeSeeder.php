<?php

namespace Database\Seeders;

use App\Modules\Items\Models\ItemType;
use Illuminate\Database\Seeder;

class ItemTypeSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Janitorial', 'Office Supply', 'Product Consumable', 'Packaging'] as $name) {
            ItemType::query()->firstOrCreate(
                ['name' => $name],
                ['status' => 'active']
            );
        }
    }
}
