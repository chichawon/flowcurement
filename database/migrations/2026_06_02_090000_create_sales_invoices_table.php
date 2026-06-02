<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_invoices', function (Blueprint $table): void {
            $table->id();
            $table->string('sales_invoice_no')->unique()->index();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->foreignId('business_partner_id')->constrained('business_partners');
            $table->foreignId('sales_order_id')->constrained('sales_orders');
            $table->foreignId('delivery_receipt_id')->nullable()->constrained('delivery_receipts');
            $table->string('sales_order_no');
            $table->string('delivery_receipt_no')->nullable();
            $table->string('customer_po')->nullable();
            $table->string('company_name');
            $table->integer('terms')->default(30);
            $table->text('company_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('currency')->default('php');
            $table->decimal('tax_rate', 8, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('withholding_tax_rate', 8, 2)->default(0);
            $table->decimal('withholding_tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('balance_amount', 15, 2)->default(0);
            $table->string('status')->default('unpaid')->index();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['sales_order_id', 'status']);
            $table->index(['delivery_receipt_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_invoices');
    }
};
