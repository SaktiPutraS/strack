-- ============================================================
-- REFACTOR: Hilangkan tabel `budgets`, pindahkan month+year ke budget_items
-- Jalankan di phpMyAdmin untuk database strack
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Langkah 1: Tambah kolom month dan year ke budget_items
ALTER TABLE `budget_items`
    ADD COLUMN `month` int(11) NOT NULL DEFAULT 1 AFTER `id`,
    ADD COLUMN `year`  int(11) NOT NULL DEFAULT 2026 AFTER `month`;

-- Langkah 2: Isi month dan year dari tabel budgets via JOIN
UPDATE `budget_items` bi
INNER JOIN `budgets` b ON bi.budget_id = b.id
SET bi.month = b.month,
    bi.year  = b.year;

-- Langkah 3: Hapus foreign key constraint
ALTER TABLE `budget_items` DROP FOREIGN KEY `budget_items_budget_id_foreign`;

-- Langkah 4: Hapus index budget_id
ALTER TABLE `budget_items` DROP INDEX `budget_items_budget_id_foreign`;

-- Langkah 5: Hapus kolom budget_id
ALTER TABLE `budget_items` DROP COLUMN `budget_id`;

-- Langkah 6: Tambah index untuk performa query
ALTER TABLE `budget_items` ADD INDEX `idx_year_month` (`year`, `month`);

-- Langkah 7: Hapus tabel budgets
DROP TABLE IF EXISTS `budgets`;

SET FOREIGN_KEY_CHECKS = 1;

-- Verifikasi hasil:
-- SELECT * FROM budget_items LIMIT 5;
-- SHOW COLUMNS FROM budget_items;
