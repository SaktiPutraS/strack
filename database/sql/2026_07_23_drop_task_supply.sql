-- Delta SQL: hapus fitur Tugas (Task) & Perlengkapan (Supply)
-- Tanggal: 2026-07-23
-- Konteks: fitur Tugas + Perlengkapan + area karyawan (role 'user') dihapus dari aplikasi.
--          Jalankan manual via phpMyAdmin / mysql CLI. JANGAN lewat `php artisan migrate`.
--
-- Urutan drop mengikuti foreign key (anak dulu, lalu induk):
--   supply_usages.supply_id  -> supplies.id      (ON DELETE CASCADE)
--   task_assignments.task_id -> tasks.id         (ON DELETE CASCADE)
-- Tidak ada tabel lain yang mereferensi keempat tabel ini.

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `supply_usages`;
DROP TABLE IF EXISTS `supplies`;
DROP TABLE IF EXISTS `task_assignments`;
DROP TABLE IF EXISTS `tasks`;

SET FOREIGN_KEY_CHECKS = 1;

-- Opsional (kebersihan tabel migrations): hapus catatan migrasi tasks bila ada.
DELETE FROM `migrations` WHERE `migration` = '2024_01_01_000001_create_tasks_table';
