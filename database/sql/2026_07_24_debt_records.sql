-- Delta skema hosting: modul Catatan Hutang Piutang.
-- Dua tabel: debt_records (catatan hutang/piutang) + debt_payments (riwayat pembayaran).
-- Jalankan via phpMyAdmin hosting (atau: mysql -u$US $DB < file.sql). Hanya menambah tabel, aman.

CREATE TABLE IF NOT EXISTS `debt_records` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `type` ENUM('HUTANG','PIUTANG') NOT NULL,
  `party_name` VARCHAR(255) NOT NULL,
  `title` VARCHAR(255) NULL,
  `principal_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `paid_amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `status` ENUM('ONGOING','PAID') NOT NULL DEFAULT 'ONGOING',
  `due_date` DATE NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `debt_records_type_index` (`type`),
  KEY `debt_records_status_index` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `debt_payments` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `debt_record_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(15,2) NOT NULL DEFAULT 0,
  `payment_date` DATE NOT NULL,
  `notes` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `debt_payments_debt_record_id_index` (`debt_record_id`),
  CONSTRAINT `debt_payments_debt_record_id_foreign`
    FOREIGN KEY (`debt_record_id`) REFERENCES `debt_records` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
