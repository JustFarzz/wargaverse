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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->text('address');
            $table->string('rt', 2);
            $table->string('rw', 2);
            $table->string('password');
            $table->string('avatar')->nullable();

            // Updated enum untuk role - menambahkan admin
            $table->enum('role', ['warga', 'admin'])->default('warga');

            // Updated enum untuk status - menambahkan verified dan blocked
            $table->enum('status', ['active', 'verified', 'blocked'])->default('active');

            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->string('registered_ip')->nullable();

            // Tambahan field untuk profil lengkap
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('occupation')->nullable();
            $table->text('bio')->nullable();
            $table->string('rt_number')->nullable();
            $table->string('rw_number')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('whatsapp')->nullable();
            $table->rememberToken();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};