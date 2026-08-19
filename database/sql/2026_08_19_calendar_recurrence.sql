-- Kalender: agenda & todo berulang (setiap hari / hari kerja / mingguan / bulanan / tahunan).
-- Terapkan di hosting via phpMyAdmin atau ssh, lalu catat ke tabel migrations.
--
-- Aman diulang? TIDAK. ALTER TABLE akan error "Duplicate column name" kalau dijalankan dua kali.
-- Jalankan SEKALI saja.

-- 1. Kolom aturan pengulangan di calendar_events.
--    repeat_type NULL = sekali jalan (perilaku lama, semua baris lama otomatis begini).
ALTER TABLE `calendar_events`
  ADD COLUMN `repeat_type` VARCHAR(20) NULL DEFAULT NULL AFTER `completed_at`,
  ADD COLUMN `repeat_interval` SMALLINT UNSIGNED NOT NULL DEFAULT 1 AFTER `repeat_type`,
  ADD COLUMN `repeat_days` VARCHAR(20) NULL DEFAULT NULL AFTER `repeat_interval`,
  ADD COLUMN `repeat_day_of_month` SMALLINT NULL DEFAULT NULL AFTER `repeat_days`,
  ADD COLUMN `repeat_until` DATE NULL DEFAULT NULL AFTER `repeat_day_of_month`;

-- 2. Centang selesai per tanggal kemunculan (todo berulang).
CREATE TABLE IF NOT EXISTS `calendar_event_completions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `event_id` BIGINT UNSIGNED NOT NULL,
  `occurrence_date` DATE NOT NULL,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cec_event_date_unique` (`event_id`, `occurrence_date`),
  CONSTRAINT `calendar_event_completions_event_id_foreign`
    FOREIGN KEY (`event_id`) REFERENCES `calendar_events` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Catat migrasi supaya `php artisan migrate:status` tetap sinkron.
--    Ganti nomor batch sesuai batch terakhir + 1 (per 2026-08-19: batch terakhir = 8).
INSERT INTO `migrations` (`migration`, `batch`)
VALUES ('2026_08_19_000002_add_recurrence_to_calendar_events', 9);
