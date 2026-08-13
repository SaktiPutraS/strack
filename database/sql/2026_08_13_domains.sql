-- Modul Domain & Hosting: tabel domains
-- Terapkan di hosting via phpMyAdmin / ssh, lalu catat ke tabel migrations.

CREATE TABLE IF NOT EXISTS `domains` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(191) NOT NULL,
  `client_id` BIGINT UNSIGNED NULL,
  `project_id` BIGINT UNSIGNED NULL,
  `provider` VARCHAR(100) NULL,
  `registered_at` DATE NULL,
  `expires_at` DATE NULL,
  `renewal_cost` DECIMAL(15,2) NULL,
  `is_hosted` TINYINT(1) NOT NULL DEFAULT 0,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domains_name_unique` (`name`),
  KEY `domains_client_id_foreign` (`client_id`),
  KEY `domains_project_id_foreign` (`project_id`),
  CONSTRAINT `domains_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `domains_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
