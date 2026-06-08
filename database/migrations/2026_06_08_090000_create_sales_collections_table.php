<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_collections', function (Blueprint $table): void {
            $table->id();
            $table->string('collection_no')->unique();
            $table->foreignId('business_partner_id')->constrained('business_partners');
            $table->string('company_name');
            $table->string('agent_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('check_number');
            $table->date('check_date');
            $table->decimal('check_amount', 15, 2)->default(0);
            $table->string('collection_receipt_no')->unique();
            $table->date('collection_receipt_date');
            $table->decimal('collection_receipt_amount', 15, 2)->default(0);
            $table->decimal('applied_amount', 15, 2)->default(0);
            $table->string('status')->default('pending')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_collections');
    }
};
