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
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['income', 'expense']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('category', [
                'iuran',
                'donasi',
                'bantuan',
                'lainnya_masuk',
                'keamanan',
                'infrastruktur',
                'acara',
                'operasional',
                'lainnya_keluar'
            ]);
            $table->date('transaction_date');
            $table->enum('payment_method', ['cash', 'transfer', 'ewallet', 'check'])->default('cash');
            $table->boolean('is_recurring')->default(false);
            $table->text('notes')->nullable();
            $table->json('attachments')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
