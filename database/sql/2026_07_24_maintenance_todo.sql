-- Delta skema hosting: upgrade Maintenance jadi checklist/todo.
-- - Tambah tipe jadwal ODOMETER (servis berbasis km) ke enum.
-- - Tambah kolom last_done_at, interval_km, last_km di maintenance_tasks.
-- - Tabel baru maintenance_logs (riwayat penyelesaian, lengkap dengan odometer).
-- Jalankan via phpMyAdmin hosting (atau: mysql -u$US $DB < file.sql). Aman terhadap data.

ALTER TABLE `maintenance_tasks`
  MODIFY COLUMN `schedule_type` ENUM('TEXT','DATE','MONTH','YEAR','ODOMETER') NOT NULL DEFAULT 'TEXT',
  ADD COLUMN `last_done_at` DATE NULL AFTER `notes`,
  ADD COLUMN `interval_km` INT UNSIGNED NULL AFTER `last_done_at`,
  ADD COLUMN `last_km` INT UNSIGNED NULL AFTER `interval_km`;

CREATE TABLE IF NOT EXISTS `maintenance_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `maintenance_task_id` BIGINT UNSIGNED NOT NULL,
  `done_at` DATE NOT NULL,
  `odometer` INT UNSIGNED NULL,
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintenance_logs_task_index` (`maintenance_task_id`),
  CONSTRAINT `maintenance_logs_task_foreign`
    FOREIGN KEY (`maintenance_task_id`) REFERENCES `maintenance_tasks` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
