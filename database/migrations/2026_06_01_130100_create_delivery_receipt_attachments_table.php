<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_receipt_attachments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('delivery_receipt_id')->constrained('delivery_receipts')->cascadeOnDelete();
            $table->string('file_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('delivery_receipt_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_receipt_attachments');
    }
};
