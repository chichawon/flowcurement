<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_receipt_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_receipt_id')->constrained('delivery_receipts')->cascadeOnDelete();
            $table->foreignId('sales_order_item_id')->constrained('sales_order_items');
            $table->foreignId('item_id')->constrained('items');
            $table->string('item_name');
            $table->decimal('ordered_quantity', 15, 2);
            $table->decimal('previously_delivered_quantity', 15, 2)->default(0);
            $table->decimal('remaining_balance_quantity', 15, 2)->default(0);
            $table->decimal('available_stock', 15, 2)->default(0);
            $table->decimal('delivered_quantity', 15, 2)->default(0);
            $table->decimal('balance_quantity', 15, 2)->default(0);
            $table->foreignId('unit_measure_id')->constrained('unit_measures');
            $table->string('stock_status', 30)->default('available')->index();
            $table->string('delivery_no')->nullable();
            $table->date('delivered_date')->nullable();
            $table->string('delivered_by')->nullable();
            $table->string('received_by')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_receipt_items');
    }
};

