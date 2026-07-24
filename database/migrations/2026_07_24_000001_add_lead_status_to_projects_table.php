<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah opsi status 'LEAD' (Penawaran) pada enum projects.status.
     * Catatan: DB lokal/hosting diisi dari dump produksi, perubahan skema di-apply
     * lewat delta SQL (database/sql/2026_07_24_add_lead_status_to_projects.sql).
     * File migrasi ini hanya untuk catatan repo agar skema kanonik konsisten.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `projects` MODIFY COLUMN `status` ENUM('LEAD','WAITING','PROGRESS','FINISHED','CANCELLED') NOT NULL DEFAULT 'WAITING'");
    }

    public function down(): void
    {
        // Kembalikan proyek berstatus LEAD ke WAITING agar enum bisa diciutkan tanpa error.
        DB::statement("UPDATE `projects` SET `status` = 'WAITING' WHERE `status` = 'LEAD'");
        DB::statement("ALTER TABLE `projects` MODIFY COLUMN `status` ENUM('WAITING','PROGRESS','FINISHED','CANCELLED') NOT NULL DEFAULT 'WAITING'");
    }
};
