<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_collections', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales_collections', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('contact_person');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_collections', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_collections', 'bank_name')) {
                $table->dropColumn('bank_name');
            }
        });
    }
};
