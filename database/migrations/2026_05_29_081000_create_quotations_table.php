<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table): void {
            $table->id();
            $table->string('quotation_no')->unique()->index();
            $table->date('quotation_date');
            $table->date('validity_date');
            $table->foreignId('business_partner_id')->constrained('business_partners')->restrictOnDelete();
            $table->text('company_address')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_no', 50)->nullable();
            $table->string('agent_name');
            $table->foreignId('prepared_by')->constrained('users')->restrictOnDelete();
            $table->text('remarks')->nullable();
            $table->string('currency', 20)->default('php');
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->string('status', 30)->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['quotation_date', 'status']);
            $table->index(['currency', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
