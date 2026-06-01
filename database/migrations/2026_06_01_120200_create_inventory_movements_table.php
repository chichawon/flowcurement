<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained('items');
            $table->string('movement_type', 20)->index();
            $table->decimal('quantity', 15, 2);
            $table->decimal('before_stock', 15, 2)->default(0);
            $table->decimal('after_stock', 15, 2)->default(0);
            $table->string('reference_type', 50)->index();
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};

