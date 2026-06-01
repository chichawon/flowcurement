<?php

namespace Database\Seeders;

use App\Modules\Quotations\Models\UnitMeasure;
use Illuminate\Database\Seeder;

class UnitMeasureSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['pair', 'pack', 'bundle'] as $name) {
            UnitMeasure::query()->firstOrCreate(['name' => $name], ['status' => 'active']);
        }
    }
}
