<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bank_balance', function (Blueprint $table) {
            $table->id();
            $table->decimal('initial_balance', 15, 2)->default(0);
            $table->decimal('current_balance', 15, 2)->default(0);
            $table->date('last_updated');
            $table->timestamps();
        });

        // Insert initial record
        DB::table('bank_balance')->insert([
            'initial_balance' => 0,
            'current_balance' => 0,
            'last_updated' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('bank_balance');
    }
};
