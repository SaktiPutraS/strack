<?php
// database/migrations/2024_01_01_000002_create_projects_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['HTML/PHP', 'LARAVEL', 'WORDPRESS', 'REACT', 'VUE', 'FLUTTER', 'MOBILE', 'OTHER']);
            $table->decimal('total_value', 15, 2);
            $table->decimal('dp_amount', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->enum('status', ['WAITING', 'PROGRESS', 'FINISHED', 'CANCELLED'])->default('WAITING');
            $table->date('deadline');
            // REMOVED: has_testimonial column
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index('deadline');
            $table->index(['client_id', 'status']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
