<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('sales_invoices')
            ->where('balance_amount', '>', 0)
            ->where('balance_amount', '<=', 0.01)
            ->update([
                'balance_amount' => 0,
                'status' => 'paid',
            ]);

        DB::table('sales_collection_invoices')
            ->where('remaining_balance', '>', 0)
            ->where('remaining_balance', '<=', 0.01)
            ->update([
                'remaining_balance' => 0,
            ]);
    }

    public function down(): void
    {
        //
    }
};
