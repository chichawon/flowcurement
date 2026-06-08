<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_collections', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_collections', 'bank_number') && ! Schema::hasColumn('sales_collections', 'check_number')) {
                $table->renameColumn('bank_number', 'check_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_collections', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_collections', 'check_number') && ! Schema::hasColumn('sales_collections', 'bank_number')) {
                $table->renameColumn('check_number', 'bank_number');
            }
        });
    }
};
