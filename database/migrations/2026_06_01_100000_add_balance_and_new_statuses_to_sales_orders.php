<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales_order_items', function (Blueprint $table): void {
            if (! Schema::hasColumn('sales_order_items', 'balance_quantity')) {
                $table->decimal('balance_quantity', 15, 2)->default(0)->after('order_quantity');
            }
        });

        DB::table('sales_order_items')->update([
            'balance_quantity' => DB::raw('order_quantity'),
        ]);

        DB::table('sales_orders')
            ->whereIn('status', ['draft', 'approved', 'cancelled'])
            ->update(['status' => 'pending']);

        DB::table('sales_orders')
            ->where('status', 'completed')
            ->update(['status' => 'served']);

        DB::statement("ALTER TABLE sales_orders ALTER status SET DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE sales_orders ALTER status SET DEFAULT 'draft'");

        DB::table('sales_orders')
            ->whereIn('status', ['pending', 'partial'])
            ->update(['status' => 'draft']);

        DB::table('sales_orders')
            ->where('status', 'served')
            ->update(['status' => 'completed']);

        Schema::table('sales_order_items', function (Blueprint $table): void {
            if (Schema::hasColumn('sales_order_items', 'balance_quantity')) {
                $table->dropColumn('balance_quantity');
            }
        });
    }
};
