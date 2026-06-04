<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_partners', function (Blueprint $table): void {
            $table->id();
            $table->string('type', 20)->index();
            $table->string('company_name')->index();
            $table->string('company_code', 50)->unique()->index();
            $table->string('tin_number', 15);
            $table->string('contact_person');
            $table->string('contact_no', 11);
            $table->string('agent_name')->nullable();
            $table->decimal('credit_limit', 15, 2)->default(0);
            $table->text('company_address')->nullable();
            $table->string('under_pesa', 10)->default('no');
            $table->string('vatable', 20)->default('non_vat');
            $table->string('terms', 30)->default('30');
            $table->string('status', 20)->default('active')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index(['type', 'vatable']);
            $table->index(['type', 'terms']);
            $table->index(['type', 'under_pesa']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_partners');
    }
};
