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
        Schema::create('pollings', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description');
            $table->enum('category', ['umum', 'keamanan', 'kebersihan', 'keuangan', 'fasilitas', 'kegiatan', 'lainnya'])->default('umum');
            $table->datetime('end_date');
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('anonymous')->default(false);
            $table->boolean('notify_result')->default(true);
            $table->enum('status', ['active', 'ended', 'draft'])->default('active');
            $table->string('rt', 2);
            $table->string('rw', 2);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->index(['status', 'end_date']);
            $table->index(['status', 'rt', 'rw']);
            $table->index('user_id');
            $table->index(['rt', 'rw']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pollings');
    }
};