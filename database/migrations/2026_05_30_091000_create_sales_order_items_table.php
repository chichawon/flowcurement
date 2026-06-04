<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sales_order_id')->constrained('sales_orders')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items');
            $table->text('description')->nullable();
            $table->string('lead_time')->nullable();
            $table->decimal('order_quantity', 15, 2);
            $table->decimal('balance_quantity', 15, 2)->default(0);
            $table->foreignId('unit_measure_id')->constrained('unit_measures');
            $table->decimal('price', 15, 2);
            $table->decimal('available_stock', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};
