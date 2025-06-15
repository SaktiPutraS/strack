<?php
// database/migrations/2024_01_01_000005_create_testimonials_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->onDelete('cascade');
            $table->text('content');
            $table->tinyInteger('rating')->default(5)->comment('Rating 1-5 stars');
            $table->boolean('is_published')->default(false);
            $table->string('client_photo')->nullable(); // Path ke foto klien jika ada
            $table->timestamps();

            // Indexes
            $table->index('is_published');
            $table->index('rating');
        });
    }

    public function down()
    {
        Schema::dropIfExists('testimonials');
    }
};
