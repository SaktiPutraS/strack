<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Upgrade Maintenance jadi checklist/todo: tipe ODOMETER, kolom penyelesaian,
     * dan tabel riwayat maintenance_logs.
     * Catatan: skema di-apply lewat delta SQL (database/sql/2026_07_24_maintenance_todo.sql).
     * File migrasi ini untuk catatan repo.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `maintenance_tasks` MODIFY COLUMN `schedule_type` ENUM('TEXT','DATE','MONTH','YEAR','ODOMETER') NOT NULL DEFAULT 'TEXT'");

        Schema::table('maintenance_tasks', function (Blueprint $table) {
            $table->date('last_done_at')->nullable()->after('notes');
            $table->unsignedInteger('interval_km')->nullable()->after('last_done_at');
            $table->unsignedInteger('last_km')->nullable()->after('interval_km');
        });

        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_task_id')->constrained('maintenance_tasks')->cascadeOnDelete();
            $table->date('done_at');
            $table->unsignedInteger('odometer')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_logs');

        Schema::table('maintenance_tasks', function (Blueprint $table) {
            $table->dropColumn(['last_done_at', 'interval_km', 'last_km']);
        });

        DB::statement("ALTER TABLE `maintenance_tasks` MODIFY COLUMN `schedule_type` ENUM('TEXT','DATE','MONTH','YEAR') NOT NULL DEFAULT 'TEXT'");
    }
};
