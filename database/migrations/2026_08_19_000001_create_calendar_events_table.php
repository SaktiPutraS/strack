<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();
            $table->string('user_id')->index();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->enum('type', ['EVENT', 'TODO'])->default('EVENT');
            $table->date('start_date')->index();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('all_day')->default(true);
            $table->string('color', 20)->nullable();
            $table->boolean('is_done')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

        // Pindahkan catatan lama dari calendar_notes (kalau tabelnya ada).
        if (Schema::hasTable('calendar_notes')) {
            DB::statement("
                INSERT INTO calendar_events
                    (user_id, title, description, type, start_date, all_day, created_at, updated_at)
                SELECT user_id, title, content, 'EVENT', date, 1, created_at, updated_at
                FROM calendar_notes
            ");
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};
