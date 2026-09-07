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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique(); // Format: TRX/YYYY/MM/XXXX
            $table->enum('type', ['income', 'expense']);
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->foreignId('invoice_id')->nullable()->constrained('documents')->nullOnDelete();
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date');
            $table->string('payment_method')->default('Bank Transfer'); // Cash, Bank Transfer, e-Wallet
            $table->text('description')->nullable();
            $table->string('receipt_file_path')->nullable(); // Path upload bukti nota / struk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
