<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_adjustments', function (Blueprint $table): void {
            $table->id();
            $table->string('adjustment_no')->unique();
            $table->date('adjustment_date')->index();
            $table->foreignId('item_id')->constrained('items');
            $table->string('adjustment_type', 20)->index();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('before_stock')->default(0);
            $table->unsignedInteger('after_stock')->default(0);
            $table->string('reason')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['item_id', 'adjustment_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_adjustments');
    }
};
