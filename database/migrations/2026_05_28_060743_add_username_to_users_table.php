<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
        });

        DB::table('users')
            ->whereNull('username')
            ->orderBy('id')
            ->get(['id', 'name', 'email'])
            ->each(function (object $user): void {
                $base = str($user->name ?: $user->email)
                    ->lower()
                    ->replaceMatches('/[^a-z0-9]+/', '.')
                    ->trim('.')
                    ->limit(40, '')
                    ->value() ?: 'user';

                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['username' => $base.'.'.$user->id]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
