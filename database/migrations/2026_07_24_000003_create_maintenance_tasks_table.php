<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Modul Maintenance (catatan tugas perawatan).
     * Catatan: DB lokal/hosting diisi dari dump produksi; skema di-apply lewat delta SQL
     * (database/sql/2026_07_24_maintenance_tasks.sql). File migrasi ini untuk catatan repo.
     */
    public function up(): void
    {
        Schema::create('maintenance_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('schedule_type', ['TEXT', 'DATE', 'MONTH', 'YEAR'])->default('TEXT');
            $table->string('schedule_value')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('schedule_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_tasks');
    }
};
