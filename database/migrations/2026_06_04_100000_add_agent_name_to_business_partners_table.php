<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_partners', function (Blueprint $table): void {
            if (! Schema::hasColumn('business_partners', 'agent_name')) {
                $table->string('agent_name')->nullable()->after('contact_no');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_partners', function (Blueprint $table): void {
            if (Schema::hasColumn('business_partners', 'agent_name')) {
                $table->dropColumn('agent_name');
            }
        });
    }
};
