# Dokumentasi Sesi - strack

Log pekerjaan per sesi. Sesi terbaru di atas.

---

## Sesi 2026-08-12 (hapus total integrasi Midtrans)

### Ringkasan
User membatalkan fitur pembayaran otomatis Midtrans. SELURUH konsep Midtrans dihapus dari kode DAN
dari DB produksi. Sudah commit + push + deploy ke hosting + drop objek DB via ssh. Commit `4959ca6`.
Fitur Maintenance dianggap SOLVE (tidak ada perubahan). Model `Payment`/`PaymentController` (fitur
Pendapatan yang sah) TIDAK ikut dihapus, hanya bagian Midtrans-nya yang dicabut.

### File dihapus
- `app/Services/Payment/MidtransService.php`, `app/Http/Controllers/BillingController.php`,
  `app/Http/Controllers/PaymentWebhookController.php`, `app/Models/PaymentRequest.php`.
- Migrasi `2026_06_10_000001_create_payment_requests_table.php` +
  `2026_06_10_000002_add_payment_status_to_projects_table.php`.
- `database/sql/2026_06_10_payment_gateway.sql`.
- Folder untracked `docs/midtrans/` (html + img + pdf onboarding Midtrans).

### File diedit (bagian Midtrans dibuang, fitur lain utuh)
- `routes/web.php`: buang import Billing/PaymentWebhookController, route webhook `webhooks/payment/midtrans`,
  route `projects/{project}/charge`.
- `config/services.php`: hapus blok `midtrans`. `bootstrap/app.php`: hapus `validateCsrfTokens(except webhooks/*)`.
- `.env.example`: hapus 4 baris `MIDTRANS_*`.
- `app/Models/Project.php`: buang `payment_status` dari fillable + relasi `paymentRequests()` +
  `activePaymentRequest()` + `syncPaymentStatus()` + accessor `payment_status_label/color`.
- `app/Models/Payment.php`: boot dikembalikan hanya menghitung `paid_amount` (tanpa sync payment_status).
- `resources/views/projects/show.blade.php`: buang badge status bayar di header, tombol "Tagih Klien (QRIS)",
  modal tagihan, dan JS `tagihKlien()`/`copyTagihLink()`.
- `resources/views/projects/index.blade.php`: buang badge `payment_status`.

### DB produksi (drop via ssh saktify)
- Delta baru `database/sql/2026_08_12_drop_midtrans.sql`: `ALTER TABLE projects DROP COLUMN payment_status`,
  `DROP TABLE IF EXISTS payment_requests`, + hapus 2 baris `migrations`.
- URUTAN PENTING (dijalankan benar): deploy KODE dulu, BARU drop DB. Kalau dibalik, boot Payment lama
  masih menulis kolom `payment_status` -> error "Unknown column" saat simpan. Verifikasi BEFORE (ada) ->
  AFTER (tabel-hilang + kolom-hilang). Smoke test: `/login`=200, `/`=302.

### Catatan
- `.env` LOKAL & `.env` HOSTING masih menyimpan key `MIDTRANS_*` (orphan, tak dipakai kode). Aman
  dibiarkan; bisa dibersihkan manual kapan saja.
- Konfirmasi workflow deploy: benar, tinggal `./scripts/deploy.ps1` (push + `ssh saktify` git pull +
  bersihkan cache). Perubahan SKEMA DB tetap manual (delta SQL via ssh/phpMyAdmin).

### Pending / lanjutan
- Masih menggantung (bukan Midtrans lagi): commit `Claude-strack.bat` (untracked), commit
  `resources/views/errors/` (+ logo) dari hosting. Seluruh urusan Midtrans (aktivasi production,
  webhook, regenerate server key) SUDAH TIDAK RELEVAN karena fitur dibatalkan.

---

## Sesi 2026-07-24 (status Penawaran + modul Hutang Piutang + modul Maintenance/checklist)

### Ringkasan
Tiga pekerjaan besar, SEMUA sudah commit + push + deploy ke hosting + delta SQL diterapkan ke DB
hosting + migrasi dicatat manual. Verifikasi via `php -l` + `route:list` + `view:cache` + cek struktur
tabel di hosting. DB lokal `strack` TETAP TIDAK ADA -> tidak ada uji runtime lokal; user uji manual di
strack.my.id. Commits urut: `4e49e49`, `3ae1aa8`, `f905e7e`, `f46fb9e`, `8488433`, `2f102d7`, `31c7352`,
`ffcffcb`. Migrasi hosting tercatat: batch 3 (lead), 4 (debt), 5 (maintenance), 6 (maintenance-todo).

### 1. Status proyek "Penawaran" (LEAD) + kecualikan CANCELLED/LEAD dari penjualan (`4e49e49`, `3ae1aa8`)
- Pemicu: user tanya apakah proyek CANCELLED ikut dihitung di penjualan/laporan total proyek. Ternyata
  IYA di beberapa tempat. Minta CANCELLED dikecualikan + tambah 1 status baru untuk proyek "belum deal"
  yang ingin dicatat TAPI tidak dihitung sebagai penjualan.
- KEPUTUSAN (user pilih): status baru = `LEAD`, label UI "Penawaran", warna abu-abu (secondary).
- Enum `projects.status` ditambah `LEAD` -> (LEAD,WAITING,PROGRESS,FINISHED,CANCELLED). Delta
  `database/sql/2026_07_24_add_lead_status_to_projects.sql`.
- CANCELLED + LEAD DIKECUALIKAN dari perhitungan nilai (pakai `->whereNotIn('status',['CANCELLED','LEAD'])`):
  `ProjectController::index` (totalNilaiBulanIni), `DashboardController` (grafik nilai proyek bulanan),
  `FinancialReportController::generateLaporanPenjualan` (difilter di sumber $projectsInPeriod),
  `Client::getTotalProjectValueAttribute` (total nilai proyek per klien).
- TIDAK terpengaruh: Total Pendapatan (dari tabel Payment = uang riil), Piutang (sudah WAITING/PROGRESS saja).
- UI: dropdown "Status Awal" (Menunggu/Penawaran) di form create (default WAITING) + opsi di edit +
  tombol "Deal - Jadikan Proyek" (LEAD->WAITING) di detail. Tombol Tagih/Tandai Selesai/Konfirmasi
  disembunyikan saat status LEAD. Badge PENAWARAN di index desktop/mobile + show. Validasi update &
  updateStatus ditambah LEAD. Kartu statistik "Penawaran" (+filter) di index.
- `3ae1aa8`: kartu statistik proyek dirapikan agar 1 baris. Penyebab pecah 2 baris: 2 kartu (Nilai Bulan
  Ini, Total Piutang) tak punya `col-xl-2`. Seragamkan 6 kartu ke `col-6 col-md-4 col-lg-2`.
- File: `app/Models/Project.php`, `Client.php`; `ProjectController.php`, `DashboardController.php`,
  `FinancialReportController.php`, `ClientController.php`; views `projects/{index,create,edit,show}.blade.php`,
  `clients/show.blade.php`.

### 2. Modul Catatan Hutang Piutang (`f905e7e`, `f46fb9e`, `8488433`)
- Menu baru "Catatan Hutang Piutang" (sidebar, ikon cash-stack). Buku catatan HUTANG (saya meminjam) &
  PIUTANG (saya meminjamkan) UMUM (mis. pinjam ke kantor), TERPISAH dari piutang proyek. User input tiap
  pembayaran agar sisa terlihat.
- Tabel `debt_records` (type HUTANG/PIUTANG, party_name, title, principal_amount, paid_amount,
  status ONGOING/PAID, due_date, notes) + `debt_payments` (riwayat cicilan). paid_amount + status
  auto-sync lewat event model DebtPayment (pola sama Project<->Payment). Delta
  `database/sql/2026_07_24_debt_records.sql`.
- KEPUTUSAN (user, 3 pertanyaan): (a) TERPISAH dari "Total Asset" dashboard (dashboard tak diubah),
  (b) TOLAK kelebihan bayar (pembayaran > sisa ditolak dgn error), (c) pakai due date TAPI OPSIONAL.
- `f46fb9e`: due date jadi opsional via saklar "Pakai tanggal jatuh tempo" (default mati; field tanggal
  muncul & terkirim hanya bila aktif; saat edit auto-aktif bila sudah ada). Input nonaktif di-`disabled`
  agar tak ikut submit. Hanya ubah `_form.blade.php`.
- `8488433`: hardening responsif kartu ringkasan HP (font `clamp` + `word-break` agar rupiah besar tak meluber).
- Index: ringkasan Total Piutang/Total Hutang/Selisih + daftar (filter tipe, pencarian) + kartu khusus HP.
  Detail: riwayat pembayaran + form tambah (tolak > sisa) + progress bar + badge lewat/dekat tempo. Auto LUNAS.
- File: `app/Models/DebtRecord.php`, `DebtPayment.php`; `DebtRecordController.php`, `DebtPaymentController.php`;
  `routes/web.php`; `layouts/app.blade.php`; `resources/views/debts/{index,create,edit,show,_form}.blade.php`.
- Route: `resource debts` + `debts/{debt}/payments` (store) + `debt-payments/{debtPayment}` (destroy).

### 3. Modul Maintenance -> checklist/todo (`2f102d7`, `31c7352`, `ffcffcb`)
- Menu baru "Maintenance" (sidebar, ikon tools). Catatan tugas perawatan (AC, motor, filter air, dll).
- `2f102d7` (awal): `maintenance_tasks` (name, schedule_type TEXT/DATE/MONTH/YEAR, schedule_value, notes).
  Jadwal fleksibel; nilai disimpan ternormalisasi string di `schedule_value`, diformat di model (nama bulan
  Indonesia). Delta `2026_07_24_maintenance_tasks.sql` (batch 5).
- `31c7352`: tipe MONTH jadi MULTI-pilih (checkbox 12 bulan, berulang tiap tahun), disimpan "1,4,6,11",
  tampil "Januari, April, Juni, November". (User minta 1 catatan bisa banyak bulan, mis. Cuci AC Jan/Apr/
  Jun/Nov.) Format lama YYYY-MM tetap didukung untuk tampilan.
- `ffcffcb` (upgrade jadi todo). KEPUTUSAN user (3 pertanyaan): (a) tampilan SATU DAFTAR + filter status
  (Semua/Perlu Dikerjakan/Terjadwal/Selesai), (b) simpan HISTORI LENGKAP, (c) tipe ODOMETER dibuat sekarang.
  - Status otomatis dihitung di PHP via accessor `status` (BUKAN kolom DB): DUE/SCHEDULED/DONE.
    - MONTH: DUE bila bulan berjalan termasuk daftar & belum dikerjakan bulan itu; AUTO-RESET tiap siklus
      (cek last_done_at vs bulan+tahun kini). Selesai bulan ini -> DONE, bulan berikut yg terdaftar -> DUE lagi.
    - DATE/YEAR: DUE bila sudah jatuh waktu & belum ditandai; DONE setelah ditandai.
    - TEXT: todo manual (DUE sampai dicentang selesai).
    - ODOMETER: SELALU SCHEDULED. Aplikasi tak tahu km live -> tak bisa auto-DUE. Tampil "berikutnya: X km".
  - Tombol "Tandai Selesai": cepat 1 klik di daftar (odometer -> Swal input km) atau via detail (tanggal +
    odometer + catatan). Simpan ke `maintenance_logs` + update last_done_at (dan last_km utk odometer).
  - Tipe ODOMETER: kolom `interval_km` + `last_km`; `next_km` = last_km + interval_km.
  - DB: tabel baru `maintenance_logs` + kolom `last_done_at`/`interval_km`/`last_km` + enum +ODOMETER.
    Delta `2026_07_24_maintenance_todo.sql` (batch 6).
  - Controller index: fetch all -> hitung status di PHP -> filter status -> `sortBy([status_sort, nama])`
    (tanpa paginate, skala personal). show: detail + riwayat (hapus per entri). `complete()` + `destroyLog()`.
- File: `app/Models/MaintenanceTask.php`, `MaintenanceLog.php`; `MaintenanceController.php`; `routes/web.php`;
  `layouts/app.blade.php`; `resources/views/maintenance/{index,create,edit,show,_form}.blade.php`.
- Route: `resource maintenance` + `maintenance/{maintenance}/complete` + `maintenance-logs/{maintenanceLog}` destroy.

### Catatan teknis (penting untuk sesi berikutnya)
- Deploy: SELALU `scripts/deploy.ps1` via PowerShell. JANGAN via Bash (file .ps1 gagal parse). Commit
  manual dulu (stage file spesifik) supaya `docs/` & `Claude-strack.bat` TIDAK ikut ter-commit
  (deploy.ps1 dgn -Message memakai `git add -A`).
- Delta SQL ke hosting via SSH (pola dari memori): tanpa kutip-ganda, `export MYSQL_PWD=$(...)`,
  `mysql -u$US $DB < file.sql`. Catat migrasi: hitung batch dulu (`SELECT COALESCE(MAX(batch),0)+1`),
  lalu `echo INSERT INTO migrations \(...\) VALUES \(\'$M\', $NB\)\; | mysql -u$US $DB`. INGAT:
  `mysql -e "..."` dgn kutip-ganda GAGAL (transport hapus `"`) -> pakai echo-pipe dgn escape, atau -e '...'.
- Migrasi hosting saat ini s/d batch 6. 4 delta SQL 2026_07_24 sudah diterapkan ke hosting.

### Pending / lanjutan sesi berikutnya
- User sedang UJI manual ketiga fitur di strack.my.id. Tunggu feedback.
- ODOMETER: opsi tambah field "odometer sekarang" yang bisa diupdate user agar sistem AUTO menandai
  "Perlu dikerjakan" saat mendekati/melewati target (sekarang manual, selalu tampil Terjadwal).
- Opsi konsistensi: multi-pilih untuk tipe TAHUN (mis. 2026, 2028) bila diinginkan.
- Bila DB lokal `strack` diimpor ulang: jalankan 4 delta SQL `database/sql/2026_07_24_*.sql` agar konsisten.
- Masih menggantung dari sesi lampau: `docs/midtrans/*` & `Claude-strack.bat` belum di-commit; aktivasi
  Midtrans production + regenerate server key; commit `resources/views/errors/` (+logo) dari hosting.

---

## Sesi 2026-07-23 (tombol konfirmasi WAITING + hapus fitur Tugas & Perlengkapan + area karyawan)

### Ringkasan
Dua pekerjaan. Keduanya SUDAH commit + push + deploy ke hosting.
1. `7152cd9`: sembunyikan tombol "Konfirmasi Selesai ke Client" saat proyek belum dimulai.
2. `7c1db26`: hapus total fitur Tugas (Task) + Perlengkapan (Supply) + seluruh area login karyawan
   (role `user`), termasuk drop tabel DB di hosting.

### 1. Tombol Konfirmasi Selesai hanya setelah proyek dimulai (`7152cd9`)
- `resources/views/projects/show.blade.php`: kondisi tombol ditambah `&& $project->status !== 'WAITING'`
  (sebelumnya hanya kecualikan CANCELLED/CANCELED). Konsisten dengan tombol "Tandai Selesai" di atasnya.
  Status WAITING = belum dimulai; tombol muncul setelah PROGRESS dst.

### 2. Hapus fitur Tugas + Perlengkapan + area karyawan (`7c1db26`)
- KEPUTUSAN (dikonfirmasi user via 2 pertanyaan):
  - Hapus fitur Tugas SEKALIGUS seluruh area role `user` (dashboard-user, tasks-user, menu sidebar
    karyawan) karena dashboard karyawan sepenuhnya berbasis Task dan sudah tidak dipakai.
  - Tabel DB di-drop (bukan dibiarkan), dijalankan via SSH ke hosting oleh Claude.
- FILE DIHAPUS: `app/Http/Controllers/TaskController.php`, `SupplyController.php`;
  `app/Models/Task.php`, `TaskAssignment.php`, `Supply.php`, `SupplyUsage.php`;
  `resources/views/tasks/*` (7 file), `resources/views/supplies/*` (6 file),
  `resources/views/dashboard/index-user.blade.php`;
  `database/migrations/2024_01_01_000001_create_tasks_table.php`.
- FILE DIEDIT:
  - `routes/web.php`: buang import Task/SupplyController + grup route tasks, tasks-user,
    dashboard-user, dan resource supplies (+ route turunannya).
  - `app/Http/Controllers/DashboardController.php`: buang import Task/TaskAssignment + method
    `userIndex`. Redirect non-admin di `index()` diubah dari `dashboard.user` -> route `login`.
  - `resources/views/layouts/app.blade.php`: hapus menu Perlengkapan, menu Tugas (admin), seluruh
    blok `@if session('role')==='user'` (Dashboard+Tugas karyawan). Link logo header disederhanakan
    jadi selalu `route('dashboard')` (dulu bercabang admin/user).
  - `app/Http/Controllers/SimpleLoginController.php`: hapus cabang login PIN `120906` (role user ->
    /dashboard-user yang sudah tak ada). Sisa hanya PIN admin `123698`.
- FILE BARU: `database/sql/2026_07_23_drop_task_supply.sql` (delta DROP + hapus baris migrasi tasks).
- Verifikasi lokal: `php -l` bersih (controller, routes), `php artisan view:cache` sukses,
  `route:list` sudah tidak memuat route task/supply, hanya `dashboard-admin` (name `dashboard`).

### DB hosting (drop via SSH)
- DB hosting = `u137841455_ZzLVP`. Sebelum drop, jumlah baris: tasks=4, task_assignments=251,
  supplies=22, supply_usages=45. User sudah export backup ke folder Downloads (`u137841455_ZzLVP.sql`).
- Drop dijalankan lewat file delta yang sudah ter-deploy: `mysql -u$US $DB < database/sql/2026_07_23_drop_task_supply.sql`.
  Verifikasi `SHOW TABLES | grep` -> keempat tabel hilang. FK: `supply_usages->supplies`,
  `task_assignments->tasks` (CASCADE), tidak ada tabel lain yang mereferensi -> aman.
- Smoke test situs: `/login` = 200, `/` = 302 (redirect login, normal).

### DB lokal
- Database `strack` TIDAK ADA di MySQL lokal saat ini (Laragon jalan, tapi belum diimpor; yang ada
  a.l. `u137841455_horawranghae`). Jadi tidak ada yang di-drop lokal. Delta SQL siap dijalankan
  bila DB lokal diimpor ulang.

### Catatan teknis penting: menjalankan MySQL di hosting via SSH dari PowerShell
- Transport PowerShell -> `ssh saktify` MENGHAPUS semua karakter kutip-ganda `"` dari perintah, dan
  heredoc `<<'PHP'` GAGAL (exit 255). Beberapa iterasi terbuang sebelum menemukan pola yang jalan.
- Pola yang terbukti jalan (simpan ke `$remote = @'...'@` lalu `ssh saktify $remote`):
  - TANPA kutip-ganda sama sekali.
  - Password DB via env: `export MYSQL_PWD=$(grep '^DB_PASSWORD=' .env | cut -d= -f2- | tr -d '"')`,
    lalu `mysql -u$US $DB ...` (tanpa `-p`). Kredensial lain juga di-grep dari `.env`.
  - SQL inline pakai KUTIP-TUNGGAL; hindari string-literal (pakai identifier/alias) atau jalankan file.

### Pending / lanjutan sesi berikutnya
- User akan CEK hasil (tombol WAITING + hilangnya menu/fitur Tugas & Perlengkapan). Tunggu feedback.
- Bila suatu saat DB lokal `strack` diimpor ulang, jalankan `database/sql/2026_07_23_drop_task_supply.sql`
  agar konsisten (opsional).
- Masih menggantung dari sesi 2026-07-22 (belum berubah): `docs/midtrans/*`, `CLAUDE.md`, `DOKUMENTASI.md`,
  `Claude-strack.bat` belum di-commit; aktivasi Midtrans production; commit `resources/views/errors/`
  dari hosting. Lihat entri 2026-07-22 di bawah.

---

## Sesi 2026-07-22 (sinkron hosting, workflow deploy, sidebar, dokumen Midtrans)

### Ringkasan
Empat hal: (1) verifikasi project lokal == hosting, (2) buat workflow deploy git+SSH, (3) rapikan
tampilan sidebar, (4) susun dokumen "Flow Transaksi" untuk onboarding Midtrans (data susulan).
Poin 1-3 sudah commit+push+deploy (`c17d357`). Poin 4 (folder `docs/midtrans/`) BELUM di-commit.

### 1. Verifikasi lokal vs hosting
- Hosting = clone git repo yang sama (`SaktiPutraS/strack`, branch `main`) di
  `~/domains/strack.my.id/public_html`, SSH `ssh saktify` (user `u137841455`, PHP 8.2.30, git 2.47).
- Saat dicek: lokal HEAD == hosting HEAD (`d6c9a3e`). Jadi kode SUDAH sinkron.
- Beda kecil di hosting: `composer.lock` termodifikasi kosmetik (`[]`->`{}`, plugin-api 2.6->2.9),
  plus untracked `resources/views/errors/`, `Logo.png`, `public/logo_saktify.png` (halaman error
  kustom & aset yang belum masuk repo; aman dari pull).
- KOREKSI: catatan "2026-06-18 BELUM DI-COMMIT" di CLAUDE.md keliru; pekerjaan kalender sebenarnya
  sudah tercommit `d6c9a3e`. Sudah diperbaiki.

### 2. Workflow deploy (BARU)
- `scripts/deploy.ps1`: push branch ke origin lalu SSH ke hosting untuk deploy.
  Remote step: `git checkout -- composer.lock` (buang modif kosmetik agar pull tak menolak) ->
  `git pull --ff-only origin main` -> `php artisan optimize:clear` -> `php artisan view:cache`.
- Param: `-Message` (commit dulu), `-SkipPush`, `-SshHost`, `-RemotePath`, `-Branch`.
- Diuji end-to-end: deploy `c17d357` sukses, situs live balas HTTP 302 (redirect login = normal).
- Skema DB TIDAK lewat skrip ini (tetap delta SQL manual via phpMyAdmin).

### 3. Sidebar dirapikan (`resources/views/layouts/app.blade.php`)
- Masalah yang dilaporkan: ukuran font/warna menu tak konsisten, kurang menarik.
- Fix (CSS, scoped `.sidebar`, tanpa ubah markup): `.sidebar` jadi flex-column (footer logout
  menempel bawah); ikon lebar tetap 1.4rem -> label sejajar; state aktif = pill ungu + accent bar
  kiri (khusus menu utama) + inset outline; hover halus; caret grup rata kanan & berputar saat
  terbuka (`[aria-expanded=true]`); sub-menu skala font konsisten 0.85rem + garis pemandu (tree
  line) kiri. Tetap tema ungu+glass, dihindari kesan generik/AI.
- Verifikasi kompilasi Blade pakai PHP 8.2 Laragon (bukan xampp 8.1.17 yg gagal platform check).

### 4. Dokumen onboarding Midtrans
- Midtrans minta (form data susulan) dokumen flow transaksi ber-screenshot step-by-step dari
  pemesanan sampai checkout Midtrans.
- Dibuat `docs/midtrans/flow-transaksi.html` (dokumen HTML rapi, print-to-PDF, header info usaha,
  6 langkah) + `docs/midtrans/img/` (logo + step1..step6) + hasil `Flow-Transaksi-Saktify.pdf`
  (di-generate via Edge headless `--print-to-pdf`).
- Info usaha (dari user): merchant **Saktify**, email **admin@saktify.com**, web **saktify.com**;
  **strack.my.id = sistem penagihan internal** Saktify (framing ini penting agar reviewer paham
  kenapa flow ada di domain strack padahal yg didaftarkan saktify).
- Isi 6 langkah (screenshot asli user, proyek "Website Starvvo" Rp2.000.000, klien PT Global Mitra
  Proteksindo, Order STRACK-204): 1 detail proyek+tombol Tagih Klien, 2 dialog nominal, 3 tagihan
  berhasil (link), 4 pesan WA, 5 halaman checkout Midtrans (merchant Saktify, metode VA/QRIS/Card),
  6 pembayaran QRIS (QR Saktify). Langkah 7 (bukti sukses) DIHAPUS atas permintaan user.
- Langkah 5-6 pakai environment SANDBOX (ada badge "TEST" di sudut) karena production belum aktif.

### Temuan penting Midtrans
- Key di `.env` LOKAL adalah key **SANDBOX** (akun ini pakai prefix `Mid-server-`/`Mid-client-`
  untuk sandbox, BUKAN `SB-Mid-`; terverifikasi dari screenshot Access Keys yg Environment=Sandbox)
  dengan `MIDTRANS_IS_PRODUCTION=false`. Karena itu link sandbox bisa langsung dibuat.
- Cara generate link sandbox (dipakai untuk screenshot langkah 5-6):
  `curl -u "$SERVER_KEY:" -X POST https://app.sandbox.midtrans.com/snap/v1/transactions -d '{...}'`
  -> balas `redirect_url` (buka untuk lihat halaman checkout). Screenshot 3-4 lama pakai domain
  production `app.midtrans.com` (order STRACK-204) yg TIDAK bisa dibuka ("No payment channels").

### File tersentuh
- SUDAH commit+push+deploy (`c17d357`): `resources/views/layouts/app.blade.php`, `scripts/deploy.ps1`.
- BELUM di-commit: `docs/midtrans/*` (html, img/, pdf), `CLAUDE.md`, `DOKUMENTASI.md`,
  `Claude-strack.bat` (untracked dari sesi lampau).

### Pending / lanjutan sesi berikutnya
- User sedang CEK PDF `docs/midtrans/Flow-Transaksi-Saktify.pdf`. Bila OK -> upload ke form Dokumen
  Tambahan Midtrans + Submit. Opsi tersedia: crop badge "TEST" dari screenshot 5-6 bila diminta.
- Pertimbangkan commit `docs/midtrans/` ke repo (opsional, agar tersimpan).
- Midtrans production MASIH belum aktivasi ("No payment channels available"). Setelah aktif:
  deploy kode+.env production, apply `database/sql/2026_06_10_payment_gateway.sql`, set webhook URL
  `https://strack.my.id/webhooks/payment/midtrans`, uji Rp1.000 asli. Regenerate Server Key
  production (sempat ter-paste chat sesi lampau) - masih pending.
- Opsional: commit `resources/views/errors/` (+ logo) dari hosting ke repo.

---

## Sesi 2026-06-18 (perbaikan fitur Catatan Pribadi di kalender dashboard)

### Ringkasan
User melaporkan 2 kendala pada "Catatan Pribadi" di kalender beranda (catatan PROYEK sudah OK):
(1) saat baru membuat catatan, kalender langsung KOSONG semua termasuk proyek, baru muncul
setelah reload; (2) catatan tampil sebagai kotak hijau tapi teks yang diinput tidak terlihat.
Semua diperbaiki. **BELUM di-commit** - user mau cek/uji visual dulu.

### Diagnosis & akar masalah
1. **Kalender kosong total setelah simpan catatan (perlu reload).**
   - `CalendarNote::getNotesForMonth()` me-`keyBy(day)`, jadi JSON-nya OBJEK ber-key tanggal,
     BUKAN array.
   - Saat load halaman, JS inline sudah mengonversi via `Object.values` (aman). Tapi
     `loadCalendarData()` (dipanggil setelah simpan, jalur AJAX) langsung `calendarNotes = data.notes`
     tanpa konversi. Akibatnya `calendarNotes` jadi objek, lalu `calendarNotes.filter(...)` di
     `renderCalendar()` -> TypeError. Karena `calendarBody.innerHTML=''` jalan duluan, seluruh
     sel (proyek ikut) jadi blank. Reload memperbaiki karena lewat jalur konversi inline.
2. **Teks catatan tak terlihat / "hijau saja".**
   - Sel kalender (dan mingguan) cuma menampilkan JUMLAH ("N catatan"), tak pernah judul -> user
     tak melihat teks yang diketik.
   - Field deskripsi PUTUS end-to-end: form kirim `description`, controller validasi/simpan
     `content`, modal baca `note.description`. Jadi deskripsi tak pernah tersimpan maupun tampil.
   - Tanggal catatan diserialisasi ISO (`...T00:00:00Z`) lewat cast 'date', sedangkan tampilan
     mingguan & modal membandingkan `note.date === 'Y-m-d'` -> tak pernah cocok (sel desktop aman
     karena pakai `new Date()`).

### Yang dikerjakan (4 perbaikan terkait)
1. `loadCalendarData`: normalkan `data.notes` ke array (`Object.values` bila objek) -> fix kalender kosong.
2. Sel kalender + tampilan mingguan: tampilkan JUDUL catatan (di-`escapeHtml`, elipsis bila panjang),
   bukan sekadar jumlah.
3. Samakan field deskripsi ke `content` (kolom DB): JS kirim `content`, model ekspos `content`,
   modal baca `note.content`. Controller store/update sudah pakai `content` (tak diubah).
4. Serialisasi tanggal catatan jadi `Y-m-d` di `getNotesForMonth` (lewat `->map(...)`) -> cocok di
   sel, mingguan, dan modal. Karena method ini dipakai DashboardController (load awal, 2 tempat)
   dan endpoint `getMonthNotes` (AJAX), perbaikan berlaku di semua jalur sekaligus.
- Bonus: `editNote(id)` kini cukup terima `id` lalu cari datanya dari `calendarNotes`
  (lebih aman terhadap tanda kutip/karakter khusus di judul; tak lagi oper string via onclick).
- Tambah CSS `.note-title` (elipsis) + `.note-preview i { flex-shrink:0 }`.

### Keputusan penting
- Sumber kebenaran field deskripsi = kolom DB `content` (bukan `description`). Semua jalur disetel
  ke `content`.
- Tanggal catatan diserialisasi sebagai `Y-m-d` di level model (`getNotesForMonth`), agar konsisten
  dengan deadline proyek yang juga `->format('Y-m-d')`.
- `getNotesForMonth` tetap `keyBy(day)` (struktur lama dipertahankan); konversi ke array dilakukan
  di sisi JS (inline + loadCalendarData).

### File tersentuh (BELUM commit)
- `app/Models/CalendarNote.php` (map date Y-m-d + ekspos content di getNotesForMonth)
- `resources/views/dashboard/index.blade.php` (normalisasi notes, tampil judul di sel & mingguan,
  modal baca content, editNote by id, form kirim content, CSS .note-title)

### Verifikasi
- `php -l` bersih untuk model & `CalendarNoteController`.
- `php artisan view:clear` + `view:cache` sukses (Blade kompilasi OK).
- BELUM diuji visual oleh user (buat/edit catatan, cek desktop & mingguan, cek kalender tak kosong
  lagi setelah simpan).

### Pending / lanjutan sesi berikutnya
- User uji visual. Bila OK -> commit + push ke `main` (commit pakai BASH, multiple `-m`, bukan
  here-string PowerShell). Setelah commit, perbarui status "BELUM DI-COMMIT" di CLAUDE.md.
- Midtrans dari 2026-06-10 MASIH pending (aktivasi akun production + deploy hosting + apply delta
  SQL + set webhook + regenerate server key). Lihat entri di bawah.

---

## Sesi 2026-06-11 (perbaikan dashboard + tombol konfirmasi selesai)

### Ringkasan
Penyesuaian tampilan kalender/dashboard dan satu fitur kecil di detail proyek. Semua
perubahan SUDAH di-commit & di-push ke `origin/main` (GitHub SaktiPutraS/strack).

### Yang dikerjakan
1. **Bugfix grafik "Pendapatan & Pengeluaran Mingguan" (nilai tidak muncul)**
   - Akar masalah di `DashboardController.php`: hitung awal tahun fiskal pakai
     `Carbon::now()->startOfYear()->addMonths(6)` lalu cek `->month < 7`. Setelah +6 bulan,
     bulannya selalu Juli, jadi cek tidak pernah jalan -> `$startOfYear` = 1 Juli tahun ini
     (di masa depan, krn skrg Juni). Akibatnya `while` loop tak pernah jalan, `$weeklyData` kosong.
   - Fix: simpan `$currentMonth = Carbon::now()->month` SEBELUM dimodifikasi, baru cek `< 7`.
     `$endOfYear` diubah jadi `$startOfYear->copy()->addYear()->subDay()`.
   - Diverifikasi: untuk 2026-06-11 menghasilkan fiskal 2025-07-01 s/d 2026-06-30 -> loop terisi.

2. **Kalender: urutan info -> nama KLIEN dulu, nama PROYEK di bawah**
   - User lebih familiar dgn nama klien. Diterapkan konsisten di SEMUA tampilan dalam kartu
     "Kalender & Deadline Proyek": sel kalender (desktop), modal detail hari, tampilan mingguan
     (ponsel), panel "proyek belum selesai", dan tooltip sel.

3. **Warna nilai piutang**
   - Iterasi: merah (awal) -> hijau+outline putih -> hijau polos -> **PUTIH** (final).
     Alasan: hijau tidak terbaca di latar sel yang berwarna. Sel kuning (deadline mendekat)
     dikecualikan -> teks gelap agar tetap terbaca.
   - Latar terang (modal hari, mingguan, panel proyek) TETAP hijau (`.text-piutang`), karena
     putih akan tak terlihat di latar putih.

4. **Hapus prefiks "Rp"** pada nilai proyek & piutang di tampilan kalender (sudah pasti rupiah).
   - Dihapus dari helper JS `formatRupiahSingkat` (hanya dipakai utk nilai/piutang proyek di
     kalender), teks modal hari, dan tooltip. Kartu aset atas dashboard pakai helper PHP terpisah,
     jadi TETAP menampilkan "Rp".

5. **Tombol "Konfirmasi Selesai ke Client" di detail proyek** (`projects/show.blade.php`, Aksi Cepat)
   - Link WhatsApp ke klien dgn pesan terisi otomatis:
     "hallo ka, maaf mau confirm, berarti untuk tugasnnya sudah selesai yah. karna mau saya
     close projectnnya 🙏🏼"
   - Pakai `client->whatsapp_link` (sudah ada, format `api.whatsapp.com/send?phone=...`) + `&text=`
     hasil `rawurlencode`. Muncul selama status proyek bukan CANCELLED/CANCELED.

### Keputusan penting
- Piutang di kalender = putih (bukan hijau), karena keterbacaan di latar sel berwarna.
- "Rp" hanya dihapus di konteks kalender (nilai/piutang proyek), bukan di kartu aset.
- Urutan klien-dulu diterapkan menyeluruh di kartu kalender demi konsistensi (bukan hanya 1 view).

### File tersentuh (sudah commit + push)
- `app/Http/Controllers/DashboardController.php` (fix tahun fiskal weeklyData)
- `resources/views/dashboard/index.blade.php` (urutan klien/proyek, warna piutang, hapus Rp)
- `resources/views/projects/show.blade.php` (tombol Konfirmasi Selesai ke Client)
- Commits: `0adbc26`, `c28b15c`, `fd62725` (semua di `main`).

### Catatan teknis
- Tool commit jalan di BASH (bukan PowerShell). Here-string PowerShell `@'...'@` TIDAK berlaku
  dan menyisipkan `@` literal ke pesan commit. Gunakan multiple `-m` flag biasa.
- Push lancar tanpa login ulang: kredensial via Git Credential Manager sudah tersimpan.

### Pending / belum dikonfirmasi user
- User belum cek hasilnya secara visual (tombol konfirmasi + warna piutang putih + grafik mingguan).
  Tunggu feedback sesi berikutnya kalau ada yang perlu disesuaikan.
- Pekerjaan Midtrans dari sesi 2026-06-10 MASIH pending (lihat entri di bawah): aktivasi akun
  production + deploy hosting + apply delta SQL + set webhook + regenerate server key.

---

## Sesi 2026-06-10 (testing & aktivasi Midtrans)

### Yang diuji & hasilnya
- **Tagih Klien (sandbox)**: tombol jalan, link Snap sandbox Rp1.000 berhasil dibuat
  (order STRACK-192-...). Integrasi + kredensial terbukti benar.
- **Webhook end-to-end di lokal (terisolasi, lalu dibersihkan)**: SEMUA LOLOS.
  - Notifikasi "settlement" -> HTTP 200, payment_request jadi PAID + paid_at terisi.
  - Project otomatis jadi **Lunas** (paid_amount = total_value), Payment tercatat
    (Rp1.000, type FINAL, method "QRIS (Midtrans)").
  - Idempotency: kirim ulang notifikasi tidak bikin Payment dobel.
  - Signature salah -> ditolak 403.
  - Cara uji: skrip bootstrap Laravel + Request::create ke route asli (lewat router +
    middleware, termasuk pengecualian CSRF). Tidak butuh ngrok/URL publik.
- **Coba production**: user ganti ke key production + IS_PRODUCTION=true. Link production
  (app.midtrans.com) BERHASIL dibuat (order STRACK-193), TAPI saat dibuka muncul
  **"No payment channels available"**.

### Temuan penting
- "No payment channels available" = **akun production belum diaktivasi / belum ada metode
  pembayaran yang disetujui**. Ini murni urusan akun Midtrans, BUKAN bug kode.
- User sedang proses aktivasi: menyetujui S&K biaya (QRIS 0,7%, VA flat Rp4.000, GoPay 2% -
  semua dipotong per transaksi sukses) lalu klik "Ajukan". Menunggu review Midtrans.

### Catatan biaya (untuk referensi)
- QRIS 0,7% (persentase) vs VA Rp4.000 (flat). Titik impas ~Rp570.000: di bawah QRIS lebih
  murah, di atas VA lebih murah. Snap menampilkan semua metode yang diaktifkan.

### PENDING (lanjut sesi berikutnya)
1. **Tunggu aktivasi akun Midtrans production + QRIS/VA aktif** (cek dashboard: tidak ada
   banner "Activate"/"In Review", menu Payment menampilkan QRIS hijau).
2. Setelah aktif: deploy ke hosting -> kode + `.env` production (`MIDTRANS_IS_PRODUCTION=true`)
   + jalankan `database/sql/2026_06_10_payment_gateway.sql` di phpMyAdmin + set URL notifikasi
   production ke `https://strack.my.id/webhooks/payment/midtrans`.
3. Tes Rp1.000 asli di hosting dgn project fiktif sampai status auto-Lunas.
4. **KEAMANAN**: Server Key production sempat ter-paste di chat -> regenerate di Settings ->
   Access Keys setelah beres.
5. **Cara deploy ke Hostinger belum ditentukan** (Git pull / upload manual / FTP) - tanyakan.
6. Sisa data uji di DB lokal: payment_request PENDING order STRACK-192 (project nyata 192,
   nominal 1000) & STRACK-193. Tidak mengganggu, akan expire sendiri; boleh dihapus manual.
7. `.env` LOKAL saat ini berisi key production + IS_PRODUCTION=false (kombinasi tidak konsisten,
   tapi sempat menghasilkan link sandbox saat key masih sandbox). Rapikan sesuai kebutuhan tes.

---

## Sesi 2026-06-10 (lanjutan) - Fitur Pembayaran Otomatis (Midtrans)

### Ringkasan
Menambah alur tagih otomatis: tombol "Tagih Klien" di project -> generate QRIS/payment
link Midtrans (Snap, sandbox) -> kirim ke klien via WhatsApp -> webhook memverifikasi
signature -> status pembayaran project otomatis jadi Lunas + tercatat sebagai Payment.

### Keputusan (dikonfirmasi user)
- Gateway: **Midtrans** (Snap API, mode sandbox dulu: MIDTRANS_IS_PRODUCTION=false).
- Uang masuk: **Payment saja** (saldo Bank Octo tetap di-transfer manual, tidak auto BankTransfer).
- Status Lunas: **kolom baru `projects.payment_status`** (UNPAID/PARTIAL/PAID), terpisah dari
  status pengerjaan (WAITING/PROGRESS/FINISHED/CANCELLED).

### Database (sudah diterapkan ke DB lokal `strack`)
- Tabel baru `payment_requests` (project_id, order_id unik, gateway, amount, status,
  payment_url, snap_token, gateway_ref, paid_at, expired_at, raw_response json).
- `projects.payment_status` enum UNPAID/PARTIAL/PAID, di-backfill dari paid_amount
  (hasil awal: PAID 174, PARTIAL 2, UNPAID 2).
- Diterapkan via SQL langsung (BUKAN `php artisan migrate`) karena ada migrasi lama
  pending yang tabelnya sudah ada (tasks, budget_items.category) - migrate akan gagal.
- Migrasi dicatat manual ke tabel `migrations` batch 2 agar konsisten dgn file migrasi.
- File delta SQL siap pakai untuk hosting: `database/sql/2026_06_10_payment_gateway.sql`.

### File yang dibuat
- `database/migrations/2026_06_10_000001_create_payment_requests_table.php`
- `database/migrations/2026_06_10_000002_add_payment_status_to_projects_table.php`
- `database/sql/2026_06_10_payment_gateway.sql` (delta untuk hosting)
- `app/Models/PaymentRequest.php`
- `app/Services/Payment/MidtransService.php` (createCharge/verifySignature/mapStatus, pakai Http facade, tanpa SDK)
- `app/Http/Controllers/BillingController.php` (charge -> JSON: payment_url + whatsapp_url)
- `app/Http/Controllers/PaymentWebhookController.php` (verifikasi signature, idempotent, lockForUpdate)

### File yang diubah
- `app/Models/Project.php` (fillable payment_status, relasi paymentRequests, syncPaymentStatus, badge accessor)
- `app/Models/Payment.php` (boot: ikut update payment_status saat saved/deleted)
- `config/services.php` (blok midtrans), `.env` + `.env.example` (MIDTRANS_*)
- `routes/web.php` (POST projects/{project}/charge; POST webhooks/payment/midtrans di luar auth)
- `bootstrap/app.php` (CSRF except webhooks/*)
- `resources/views/projects/show.blade.php` (tombol Tagih Klien + modal + badge + JS tagihKlien)
- `resources/views/projects/index.blade.php` (badge payment_status di kolom status)

### Sudah diverifikasi
- Lint PHP semua file OK, `view:cache` sukses (blade kompilasi), `route:list` memuat 2 route baru.
- Smoke test: accessor payment_status_label/color, relasi paymentRequests, syncPaymentStatus,
  model PaymentRequest, dan config midtrans semua jalan.

### PENDING / langkah berikut (PENTING)
1. **Isi kredensial sandbox** di `.env`: MIDTRANS_SERVER_KEY & MIDTRANS_CLIENT_KEY
   (dari dashboard Midtrans sandbox). Tanpa ini, tombol Tagih akan error.
2. **Set webhook URL** di dashboard Midtrans -> `https://strack.my.id/webhooks/payment/midtrans`.
   Untuk uji lokal pakai ngrok/expose karena Midtrans perlu URL publik.
3. **Apply ke hosting**: PRODUKSI LEBIH BARU dari local (projects ~192 vs local 183, payments
   ~295 vs 281). Karena itu JANGAN replace seluruh DB hosting dgn export local (akan hilang
   data baru). REKOMENDASI: jalankan `database/sql/2026_06_10_payment_gateway.sql` di phpMyAdmin
   hosting (hanya menambah tabel + kolom, tanpa sentuh data). Plus deploy file kode + isi .env hosting.
4. Uji end-to-end di sandbox: bikin tagihan -> bayar simulator -> pastikan webhook masuk,
   payment_status jadi PAID, dan Payment tercatat.
5. Belum ada cara membatalkan/expire tagihan PENDING dari UI (opsional).

---

## Sesi 2026-06-10

### Ringkasan
Dua perbaikan UX: tombol "Simpan & Lanjut" di form input pengeluaran, dan redesain
kartu ringkasan aset di dashboard agar saldo yang paling sering dicek (Bank Octo & Piutang)
tampil paling menonjol.

### Yang dikerjakan

**1. Fitur "Simpan & Lanjut" pada input pengeluaran**
- `app/Http/Controllers/ExpenseController.php` (method `store`):
  setelah `Expense::create`, jika request `action === 'save_next'` maka redirect kembali
  ke `expenses.create` dengan flash sukses; selain itu tetap ke `expenses.index` seperti biasa.
- `resources/views/expenses/create.blade.php`:
  - Tambah hidden input `action` (`id="formAction"`, default `save`).
  - Tambah tombol baru "Simpan & Lanjut" (`id="saveNextBtn"`); tombol lama di-rename
    dari "Simpan Pengeluaran" jadi "Simpan".
  - JS: klik "Simpan & Lanjut" mengeset `formAction.value = 'save_next'` lalu `requestSubmit()`.
  - JS validasi (jumlah <= 0 atau melebihi saldo) mereset `formAction` kembali ke `save`
    agar tidak nyangkut di mode save_next saat submit dibatalkan.
  - Loading state ("Menyimpan...") menyesuaikan tombol mana yang ditekan.

**2. Redesain kartu Asset Overview di dashboard**
- `resources/views/dashboard/index.blade.php`:
  - Bagian "Saldo Utama" baru di header kartu: **Bank Octo** (merah) dan **Piutang** (ungu)
    ditampilkan besar (font `clamp(1.15rem,3.5vw,1.6rem)`) karena paling sering dicek.
  - Masing-masing menampilkan persentase terhadap total aset.
  - "Total Asset" diturunkan jadi baris ringkasan kecil di body kartu.
  - Piutang dikeluarkan dari grid aset-detail (Cash, Emas, dll) dan dipindah ke atas.

### Keputusan penting
- Bank Octo & Piutang dianggap saldo yang paling sering dipantau, jadi diberi porsi visual terbesar.
- Pola "Simpan & Lanjut" memakai 1 hidden field + dua tombol submit (bukan dua form terpisah),
  supaya validasi JS yang sudah ada tetap dipakai bersama.

### File tersentuh
- `app/Http/Controllers/ExpenseController.php`
- `resources/views/expenses/create.blade.php`
- `resources/views/dashboard/index.blade.php`
- `desktop.ini` (hanya perubahan line-ending/CRLF, bisa diabaikan)

### Pending / lanjutan sesi berikutnya
- Perubahan **belum di-commit**. User akan cek/uji dulu manual.
- Perlu diverifikasi: alur "Simpan & Lanjut" benar-benar mengulang form dengan saldo terupdate,
  dan flash message tampil di halaman create.
- Cek tampilan kartu aset dashboard di layar kecil (mobile) - sudah pakai `clamp` & `word-break`,
  tapi belum diuji visual.
- Pertimbangkan revert/abaikan perubahan `desktop.ini` saat commit.
