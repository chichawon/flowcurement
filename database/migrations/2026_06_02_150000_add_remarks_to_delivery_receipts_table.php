<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table): void {
            $table->string('remarks', 30)->default('on_hold')->after('contact_no')->index();
        });

        DB::table('delivery_receipts')
            ->where('status', 'completed')
            ->update(['remarks' => 'completed', 'status' => 'pending']);

        DB::table('delivery_receipts')
            ->whereExists(function ($query): void {
                $query->select(DB::raw(1))
                    ->from('sales_invoices')
                    ->whereColumn('sales_invoices.delivery_receipt_id', 'delivery_receipts.id')
                    ->whereNull('sales_invoices.deleted_at')
                    ->where('sales_invoices.status', '!=', 'void');
            })
            ->update(['status' => 'billed']);
    }

    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table): void {
            $table->dropColumn('remarks');
        });
    }
};
