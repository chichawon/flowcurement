<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table): void {
            $table->date('received_date')->nullable()->after('dr_date');
            $table->string('received_by')->nullable()->after('received_date');
            $table->string('delivered_by')->nullable()->after('received_by');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_receipts', function (Blueprint $table): void {
            $table->dropColumn(['received_date', 'received_by', 'delivered_by']);
        });
    }
};
