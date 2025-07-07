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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', [
                'rapat',
                'gotong_royong',
                'keamanan',
                'sosial',
                'olahraga',
                'keagamaan',
                'perayaan',
                'lainnya'
            ])->nullable();
            $table->date('event_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->string('location');
            $table->text('location_detail')->nullable();
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('organizer')->nullable();
            $table->string('contact_person')->nullable();
            $table->integer('max_participants')->nullable();
            $table->text('requirements')->nullable();
            $table->boolean('is_registration_required')->default(false);
            $table->boolean('is_reminder_active')->default(true);
            $table->string('attachment')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'published', 'cancelled'])->default('published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};