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
        Schema::create('document_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->string('notes')->nullable(); // Column: Keterangan
            
            // Mengubah tipe 'qty' dari integer menjadi decimal agar mendukung angka koma (misal: 25.5)
            $table->decimal('qty', 10, 2); 
            
            $table->string('unit')->default('Pcs'); // Column: Satuan (Pcs, Unit, Box, Titik, Paket, dsb)
            $table->decimal('price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_items');
    }
};