<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_collection_invoices')) {
            Schema::table('sales_collection_invoices', function (Blueprint $table): void {
                $table->index(['sales_collection_id', 'sales_invoice_id'], 'sci_collection_invoice_idx');
            });

            return;
        }

        Schema::create('sales_collection_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_collection_id')->constrained('sales_collections')->cascadeOnDelete();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices');
            $table->string('sales_invoice_no');
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_invoice_amount', 15, 2)->default(0);
            $table->decimal('withholding_tax_amount', 15, 2)->default(0);
            $table->decimal('previous_balance', 15, 2)->default(0);
            $table->decimal('applied_amount', 15, 2)->default(0);
            $table->decimal('remaining_balance', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['sales_collection_id', 'sales_invoice_id'], 'sci_collection_invoice_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_collection_invoices');
    }
};
