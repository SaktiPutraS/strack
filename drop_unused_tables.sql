-- ============================================================
-- Script: Hapus tabel yang tidak digunakan
-- Fitur dihapus: Saktify (Prospek) & Urfav (Shopee/Jakmall)
-- Tanggal: 2026-03-03
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `prospects`;
DROP TABLE IF EXISTS `urfav_shopee_products`;

SET FOREIGN_KEY_CHECKS = 1;

-- Verifikasi (opsional, jalankan terpisah):
-- SHOW TABLES LIKE 'prospects';
-- SHOW TABLES LIKE 'urfav_shopee_products';
