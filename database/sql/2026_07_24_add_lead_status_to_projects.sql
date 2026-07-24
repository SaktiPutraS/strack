-- Delta skema hosting: tambah status 'LEAD' (Penawaran) ke enum projects.status
-- Konteks: proyek yang BELUM deal, ingin dicatat tapi TIDAK dihitung sebagai
-- penjualan / total proyek per bulan (diperlakukan seperti CANCELLED pada perhitungan nilai).
-- Jalankan via phpMyAdmin hosting (atau: mysql -u$US $DB < file.sql). Aman, tidak menyentuh data.

ALTER TABLE `projects`
  MODIFY COLUMN `status`
  ENUM('LEAD','WAITING','PROGRESS','FINISHED','CANCELLED') NOT NULL DEFAULT 'WAITING';
