-- Delta skema hosting: modul Maintenance (catatan tugas perawatan: AC, Motor, Filter Air, dll).
-- Jadwal fleksibel: TEXT (catatan bebas), DATE (tanggal), MONTH (bulan), YEAR (tahun).
-- Nilai jadwal disimpan sebagai string ternormalisasi di schedule_value.
-- Jalankan via phpMyAdmin hosting (atau: mysql -u$US $DB < file.sql). Hanya menambah tabel, aman.

CREATE TABLE IF NOT EXISTS `maintenance_tasks` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `schedule_type` ENUM('TEXT','DATE','MONTH','YEAR') NOT NULL DEFAULT 'TEXT',
  `schedule_value` VARCHAR(255) NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintenance_tasks_schedule_type_index` (`schedule_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
