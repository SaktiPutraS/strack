<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bank_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained('payments')->onDelete('cascade');
            $table->date('transfer_date');
            $table->decimal('transfer_amount', 15, 2); // Bisa berbeda dari payment amount karena fee
            $table->string('reference_number')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('transfer_date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bank_transfers');
    }
};
