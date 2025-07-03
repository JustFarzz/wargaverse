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
        Schema::create('kas_transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['income', 'expense']); // tipe transaksi
            $table->decimal('amount', 12, 2); // jumlah uang
            $table->text('description')->nullable(); // deskripsi
            $table->string('rt'); // RT terkait
            $table->string('rw'); // RW terkait
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yang membuat transaksi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_transactions');
    }
};
