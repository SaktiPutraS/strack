# Panduan Project: strack

## Tentang
Aplikasi pencatatan keuangan/aset berbasis **Laravel 12 / PHP 8.2**.
Mengelola pengeluaran, budget, invoice/quotation, transaksi emas, klien, proyek,
transfer bank, penarikan tunai, dan dashboard ringkasan aset.

## Stack & Catatan Teknis
- Laravel 12, PHP ^8.2
- Excel import/export: `maatwebsite/excel` + `phpoffice/phpspreadsheet`
- Frontend: Blade + Bootstrap 5 (Bootstrap Icons), SweetAlert2 untuk dialog
- Tema warna utama: ungu (`text-purple` / rgba(139,92,246,...)), merah untuk Bank Octo
- Sumber dana pengeluaran: `BANK` (Bank Octo) atau `CASH`

## Konvensi
- Bahasa UI: Indonesia
- JANGAN gunakan em dash / en dash di output mana pun (lihat instruksi global)
- Pesan flash sukses pakai key `success`

## Controller Utama
`app/Http/Controllers/` - Expense, Budget, ProjectInvoice, GoldTransaction,
Client, Project, BankTransfer, CashWithdrawal, Payment, Dashboard, FinancialReport, dll.

---

## Catatan DB lokal (penting)
- DB lokal `strack` (Laragon MySQL, user root tanpa password) diisi dari IMPOR dump produksi,
  bukan dari `php artisan migrate`. Akibatnya tabel `migrations` tidak sinkron: ada migrasi
  "Pending" yang tabelnya sudah ada (mis. create_tasks_table). JANGAN jalankan `php artisan migrate`
  mentah - akan gagal "table already exists". Untuk perubahan skema: pakai SQL langsung + catat
  manual ke tabel `migrations`. Dump produksi terbaru ada di Downloads (mis. u137841455_*.sql).
- Produksi biasanya lebih baru dari local. Untuk apply skema ke hosting, pakai file delta di
  `database/sql/*.sql` lewat phpMyAdmin (jangan replace seluruh DB).
- Tooling lokal: mysql di `C:\laragon\bin\mysql\...\mysql.exe`, php di `C:\xampp\php\php.exe`.

## Riwayat Sesi

### 2026-06-10 (testing & aktivasi Midtrans)
Webhook auto-Lunas sudah DIUJI end-to-end di lokal dan LOLOS (settlement -> Lunas, idempotent,
tolak signature palsu). Sandbox: link Rp1.000 berhasil. Production: link berhasil dibuat tapi
"No payment channels available" karena akun production BELUM diaktivasi. Penghalang sekarang
murni aktivasi akun + QRIS/VA di Midtrans (bukan kode). Pending lengkap (deploy hosting,
regenerate server key, cara deploy Hostinger) ada di DOKUMENTASI.md.

### 2026-06-10 (lanjutan) - Fitur Pembayaran Otomatis (Midtrans)
Tombol "Tagih Klien" -> QRIS/payment link Midtrans (Snap sandbox) -> kirim WA -> webhook
verifikasi signature -> projects.payment_status auto jadi PAID + catat Payment. Detail +
langkah pending (isi MIDTRANS_*, set webhook URL, apply delta SQL ke hosting) di DOKUMENTASI.md.

### 2026-06-10
Fokus: UX form pengeluaran + redesain kartu aset dashboard. (Detail di DOKUMENTASI.md)
- Tambah tombol "Simpan & Lanjut" pada form create pengeluaran (input cepat beruntun).
- Redesain kartu "Total Asset" dashboard: Bank Octo & Piutang dinaikkan jadi "Saldo Utama".
- Status: belum di-commit, menunggu pengecekan/uji oleh user.
