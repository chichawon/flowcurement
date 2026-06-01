<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table): void {
            $table->id();
            $table->string('item_name')->index();
            $table->string('item_code', 100)->unique()->index();
            $table->string('item_type', 100)->index();
            $table->foreignId('supplier_id')->constrained('business_partners')->restrictOnDelete();
            $table->decimal('supplier_price', 15, 2)->default(0);
            $table->decimal('percentage', 8, 2)->default(0);
            $table->decimal('item_price', 15, 2)->default(0);
            $table->unsignedInteger('available_stock')->default(0);
            $table->unsignedInteger('reorder_point')->default(0);
            $table->string('taxable', 10)->default('no')->index();
            $table->string('item_image')->nullable();
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['supplier_id', 'status']);
            $table->index(['item_type', 'status']);
            $table->index(['available_stock', 'reorder_point']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
