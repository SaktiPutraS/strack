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
- Tooling lokal: mysql di `C:\laragon\bin\mysql\...\mysql.exe`. PHP: JANGAN pakai `C:\xampp\php\php.exe`
  (versi 8.1.17, gagal platform check karena project butuh >= 8.2). Pakai PHP 8.2 Laragon:
  `C:\laragon\bin\php\php-8.2.26-nts-Win32-vs16-x64\php.exe` untuk artisan/lint/view:cache.

## Deploy ke hosting (Hostinger)
- Hosting = clone git dari remote yang sama (`SaktiPutraS/strack`, branch `main`) di
  `~/domains/strack.my.id/public_html`. SSH: `ssh saktify` (user `u137841455`, PHP 8.2.30).
- Alur deploy: commit lokal -> `git push origin main` -> di hosting `git pull` + bersihkan cache.
  Skrip siap pakai: `scripts/deploy.ps1` (contoh: `./scripts/deploy.ps1 -Message "pesan"`).
  Skrip otomatis `git checkout -- composer.lock` di hosting (ada modifikasi kosmetik `[]`->`{}` yang
  bikin pull menolak) lalu `git pull --ff-only`, `php artisan optimize:clear`, `view:cache`.
- Untuk perubahan SKEMA DB tetap manual (delta SQL via phpMyAdmin), BUKAN lewat skrip ini.
- Catatan: di hosting ada file untracked yg belum masuk repo: `resources/views/errors/`
  (halaman error kustom 403/404/500/503/maintenance), `Logo.png`, `public/logo_saktify.png`.
  Aman dari `git pull`, tapi sebaiknya suatu saat di-commit agar terkelola git.

## Riwayat Sesi

### 2026-08-12 (hapus total integrasi Midtrans)
Fitur pembayaran otomatis Midtrans DIBATALKAN dan dihapus seluruhnya dari kode + DB produksi. Commit
`4959ca6`, sudah push + deploy + drop objek DB via ssh. Detail di DOKUMENTASI.md. Maintenance dianggap
SOLVE (tak diubah). Model `Payment`/`PaymentController` (Pendapatan) TETAP ada, hanya bagian Midtrans
yang dicabut.
- DIHAPUS: `MidtransService`, `BillingController`, `PaymentWebhookController`, model `PaymentRequest`,
  2 migrasi `2026_06_10_*`, `payment_gateway.sql`, folder `docs/midtrans/`. Route webhook + charge,
  blok config `midtrans`, CSRF-except `webhooks/*`, `MIDTRANS_*` di `.env.example`, kolom+relasi+accessor
  `payment_status` di Project, sync payment_status di boot Payment, tombol/modal/JS "Tagih Klien" +
  badge di `projects/{show,index}.blade.php`.
- DB: delta `database/sql/2026_08_12_drop_midtrans.sql` (drop kolom `projects.payment_status` + tabel
  `payment_requests` + 2 baris migrations). URUTAN WAJIB: deploy KODE dulu baru drop DB (kalau dibalik,
  boot Payment lama nulis payment_status -> "Unknown column"). Verifikasi hilang + smoke test 200/302 OK.
- Orphan aman: `.env` lokal & hosting masih ada key `MIDTRANS_*` (tak dipakai). Semua pending Midtrans
  lama (aktivasi production, webhook URL, regenerate server key) kini TIDAK RELEVAN.

### 2026-07-24 (status Penawaran + modul Hutang Piutang + Maintenance checklist)
Tiga pekerjaan besar, SEMUA commit+push+deploy+delta SQL ke hosting + migrasi dicatat. Detail di
DOKUMENTASI.md. DB lokal `strack` TETAP tidak ada (uji manual di hosting oleh user). Migrasi hosting
s/d batch 6. Commits: `4e49e49`,`3ae1aa8`,`f905e7e`,`f46fb9e`,`8488433`,`2f102d7`,`31c7352`,`ffcffcb`.
- STATUS PENAWARAN (LEAD) `4e49e49`,`3ae1aa8`: enum `projects.status` +LEAD (label "Penawaran", abu-abu)
  untuk proyek BELUM DEAL. CANCELLED+LEAD DIKECUALIKAN dari perhitungan penjualan: Nilai Bulan Ini
  (ProjectController), grafik bulanan (DashboardController), Laporan Penjualan (FinancialReportController),
  total nilai per klien (Client model). Pendapatan (Payment) & piutang tak terpengaruh. Bisa dipilih saat
  create/edit + tombol "Deal" (LEAD->WAITING). Kartu statistik dirapikan jadi 1 baris.
- CATATAN HUTANG PIUTANG `f905e7e`,`f46fb9e`,`8488433`: menu baru; tabel `debt_records`+`debt_payments`
  (auto-sync paid+status, pola Project/Payment). Keputusan: TERPISAH dari Total Asset dashboard, TOLAK
  kelebihan bayar, due date OPSIONAL (saklar). Ringkasan piutang/hutang/selisih + riwayat pembayaran.
- MAINTENANCE (checklist) `2f102d7`,`31c7352`,`ffcffcb`: menu baru; `maintenance_tasks`+`maintenance_logs`.
  Jadwal fleksibel (Catatan/Tanggal/Bulan MULTI/Tahun/Odometer). Status otomatis DUE/SCHEDULED/DONE, tombol
  Tandai Selesai -> riwayat; MONTH auto-reset tiap siklus. ODOMETER: interval+last_km->next_km TAPI tak bisa
  auto-DUE (tak ada km live) -> selalu Terjadwal. Keputusan: satu daftar+filter status, histori lengkap.
- DELTA SQL baru (di `database/sql/`, sudah diterapkan hosting + migrasi dicatat): `add_lead_status_to_projects`
  (b3), `debt_records` (b4), `maintenance_tasks` (b5), `maintenance_todo` (b6).
- PENDING: user uji manual; odometer field "km sekarang" utk auto-DUE; multi-pilih Tahun (opsional);
  commit `docs/midtrans/*`+`Claude-strack.bat`; aktivasi Midtrans production.

### 2026-07-23 (tombol konfirmasi + hapus fitur Tugas & Perlengkapan + area karyawan)
Dua pekerjaan, keduanya sudah commit + push + deploy ke hosting. Detail di DOKUMENTASI.md.
- `7152cd9`: tombol "Konfirmasi Selesai ke Client" di `projects/show.blade.php` disembunyikan
  saat status proyek masih `WAITING` (belum dimulai). Cukup tambah `&& status !== 'WAITING'`.
- `7c1db26`: HAPUS TOTAL fitur Tugas (Task) + Perlengkapan (Supply) + seluruh area login karyawan
  (role `user`), karena sudah tidak dipakai. Dihapus: controller Task/Supply, model
  Task/TaskAssignment/Supply/SupplyUsage, views `tasks/` & `supplies/` & `dashboard/index-user`,
  route tasks/tasks-user/dashboard-user/supplies, method `DashboardController::userIndex`, menu
  sidebar terkait, dan cabang login PIN karyawan (`120906`) di `SimpleLoginController`. Non-admin
  kini diarahkan ke route `login`. Lint bersih, `view:cache` OK, `route:list` sudah tak ada.
- DB HOSTING (`u137841455_ZzLVP`): 4 tabel di-drop via delta `database/sql/2026_07_23_drop_task_supply.sql`
  (tasks=4, task_assignments=251, supplies=22, supply_usages=45 baris). Terverifikasi hilang.
  User sudah punya backup export di folder Downloads (`u137841455_ZzLVP.sql`). Smoke test OK
  (login 200, root 302).
- DB LOKAL: database `strack` TIDAK ADA saat ini (Laragon jalan tapi belum diimpor). Tidak ada yang
  di-drop lokal; delta SQL siap dipakai bila DB lokal diimpor ulang.
- CATATAN TEKNIS (penting untuk SSH+MySQL hosting): transport PowerShell -> `ssh saktify` MENGHAPUS
  semua kutip-ganda `"` dari perintah, dan heredoc `<<'PHP'` gagal (exit 255). Cara yang jalan:
  tanpa kutip-ganda sama sekali, password via `export MYSQL_PWD=$(...)`, SQL pakai kutip-tunggal /
  identifier / jalankan file (`mysql -u$US $DB < file.sql`). Ambil kredensial dari `.env` hosting.

### 2026-07-22 (verifikasi sinkron hosting + workflow deploy + sidebar + dokumen Midtrans)
Sudah commit + push + deploy: `c17d357`. Detail di DOKUMENTASI.md.
- Verifikasi `D:\strack` == hosting: keduanya di commit sama, git-based. (Koreksi: pekerjaan kalender
  2026-06-18 ternyata SUDAH di-commit `d6c9a3e`, bukan "belum".)
- Buat workflow deploy `scripts/deploy.ps1` (lihat bagian Deploy di atas), diuji end-to-end OK.
- Rapikan sidebar (`resources/views/layouts/app.blade.php`): ikon lebar tetap agar label sejajar,
  skala font sub-menu konsisten + garis pemandu, state aktif (accent bar), caret grup berputar,
  footer logout menempel ke bawah. Tetap tema ungu+glass, no AI slop.
- Dokumen onboarding Midtrans (data susulan): `docs/midtrans/flow-transaksi.html` + `img/` +
  `Flow-Transaksi-Saktify.pdf` (6 langkah, pemesanan s/d checkout QRIS). Info usaha: Saktify,
  admin@saktify.com, saktify.com; strack.my.id = sistem penagihan internal.
- TEMUAN: key Midtrans di `.env` LOKAL = key SANDBOX (akun ini pakai prefix `Mid-` untuk sandbox,
  bukan `SB-Mid-`) + IS_PRODUCTION=false -> link sandbox bisa dibuat. Production masih "No payment
  channels available" (akun belum aktivasi).
- BELUM DI-COMMIT: `docs/midtrans/*`, CLAUDE.md, DOKUMENTASI.md (log sesi ini).

### 2026-06-18 (perbaikan catatan pribadi kalender) - SUDAH DI-COMMIT (d6c9a3e)
Fix bug "Catatan Pribadi" di kalender dashboard. Detail di DOKUMENTASI.md. User mau uji visual
dulu sebelum commit; kalau OK -> commit + push. Lint PHP bersih, `view:cache` sukses.
- BUG UTAMA: setelah buat catatan, kalender kosong total (proyek ikut hilang) sampai reload.
  Sebab: `CalendarNote::getNotesForMonth()` me-`keyBy(day)` -> JSON jadi OBJEK, bukan array.
  Load awal mengonversi via `Object.values`, tapi `loadCalendarData` (AJAX) langsung assign ->
  `calendarNotes.filter` error -> `renderCalendar` throw setelah `calendarBody.innerHTML=''`.
  Fix: normalkan `data.notes` ke array di `loadCalendarData`.
- Sel/mingguan hanya menampilkan jumlah ("N catatan"), bukan judul. Sekarang tampilkan JUDUL
  (escapeHtml + elipsis). Modal baca `note.content` (dulu `note.description` yg selalu kosong).
- Field deskripsi putus end-to-end: form kirim `description`, controller simpan `content`. Disatukan
  ke `content` (kolom DB): JS kirim `content`, model ekspos `content`, modal baca `note.content`.
- Tanggal catatan diserialisasi `Y-m-d` (dulu ISO datetime) di `getNotesForMonth` -> cocok untuk
  perbandingan string di mingguan & modal (`note.date === 'Y-m-d'`).
- `editNote(id)` kini cari data dari `calendarNotes` (tak lagi oper title/content lewat onclick).
- File: `app/Models/CalendarNote.php`, `resources/views/dashboard/index.blade.php`.

### 2026-06-11 (perbaikan dashboard + tombol konfirmasi selesai)
Sudah commit + push ke `main` (`0adbc26`, `c28b15c`, `fd62725`). Detail di DOKUMENTASI.md.
- Bugfix grafik "Pendapatan & Pengeluaran Mingguan" yang kosong: `DashboardController.php`
  salah hitung awal tahun fiskal (cek `->month < 7` setelah `addMonths(6)` jadi tak pernah jalan).
  Fix: cek bulan `now` SEBELUM dimodifikasi.
- Kalender (`dashboard/index.blade.php`): urutan info -> nama KLIEN dulu lalu nama proyek
  (konsisten di sel, modal, mingguan, panel, tooltip). Nilai piutang jadi PUTIH (hijau tak
  terbaca di latar sel; sel kuning tetap gelap). Hapus prefiks "Rp" pada nilai/piutang kalender.
- `projects/show.blade.php`: tombol "Konfirmasi Selesai ke Client" -> buka WhatsApp klien dgn
  pesan konfirmasi penutupan proyek terisi otomatis.
- Catatan: tool commit jalan di BASH; here-string PowerShell `@'...'@` menyisipkan `@` ke pesan -
  pakai multiple `-m` biasa.

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
