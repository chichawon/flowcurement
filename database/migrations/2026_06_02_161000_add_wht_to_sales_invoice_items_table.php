<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales_invoice_items', 'withholding_tax_rate')) {
                $table->decimal('withholding_tax_rate', 8, 2)->default(0)->after('tax_amount');
            }

            if (! Schema::hasColumn('sales_invoice_items', 'withholding_tax_amount')) {
                $table->decimal('withholding_tax_amount', 15, 2)->default(0)->after('withholding_tax_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_invoice_items', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_invoice_items', 'withholding_tax_rate')) {
                $table->dropColumn('withholding_tax_rate');
            }

            if (Schema::hasColumn('sales_invoice_items', 'withholding_tax_amount')) {
                $table->dropColumn('withholding_tax_amount');
            }
        });
    }
};
