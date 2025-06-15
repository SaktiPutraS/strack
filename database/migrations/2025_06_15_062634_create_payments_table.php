<?php
// database/migrations/2024_01_01_000003_create_payments_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->enum('payment_type', ['DP', 'INSTALLMENT', 'FULL', 'FINAL']);
            $table->date('payment_date');
            $table->text('notes')->nullable();
            $table->string('payment_method')->nullable(); // Transfer, Cash, E-wallet, etc.
            $table->timestamps();

            // Indexes
            $table->index('payment_date');
            $table->index(['project_id', 'payment_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
