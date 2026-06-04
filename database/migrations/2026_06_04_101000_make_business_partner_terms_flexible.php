<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE business_partners MODIFY terms VARCHAR(30) NOT NULL DEFAULT '30'");
    }

    public function down(): void
    {
        DB::table('business_partners')
            ->whereNotIn('terms', ['30', '60', '90'])
            ->update(['terms' => '30']);

        DB::statement('ALTER TABLE business_partners MODIFY terms SMALLINT UNSIGNED NOT NULL DEFAULT 30');
    }
};
