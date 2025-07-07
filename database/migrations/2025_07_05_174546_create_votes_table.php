<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poll_id')->constrained('pollings')->onDelete('cascade');
            $table->foreignId('poll_option_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('rt', 2);
            $table->string('rw', 2);
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['poll_id', 'user_id']);
            $table->index(['poll_id', 'rt', 'rw']);
            $table->index(['user_id', 'poll_id']); // Untuk mencegah duplicate votes

            // Unique constraint untuk mencegah user vote multiple kali pada poll yang sama
            // (kecuali jika polling mengizinkan multiple choice)
            $table->unique(['poll_id', 'user_id', 'poll_option_id'], 'unique_user_poll_option');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};