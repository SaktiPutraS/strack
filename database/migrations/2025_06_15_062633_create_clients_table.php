<?php
// database/migrations/2024_01_01_000001_create_clients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('phone');
            $table->index('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clients');
    }
};
