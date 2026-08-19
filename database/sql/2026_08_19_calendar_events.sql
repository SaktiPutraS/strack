-- Modul Kalender: tabel calendar_events (agenda + todo)
-- Terapkan di hosting via phpMyAdmin / ssh, lalu catat ke tabel migrations.
--
-- Tabel lama `calendar_notes` TIDAK di-drop (dipakai sebagai cadangan data).
-- Isinya dipindahkan ke calendar_events lewat INSERT ... SELECT di bawah.

CREATE TABLE IF NOT EXISTS `calendar_events` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(191) NOT NULL,
  `title` VARCHAR(500) NOT NULL,
  `description` TEXT NULL,
  `type` ENUM('EVENT','TODO') NOT NULL DEFAULT 'EVENT',
  `start_date` DATE NOT NULL,
  `end_date` DATE NULL,
  `start_time` TIME NULL,
  `end_time` TIME NULL,
  `all_day` TINYINT(1) NOT NULL DEFAULT 1,
  `color` VARCHAR(20) NULL,
  `is_done` TINYINT(1) NOT NULL DEFAULT 0,
  `completed_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `calendar_events_user_id_index` (`user_id`),
  KEY `calendar_events_start_date_index` (`start_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migrasi data catatan lama. Jalankan SEKALI saja (kalau diulang, data jadi dobel).
INSERT INTO `calendar_events`
  (`user_id`, `title`, `description`, `type`, `start_date`, `all_day`, `created_at`, `updated_at`)
SELECT `user_id`, `title`, `content`, 'EVENT', `date`, 1, `created_at`, `updated_at`
FROM `calendar_notes`;

-- Catat migrasi supaya `php artisan migrate:status` tetap sinkron.
-- Ganti nomor batch sesuai batch terakhir + 1 (per 2026-08-19: batch terakhir = 7).
INSERT INTO `migrations` (`migration`, `batch`)
VALUES ('2026_08_19_000001_create_calendar_events_table', 8);
