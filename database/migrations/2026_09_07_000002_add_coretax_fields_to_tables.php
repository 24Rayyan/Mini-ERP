<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambahan kolom Coretax pada tabel settings
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'company_npwp16')) {
                $table->string('company_npwp16', 16)->nullable()->after('company_npwp');
            }
            if (!Schema::hasColumn('settings', 'company_nitku')) {
                $table->string('company_nitku', 22)->default('0000000000000000000000')->after('company_npwp16');
            }
        });

        // 2. Tambahan kolom Coretax pada tabel customers
        Schema::table('customers', function (Blueprint $table) {
            if (!Schema::hasColumn('customers', 'tax_id_type')) {
                $table->string('tax_id_type', 10)->default('NPWP16')->after('name'); // NPWP16, NIK, PASPOR
            }
            if (!Schema::hasColumn('customers', 'tax_id_number')) {
                $table->string('tax_id_number', 50)->nullable()->after('tax_id_type');
            }
            if (!Schema::hasColumn('customers', 'nitku')) {
                $table->string('nitku', 22)->default('0000000000000000000000')->after('tax_id_number');
            }
            if (!Schema::hasColumn('customers', 'default_tax_transaction_code')) {
                $table->string('default_tax_transaction_code', 10)->default('040')->after('nitku'); // 040, 020, 010, dll.
            }
        });

        // 3. Tambahan kolom Coretax pada tabel documents
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'tax_transaction_code')) {
                $table->string('tax_transaction_code', 10)->default('040')->after('discount');
            }
            if (!Schema::hasColumn('documents', 'tax_invoice_date')) {
                $table->date('tax_invoice_date')->nullable()->after('tax_transaction_code');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['company_npwp16', 'company_nitku']);
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['tax_id_type', 'tax_id_number', 'nitku', 'default_tax_transaction_code']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['tax_transaction_code', 'tax_invoice_date']);
        });
    }
};
