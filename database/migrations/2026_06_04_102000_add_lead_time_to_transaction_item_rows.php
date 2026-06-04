<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quotation_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('quotation_items', 'lead_time')) {
                $table->string('lead_time')->nullable()->after('description');
            }
        });

        Schema::table('sales_order_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales_order_items', 'lead_time')) {
                $table->string('lead_time')->nullable()->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_order_items', 'lead_time')) {
                $table->dropColumn('lead_time');
            }
        });

        Schema::table('quotation_items', function (Blueprint $table): void {
            if (Schema::hasColumn('quotation_items', 'lead_time')) {
                $table->dropColumn('lead_time');
            }
        });
    }
};
