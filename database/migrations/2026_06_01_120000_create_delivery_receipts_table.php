<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_receipts', function (Blueprint $table): void {
            $table->id();
            $table->string('delivery_receipt_no')->unique();
            $table->date('dr_date');
            $table->foreignId('sales_order_id')->constrained('sales_orders');
            $table->string('sales_order_no');
            $table->string('customer_po')->nullable();
            $table->string('agent_name');
            $table->foreignId('business_partner_id')->constrained('business_partners');
            $table->string('company_name');
            $table->unsignedInteger('terms')->default(30);
            $table->text('company_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no')->nullable();
            $table->string('status', 30)->default('pending')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['sales_order_id', 'status']);
            $table->index('dr_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_receipts');
    }
};

