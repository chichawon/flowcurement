<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales_invoices', 'withholding_tax_rate')) {
                $table->decimal('withholding_tax_rate', 8, 2)->default(0)->after('tax_amount');
            }

            if (! Schema::hasColumn('sales_invoices', 'withholding_tax_amount')) {
                $table->decimal('withholding_tax_amount', 15, 2)->default(0)->after('withholding_tax_rate');
            }
        });

        DB::statement("ALTER TABLE sales_invoices ALTER status SET DEFAULT 'unpaid'");

        DB::table('sales_invoices')
            ->whereIn('status', ['pending', 'issued', 'partial_paid'])
            ->update(['status' => 'unpaid']);

        DB::table('sales_invoices')
            ->where('status', 'void')
            ->update(['status' => 'cancelled']);

        DB::table('delivery_receipts')
            ->whereExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('sales_invoices')
                    ->whereColumn('sales_invoices.delivery_receipt_id', 'delivery_receipts.id')
                    ->whereNull('sales_invoices.deleted_at')
                    ->where('sales_invoices.status', '!=', 'cancelled');
            })
            ->where('status', '!=', 'cancelled')
            ->update(['status' => 'billed']);
    }

    public function down(): void
    {
        Schema::table('sales_invoices', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_invoices', 'withholding_tax_rate')) {
                $table->dropColumn('withholding_tax_rate');
            }

            if (Schema::hasColumn('sales_invoices', 'withholding_tax_amount')) {
                $table->dropColumn('withholding_tax_amount');
            }
        });
    }
};
