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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // nama kegiatan
            $table->text('description')->nullable(); // deskripsi kegiatan
            $table->string('location'); // lokasi kegiatan
            $table->dateTime('event_date'); // waktu kegiatan
            $table->string('rt'); // RT kegiatan ditujukan
            $table->string('rw'); // RW kegiatan ditujukan
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pembuat kegiatan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
