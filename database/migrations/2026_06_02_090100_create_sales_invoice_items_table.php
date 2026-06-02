<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoice_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->foreignId('delivery_receipt_id')->constrained('delivery_receipts');
            $table->foreignId('delivery_receipt_item_id')->constrained('delivery_receipt_items');
            $table->foreignId('sales_order_item_id')->constrained('sales_order_items');
            $table->foreignId('item_id')->constrained('items');
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->foreignId('unit_measure_id')->constrained('unit_measures');
            $table->decimal('delivered_quantity', 15, 2)->default(0);
            $table->decimal('previously_invoiced_quantity', 15, 2)->default(0);
            $table->decimal('invoiceable_quantity', 15, 2)->default(0);
            $table->decimal('quantity', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('withholding_tax_rate', 8, 2)->default(0);
            $table->decimal('withholding_tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();

            $table->index(['delivery_receipt_item_id', 'sales_order_item_id'], 'si_items_dr_so_item_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoice_items');
    }
};
