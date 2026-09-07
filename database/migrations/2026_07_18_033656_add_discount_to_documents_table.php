<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::table('documents', function (Blueprint $table) {
        // Menambahkan kolom discount setelah total_amount
        $table->decimal('discount', 15, 2)->default(0)->after('total_amount');
    });
}

public function down()
{
    Schema::table('documents', function (Blueprint $table) {
        $table->dropColumn('discount');
    });
}
};
