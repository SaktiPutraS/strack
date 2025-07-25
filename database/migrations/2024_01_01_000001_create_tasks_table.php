<?php
// database/migrations/2024_01_01_000001_create_tasks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->enum('schedule', ['daily', 'weekly', 'monthly']);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('admin_id');
            $table->timestamps();

            $table->index(['status', 'schedule']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
