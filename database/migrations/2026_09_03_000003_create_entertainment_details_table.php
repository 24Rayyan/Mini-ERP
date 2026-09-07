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
        Schema::create('entertainment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->date('event_date');
            $table->string('location'); // Tempat / Restoran / Hotel
            $table->string('attendee_name'); // Nama Pihak Ketiga / Relasi yang Dijamu
            $table->string('attendee_company'); // Nama Perusahaan / Instansi Relasi
            $table->string('attendee_position'); // Jabatan Relasi
            $table->text('purpose'); // Tujuan / Jenis Usaha / Bentuk Jamuan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entertainment_details');
    }
};
