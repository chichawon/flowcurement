<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table): void {
            $table->id();
            $table->string('purchase_order_no')->unique();
            $table->date('purchase_order_date');
            $table->date('expected_delivery_date')->nullable();
            $table->foreignId('supplier_id')->constrained('business_partners');
            $table->string('supplier_name');
            $table->text('supplier_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('terms')->nullable();
            $table->text('remarks')->nullable();
            $table->string('currency')->default('php');
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status')->default('pending')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['supplier_id', 'status']);
            $table->index('purchase_order_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
