-- =====================================================================
-- Delta skema: Fitur Pembayaran Otomatis (Midtrans)
-- Tanggal: 2026-06-10
-- Pakai sekali saja. Aman untuk MySQL 8 (Laragon lokal) & MariaDB (hosting).
-- Cara pakai di hosting: buka phpMyAdmin > pilih database > tab SQL > paste file ini > Go.
-- =====================================================================

-- 1) Tabel tagihan / payment link
CREATE TABLE IF NOT EXISTS `payment_requests` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `order_id` varchar(255) NOT NULL,
  `gateway` varchar(255) NOT NULL DEFAULT 'midtrans',
  `amount` decimal(15,2) NOT NULL,
  `status` enum('PENDING','PAID','EXPIRED','FAILED','CANCELLED') NOT NULL DEFAULT 'PENDING',
  `payment_url` varchar(255) DEFAULT NULL,
  `snap_token` varchar(255) DEFAULT NULL,
  `gateway_ref` varchar(255) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `raw_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payment_requests_order_id_unique` (`order_id`),
  KEY `payment_requests_status_index` (`status`),
  KEY `payment_requests_project_id_status_index` (`project_id`,`status`),
  CONSTRAINT `payment_requests_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Kolom status pembayaran di projects (UNPAID / PARTIAL / PAID)
ALTER TABLE `projects`
  ADD COLUMN `payment_status` enum('UNPAID','PARTIAL','PAID') NOT NULL DEFAULT 'UNPAID' AFTER `status`;

-- 3) Isi nilai awal berdasarkan pembayaran yang sudah tercatat
UPDATE `projects` SET `payment_status` = 'PAID'    WHERE `paid_amount` >= `total_value` AND `total_value` > 0;
UPDATE `projects` SET `payment_status` = 'PARTIAL' WHERE `paid_amount` > 0 AND `paid_amount` < `total_value`;
UPDATE `projects` SET `payment_status` = 'UNPAID'  WHERE `paid_amount` <= 0;

-- 4) Tandai migrasi sebagai sudah dijalankan (agar konsisten dengan file migrasi Laravel)
INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_06_10_000001_create_payment_requests_table', 2),
('2026_06_10_000002_add_payment_status_to_projects_table', 2);
