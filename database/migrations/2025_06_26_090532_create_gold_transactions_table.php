<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gold_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->enum('type', ['BUY', 'SELL']);
            $table->decimal('grams', 8, 3); // Support sampai 3 decimal places
            $table->decimal('total_price', 15, 2);
            $table->decimal('price_per_gram', 15, 2); // Auto calculated
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('transaction_date');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('gold_transactions');
    }
};
