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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('category', ['jual', 'jasa', 'info'])->default('info');
            $table->string('rt', 3)->nullable();
            $table->string('rw', 3)->nullable();
            $table->decimal('price', 15, 0)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->enum('status', ['active', 'hidden', 'expired'])->default('active');
            $table->integer('views_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('featured_until')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['category', 'status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index(['status', 'created_at']);
            $table->index(['is_featured', 'featured_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};