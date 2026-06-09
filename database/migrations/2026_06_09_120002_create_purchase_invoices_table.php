<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('purchase_invoice_no')->unique();
            $table->date('invoice_date');
            $table->string('supplier_invoice_no');
            $table->foreignId('purchase_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('purchase_order_no')->nullable();
            $table->foreignId('supplier_id')->constrained('business_partners');
            $table->string('supplier_name');
            $table->text('supplier_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('terms')->nullable();
            $table->date('due_date')->nullable();
            $table->string('currency')->default('php');
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->string('status')->default('unpaid')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['supplier_id', 'supplier_invoice_no']);
            $table->index(['supplier_id', 'status']);
            $table->index('invoice_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
