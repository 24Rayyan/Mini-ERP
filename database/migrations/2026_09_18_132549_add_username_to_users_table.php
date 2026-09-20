<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username')->unique()->nullable()->after('name');
            }
        });

        // Set username default untuk user yang sudah ada agar tidak NULL
        DB::table('users')->whereNull('username')->get()->each(function ($user) {
            $generatedUsername = strtolower(explode(' ', trim($user->name))[0]) . $user->id;
            DB::table('users')->where('id', $user->id)->update(['username' => $generatedUsername]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};