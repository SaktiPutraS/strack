-- Delta: hapus objek DB sisa integrasi Midtrans (fitur dibatalkan).
-- Dijalankan manual di hosting via ssh saktify / phpMyAdmin.
-- Aman: hanya menyentuh objek yang khusus dibuat untuk Midtrans.

-- 1. Buang kolom payment_status di projects (hanya dipakai oleh alur Midtrans).
ALTER TABLE `projects` DROP COLUMN `payment_status`;

-- 2. Buang tabel tagihan/QRIS Midtrans.
DROP TABLE IF EXISTS `payment_requests`;

-- 3. Hapus catatan migrasi terkait agar tabel migrations konsisten.
DELETE FROM `migrations` WHERE `migration` IN (
    '2026_06_10_000001_create_payment_requests_table',
    '2026_06_10_000002_add_payment_status_to_projects_table'
);
