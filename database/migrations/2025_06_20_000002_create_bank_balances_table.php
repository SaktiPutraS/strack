<?php
// database/migrations/2025_06_20_000002_create_bank_balances_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bank_balances', function (Blueprint $table) {
            $table->id();
            $table->decimal('balance', 15, 2);
            $table->date('balance_date');
            $table->string('bank_name')->default('Bank Octo'); // BCA, Octo Pay, etc
            $table->text('notes')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['bank_name', 'balance_date']);
            $table->index('balance_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_balances');
    }
};
