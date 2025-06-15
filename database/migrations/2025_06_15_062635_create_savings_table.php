<?php
// database/migrations/2024_01_01_000004_create_savings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->decimal('amount', 15, 2); // 10% dari payment
            $table->decimal('bank_balance', 15, 2); // Saldo Bank Octo saat ini
            $table->date('transaction_date');
            $table->text('notes')->nullable();
            $table->boolean('is_verified')->default(false); // Apakah sudah diverifikasi dengan saldo bank
            $table->timestamps();

            // Indexes
            $table->index('transaction_date');
            $table->index('is_verified');
        });
    }

    public function down()
    {
        Schema::dropIfExists('savings');
    }
};
