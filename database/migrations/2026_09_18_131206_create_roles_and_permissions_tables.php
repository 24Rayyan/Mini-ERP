<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique(); // Super Admin, Admin, Finance
                $table->string('display_name');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Tabel Modules / Permissions
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique(); // e.g., 'invoices', 'users', 'expenses'
                $table->string('name');
                $table->timestamps();
            });
        }

        // Tabel Pivot Grant Modul ke Role
        if (!Schema::hasTable('role_has_modules')) {
            Schema::create('role_has_modules', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('module_id')->constrained()->onDelete('cascade');
                $table->primary(['role_id', 'module_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('role_has_modules');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('roles');
    }
};