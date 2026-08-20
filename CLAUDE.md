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

### 2026-08-20 (Foto struk Telegram jadi pengeluaran otomatis)
Permintaan user (belanja Alfagift malas dicatat manual). Kirim foto struk ke bot, bot merekap per kategori,
balas ya untuk simpan. Detail di DOKUMENTASI.md.
- POLA LAMA YANG DITIRU (dibaca dari 80 pengeluaran "Alfagift" di produksi): 1 struk = BEBERAPA baris
  pengeluaran, satu per kategori, deskripsi "Alfagift - Barang, Barang", sumber BANK.
- KEPUTUSAN user: (1) VOUCHER dipotong di kategori ENTERTAIN dulu, kalau tidak ada/kurang baru dibagi
  PROPORSIONAL (diskon per item tetap di itemnya); (2) rekap bisa DIKOREKSI lewat balasan bebas sebelum
  simpan; (3) berlaku untuk struk toko APA PUN, nama toko jadi awalan deskripsi.
- File baru: `ReceiptParser` (AI vision -> JSON item + kategori), `ReceiptTally` (SEMUA hitungan uang ada di
  kode, bukan di AI; total baris dijamin sama persis dengan total struk), `Actions/CatatStrukAction`
  (aksi banyak-baris + `refine()` koreksi), `NotACorrectionException`, command `struk:coba` (uji baca struk
  tanpa Telegram, default tidak menyimpan).
- KATEGORI dipelajari dari RIWAYAT sendiri: `ReceiptParser::categoryExamples()` mengirim contoh dari 400
  pengeluaran "alfa" terakhir ke AI (cache 3 jam, key `receipt_category_examples`). Perbaiki kategori data
  lama + hapus cache itu = pengelompokan ikut berubah, tanpa sentuh kode.
- Diubah: kedua provider AI kini bisa menerima GAMBAR (`inline_data` Gemini / `image.source.base64`
  Anthropic, pesan teks lama tetap jalan); `WriteAction` +hidden/pendingTtlMinutes/supportsRefine/refine;
  ActionRegistry menyaring aksi tersembunyi; BotOrchestrator +handleReceipt + cabang koreksi; webhook
  menerima foto (resolusi terbesar) & dokumen image/*; `AiGateway` jadi SINGLETON (kalau tidak, penanda
  provider 🔵/🟠 hilang karena tiap kelas pegang instance sendiri).
- TIDAK ada perubahan skema DB, tidak ada delta SQL. Duplikat struk BELUM dicegah (kolom `ref` sudah dibaca).
- UJI: 98 uji SQLite lulus semua. Lint bersih, view:cache OK.
- SUDAH deploy (`b0bb828`, `0dabd10`, `5e00c2e`) + TERUJI DI PRODUKSI dengan foto struk asli lewat
  `php artisan struk:coba <foto>`: 5 item terbaca semua, harga/diskon benar, total Rp72.600 sama persis.
  Gemini menaruh Tango & Kun Susu di SIERRA (memang ikut pola data lama); kalau maunya lain, koreksi di chat.
- PERBAIKAN dari uji produksi (`5e00c2e`): saldo TIDAK lagi dicek saat menyiapkan rekap (dulu rekap tak
  bisa dilihat sama sekali saat saldo tipis). Rekap tetap tampil + peringatan kekurangannya; penolakan
  terjadi saat menyimpan. Saldo Bank Octo produksi saat uji: Rp28.858.
- PENDING: user mencoba kirim foto struk langsung dari Telegram (termasuk alur koreksi kategori).

### 2026-08-20 (Domain diingatkan H-30 + bersih-bersih em dash kode lama)
Lanjutan pengingat Telegram, hari yang sama. Detail di DOKUMENTASI.md.
- `DailyDigest::DOMAIN_REMIND_DAYS = [30,14,7,3,1,0]`: domain disebut hanya kalau sisa harinya PERSIS salah
  satu angka itu (keputusan user: titik tertentu, bukan tiap hari selama sebulan). Sisa 20 hari atau 31 hari
  dilewati. Domain yang SUDAH LEWAT sengaja tak ikut (kalau ikut, ditagih tiap pagi selamanya).
- Jadwal `domains:remind` 08:00 DIMATIKAN di routes/console.php supaya tidak dobel dengan pesan 07:00.
  Command-nya TETAP ADA untuk dipanggil manual.
- EM DASH: 22 kemunculan di 6 file dibuang (BudgetController, BudgetExcelService termasuk teks yang dilihat
  user di file Excel export, budgets/index + show, dashboard/index, expenses/create). Kini grep em/en dash
  di app+resources+routes+config+database+public NIHIL.
  AWAS: garis pemisah komentar `──` itu BOX DRAWING (U+2500), BUKAN dash. Jangan ikut diganti.
- UJI: 52 uji SQLite lulus semua (30 sumber + 22 digest/command). Lint bersih, view:cache OK.

### 2026-08-20 (Pengingat Telegram: isi kalender hari ini tiap pagi 07:00)
Permintaan user. Command baru `calendar:remind` + service `DailyDigest`. Detail di DOKUMENTASI.md.
- CAKUPAN (keputusan user): SEMUA isi kalender hari ini (agenda pribadi + deadline proyek + domain +
  maintenance + jatuh tempo hutang piutang). TODO TIDAK IKUT. Hari kosong TIDAK dikirim (anti-spam).
- `app/Services/Calendar/DailyDigest.php`: kumpulkan isi 1 tanggal jadi teks berkelompok + ikon.
  Nama hari/bulan Indonesia ditulis sendiri (APP_LOCALE masih `en`, tak diubah karena berdampak global).
- `app/Console/Commands/CalendarRemind.php`: opsi `--date` `--user` `--force` `--dry` (dry = tampilkan
  saja, tidak mengirim; enak buat uji di hosting). Dijadwalkan `dailyAt('07:00')` di routes/console.php.
- Aturan tanggal per sumber SENGAJA diduplikasi dari CalendarController (feed butuh rentang + payload
  FullCalendar, digest cuma 1 tanggal + teks). Ada komentar silang: ubah satu, ubah keduanya.
- CRON: SUDAH DIPASANG user 2026-08-20 di hPanel dan TERVERIFIKASI JALAN
  (`* * * * * /usr/bin/php /home/u137841455/domains/strack.my.id/public_html/artisan schedule:run`).
  Sebelum itu daftar cron TIDAK punya entri strack sama sekali (yang ada menunjuk horawranghae.com),
  jadi `domains:remind` sejak 2026-08-13 memang tidak pernah jalan. Cara verifikasi cron tanpa menunggu
  jadwal: tambah `Schedule::call(...)->everyMinute()` penanda LANGSUNG di hosting, tunggu ~2 menit, cek
  file penanda, lalu `git checkout -- routes/console.php` (tak perlu deploy, repo hosting kembali bersih).
- Sekalian: em dash di pesan Telegram `DomainsRemind` diganti titik dua (melanggar aturan gaya).
- UJI: SQLite in-memory, 21 uji LULUS semua. Catatan: SQLite simpan kolom date sebagai datetime penuh
  sehingga scopeInRange meleset -> di skrip uji tanggal dinormalkan; bukan masalah di MySQL.

### 2026-08-20 (Kalender: todo tidak lagi tampil di kotak tanggal)
Permintaan user: todo rutin bikin tampilan bulanan spam. SEMUA todo (bukan cuma yang berulang) dikeluarkan
dari kotak tanggal, cukup di panel kanan. Agenda tetap seperti biasa. Detail di DOKUMENTASI.md.
- `CalendarEvent::expandRange()` +param `?string $type` (EVENT/TODO/null). `getEventsForMonth()` (kalender
  Dashboard) & `CalendarController::ownEvents()` (feed FullCalendar) kini dikunci ke `TYPE_EVENT`.
- View kalender: label filter "Agenda & Todo" -> "Agenda"; catatan `#todoNotice` muncul saat tipe Todo dipilih.
- TIDAK ada perubahan skema, TIDAK ada delta SQL. Deploy cukup push + pull.
- Panel Todo tak tersentuh (query terpisah). Todo tetap bisa diedit/dihapus lewat klik badan todo di panel.
- KONSEKUENSI: todo sekali jalan yang sudah selesai tak terlihat lagi di kalender, sisa jejaknya di daftar
  "Selesai terakhir" (20 terbaru). Mau dibalikkan? Tambah sumber filter baru (mis. `own_todo`); param `$type`
  sudah menyiapkan jalannya.
- UJI: MySQL lokal mati lagi -> SQLite in-memory, 17 uji LULUS semua (feed, panel todo, dashboard,
  monthEvents, centang berulang). Lint bersih, view:cache OK, render 95 KB.

### 2026-08-19 (Kalender: agenda & todo BERULANG / terjadwal)
Lanjutan menu Kalender. Agenda + todo bisa dijadwalkan berulang. SUDAH commit `f75efa6` + delta SQL
diterapkan + deploy + smoke test produksi. Detail di DOKUMENTASI.md.
- KOLOM BARU di `calendar_events` (migrasi `2026_08_19_000002`, delta `database/sql/2026_08_19_calendar_recurrence.sql`,
  batch 9): repeat_type (NULL=sekali jalan; DAILY/WEEKDAY/WEEKLY/MONTHLY/YEARLY) + repeat_interval +
  repeat_days (CSV 0-6) + repeat_day_of_month (1-31 atau -1=akhir bulan) + repeat_until. Data lama tak berubah.
- TABEL BARU `calendar_event_completions` (event_id FK cascade, occurrence_date, completed_at, unique pasangan):
  centang selesai PER TANGGAL. Kolom `is_done` HANYA untuk data sekali jalan (berulang dipaksa false).
- Rangkaian disimpan SATU BARIS, kemunculan DIHITUNG saat dibaca (`occurrencesBetween`, pagar MAX_OCCURRENCES=500).
  MONTHLY: bulan tanpa tanggalnya DILEWATI (bukan digeser). YEARLY 29 Feb hanya tahun kabisat.
  WEEKLY: minggu mulai hari MINGGU (samakan dengan firstDay:0 FullCalendar).
- PANEL TODO: 1 baris per rangkaian lewat `activeOccurrence` (ambil kemunculan <=hari ini yang belum dicentang
  dan PALING BARU, abaikan yang di bawah centang terakhir; kalau tak ada, ambil berikutnya). Jadi todo harian
  menunjuk HARI INI, dan sekali dicentang langsung lompat ke besok. Lookback 92 hari, lookahead 400 hari.
- BATASAN SENGAJA: kemunculan berulang TIDAK bisa drag & drop (`editable:false`, `move` tolak 422); edit
  berlaku SELURUH rangkaian; ubah aturan/tanggal mulai MENGHAPUS riwayat centang (ubah judul/warna tidak).
- UJI: MySQL lokal mati lagi -> SQLite sementara, 81 uji LULUS semua. Lint bersih, view:cache OK, 9 route tetap,
  render halaman 95 KB. Uji visual browser: PENDING user.
- URUTAN WAJIB: delta SQL di hosting DULU, baru deploy kode (kalau dibalik, kalender + dashboard error).
  SUDAH dijalankan: batch 8 -> 9, 6 agenda lama utuh & tetap sekali jalan.
- CARA APPLY DELTA VIA SSH yang enak: `scp file.sql saktify:~/` lalu jalankan skrip sh kecil (baca DB_* dari
  .env, `export MYSQL_PWD`, `mysql -u"$US" "$DB" < file.sql`). Hindari `mysql -e "..."` (kutip-ganda dihapus transport).
- SMOKE TEST PRODUKSI OK: /calendar 200 (95 KB), /calendar/todos 200, /dashboard-admin 200. Uji tulis:
  "hari kerja" 1-11 Sep = 9 kemunculan (Sabtu/Minggu dilewati, berhenti di repeat_until); "Sel+Kam tiap 2
  minggu" = 1,3,15,17,29 Sep; centang 1 Sep -> panel pindah ke 2 Sep; centang tanpa tanggal 422; move
  rangkaian berulang 422. Data uji sudah dihapus.
- Uji visual browser: PENDING user.

### 2026-08-19 (Domain: provider diseragamkan jadi Hostinger)
Permintaan user (semua domain 1 hosting). Perubahan DATA di produksi lewat SSH, bukan skema, jadi tak ada
file delta. Detail di DOKUMENTASI.md.
- Awal: 50 domain, 32 provider NULL + 18 sudah "Hostinger", tak ada nilai lain (dicek dulu, tak ada yang tertimpa).
- `UPDATE domains SET provider='Hostinger' WHERE provider IS NULL OR provider=''` -> 32 baris. IDEMPOTEN.
- Hasil: 50/50 Hostinger, halaman /domains 200.
- PENDING: `starvvoindonesia.com` (id 52) belum punya `expires_at` -> tak kena reminder domains:remind.
  Opsional: default provider "Hostinger" di `DomainController::sync()` supaya domain baru tak kosong lagi.

### 2026-08-19 (menu Kalender: agenda + todo, gaya Google Calendar)
Menu baru "Kalender" (`/calendar`) halaman penuh. SUDAH commit `63bcb9d` + delta SQL diterapkan + deploy +
smoke test produksi. Detail di DOKUMENTASI.md.
- TABEL BARU `calendar_events` (migrasi `2026_08_19_000001`, delta `database/sql/2026_08_19_calendar_events.sql`,
  batch 8): user_id/title/description/type(EVENT|TODO)/start_date/end_date/start_time/end_time/all_day/color/
  is_done/completed_at. `calendar_notes` LAMA tidak di-drop, isinya dipindah lewat INSERT..SELECT (jalankan
  SEKALI). Model `CalendarNote` + `CalendarNoteController` DIHAPUS -> `CalendarEvent` + `CalendarController`.
- UI: FullCalendar 6 via CDN (build global MENYUNTIKKAN CSS sendiri, tak ada file CSS terpisah). View Bulan/
  Minggu/Hari/Agenda, drag & drop, klik-seret buat agenda, panel filter sumber (localStorage) + daftar todo.
- IKUT TAMPIL read-only: deadline proyek (WAITING/PROGRESS), domain (`expires_at`), maintenance (DATE/MONTH/
  YEAR; TEXT & ODOMETER dilewati), hutang piutang (`due_date`, status != PAID).
- DASHBOARD: kalender lama TETAP, sumber data pindah ke CalendarEvent, 3 URL fetch -> `/calendar/events*`,
  + tombol "Buka Kalender" & "Buka di Kalender" (deep link `?date=YYYY-MM-DD`).
- URUTAN PENERAPAN WAJIB: delta SQL di hosting DULU, baru deploy kode (kalau dibalik, dashboard error).
- UJI: MySQL lokal mati -> diuji via SQLite sementara (feed 5 sumber, filter, todo, CRUD, cek kepemilikan).
  Di PRODUKSI (login curl): /calendar 200, feed Agu-Sep = 7 event (2 proyek, 4 domain, 1 maintenance),
  5 catatan lama utuh di bulan aslinya, buat+baca+hapus agenda OK lalu dibersihkan. Uji visual browser: PENDING user.
- TAMPILAN `1e4829e` (permintaan user, sudah deploy): tinggi kalender dihitung agar mengisi 1 layar
  (`innerHeight - offsetTop - 28`, min 560px; hitung ulang saat `load` + `resize`; di bawah 992px tetap auto),
  `dayMaxEvents: true`, tipografi diperbesar, panel samping sticky. Menu Kalender di sidebar dipindah ke
  ATAS Sierra Berak.

### 2026-08-13 (bangun + deploy bot Telegram tanya-data, Text-to-SQL read-only - FASE 1)
Rancangan bot Telegram 2026-08-12 DIEKSEKUSI. Bot read-only sudah jadi + deploy + teruji. Commit
`b379119`, HEAD hosting sama. Bot: **t.me/Saktify_strack_bot**. Detail lengkap di DOKUMENTASI.md.
- ALUR: Telegram -> `POST /telegram/webhook` -> `TextToSqlService`: AI Haiku ubah pertanyaan ID jadi
  SELECT (skema DB sbg konteks) -> `SqlGuardrail` validasi -> jalankan di koneksi `mysql_ro` -> AI
  rangkai jawaban ID. Anthropic via `Http` facade + prompt caching. File baru: `app/Services/Ai/*`
  (AnthropicClient, SchemaInspector, SqlGuardrail, TextToSqlService), `app/Services/Telegram/
  TelegramService`, `TelegramWebhookController`, command `telegram:set-webhook`. Diedit: config
  services/database (koneksi `mysql_ro`), bootstrap/app (CSRF except), routes/web, .env.example.
- KEAMANAN berlapis (semua diuji di produksi): secret webhook (tanpa=403), whitelist chat_id
  (`8588404484` saja), guardrail SELECT-only (tolak UPDATE/DELETE/DROP/`;`/komentar, paksa LIMIT),
  koneksi read-only + timeout. Uji: "ada berapa total proyek?" -> "Ada 215 total proyek" BENAR.
- KENDALA HOSTING: user MySQL read-only TIDAK BISA dibuat (user DB hosting tak punya `CREATE USER`;
  hPanel Hostinger tak beri privilege SELECT-only granular). KEPUTUSAN: `mysql_ro` fallback ke DB utama
  (DB_RO_* SENGAJA tak ditulis di .env agar env() null->fallback), keamanan tulis diandalkan guardrail.
- KREDENSIAL hanya di .env HOSTING (bukan repo): ANTHROPIC_API_KEY, TELEGRAM_BOT_TOKEN,
  TELEGRAM_WEBHOOK_SECRET, TELEGRAM_ALLOWED_CHAT_IDS. Backup `.env.bak.telegram.*` dibuat. API key +
  bot token sempat di chat -> user sebaiknya regenerate lalu update .env.
- FASE 2 SELESAI (fitur TULIS insert/update) `9c7811f`: aksi terdefinisi via TOOL USE + KONFIRMASI.
  Tiap pesan diklasifikasi AI -> BACA (`tanya_data`->TextToSqlService) atau TULIS (1 aksi + ekstrak data).
  Tulis lewat model/controller yang ada (validasi saldo/sisa + sinkron tetap jalan), SELALU konfirmasi
  dulu (aksi tertunda di cache per chat_id TTL 5mnt, eksekusi saat balas "ya"). 6 aksi di
  `app/Services/Ai/Actions/`: catat pengeluaran/pendapatan/bayar-hutang-piutang, update status proyek,
  transfer bank, Sierra Berak. Base `WriteAction` + trait `ResolvesProject` + `ActionRegistry` +
  `BotOrchestrator`; `AnthropicClient` +raw()/extractToolUse(). Teruji di hosting (baca 215 OK; pengeluaran
  ditolak krn saldo cash Rp6.800 < 15rb; Sierra Berak create+konfirmasi+cleanup; routing 4 aksi benar).
  Tulis pakai koneksi default (mysql RW); baca `mysql_ro`. Tambah aksi baru: turunkan WriteAction +
  daftar di ActionRegistry.
- FASE 3 SELESAI (VOICE NOTE) `ff53050`: VN Telegram ditranskrip via GROQ (Whisper large v3, tier GRATIS,
  endpoint kompatibel OpenAI) -> teks -> pipeline bot sama. Anthropic API tak terima audio, makanya STT
  terpisah. File: `TranscriptionService`, `TelegramService::downloadFile`, config `services.groq`, cabang
  voice di controller. Balasan VN diberi prefix transkripsi biar user verifikasi. GROQ_API_KEY di .env
  hosting. Teruji: endpoint 200 (WAV senyap -> "Terima kasih" halusinasi). VN asli berucap: user uji sendiri.
- FAILOVER 2 AI `054dcca`: Gemini (Google AI Studio, GRATIS) PRIMER, Claude CADANGAN (hemat biaya).
  `AiGateway` coba provider urut `services.ai.primary` (default gemini->anthropic); gagal/tak respons ->
  jatuh ke berikutnya; tanpa kredensial dilewati. `AiProvider` interface + `AiResult`, `AnthropicProvider`
  (bungkus AnthropicClient) + `GeminiProvider` (terjemah tool Anthropic->Gemini functionDeclarations, type
  UPPERCASE, parse functionCall). BotOrchestrator/TextToSqlService lewat AiGateway. Failover HANYA saat
  Gemini error (keputusan user). BACKWARD-COMPATIBLE: GEMINI_API_KEY kosong -> Claude saja. AKTIF+TERUJI:
  model `gemini-flash-lite-latest` (gemini-2.0-flash sudah 404; flash-latest THINKING -> output truncation).
  Auth header `X-goog-api-key` (key baru `AQ.`). Gemini tangani baca+tulis+validasi OK; Claude cadangan.
- BOT UX (sama hari): (a) PENANDA AI `fec6d87` di awal balasan 🔵 Gemini / 🟠 Claude
  (`AiGateway::lastProvider`, ditambah di `BotOrchestrator::finish`, hanya bila ada panggilan AI, di-reset
  tiap pesan); (b) INGATAN percakapan `805d808` (6 giliran terakhir per chat di cache `tg_history:{id}` TTL
  30mnt -> rujukan "tadi/tersebut" nyambung); (c) TRANSFER BANK `e60f9ff`+`c7a01bc`: proyek OPSIONAL
  (kosong=transfer SEMUA yg belum ditransfer) & tak butuh nominal; (d) FIX GLOSARIUM `8f2978b`: tambah
  aturan bisnis ke prompt Text-to-SQL (piutang=sisa proyek status WAITING/PROGRESS saja; penjualan
  kecualikan CANCELLED+LEAD; pendapatan=payments) krn AI sempat salah soal "piutang". GROQ_API_KEY +
  GEMINI_API_KEY di .env hosting (sempat di chat -> sebaiknya regenerate).

### 2026-08-13 (strack UI: Print Invoice opsi, kolom Nilai proyek, modul Domain & Hosting)
Pekerjaan strack di LUAR bot, dilakukan setelah bot selesai (sama hari). Semua commit+deploy. Detail penuh
di DOKUMENTASI.md.
- PRINT INVOICE `8cb02cd`,`2a6e971`,`8e49e1b`: gabung 2 tombol (Print Quotation + Print Invoice) jadi 1
  dropdown "Print Invoice" -> Quotation, Invoice Penuh, Down Payment, Progress, Pelunasan. Jumlah ditagih
  MANUAL (default total_value, editable di preview). Template invoice: label tahap di header + box "Rincian
  Pembayaran Proyek" (Nilai, DP, Pelunasan, Sudah Dibayar, Ditagih invoice ini, Sisa) HANYA utk DP/Progress/
  Pelunasan (Invoice Penuh TANPA rincian). File: `ProjectInvoiceController` (type+billed_amount+stageLabel+
  paymentInfo), views `invoice-preview`, `invoice-general`, `invoice` (BTOOLS), `show.blade`.
- KOLOM NILAI PROYEK `404e172` di /projects: kolom "Nilai" sortable (total_value) di tabel desktop + baris
  nilai di card mobile. `ProjectController`: +case sort 'total_value'.
- MODUL DOMAIN & HOSTING `9134fe9`,`533c464`: menu baru "Domain & Hosting". Tabel `domains` (migrasi batch 7,
  delta `database/sql/2026_08_13_domains.sql`; kolom name/client_id/project_id/provider/registered_at/
  expires_at/renewal_cost/is_hosted/notes). CRUD + tautan Klien (opsional). Status kedaluwarsa dihitung
  (EXPIRED/EXPIRING_SOON<=30h/ACTIVE) + badge + filter + ringkasan. REMINDER Telegram: command
  `domains:remind {--days}` (dijadwalkan `dailyAt 08:00` di routes/console.php; PENDING user pasang cron
  hPanel `php artisan domains:remind`). SYNC dari hosting: `sync()` baca folder `~/domains` via `scandir`
  (exec disabled + open_basedir KOSONG -> boleh; whois tak ada -> expiry MANUAL). FORM: select Klien
  SEARCHABLE (Select2, jQuery sudah ada) + tampil nomor; select Project DIGANTI tombol "Show Project" ->
  `clients.show` (tab baru, aktif bila klien dipilih). File: `Domain` model, `DomainController`,
  `DomainsRemind`, `domains/{index,form}.blade`, config `services.hosting.domains_path`, routes web/console,
  sidebar. Fakta hosting penting: exec/whois/crontab CLI TAK ADA, open_basedir kosong, php /usr/bin/php.
- DATA: 50 domain strack di-set tanggal kedaluwarsa dari daftar user (total 66; 16 diabaikan krn tak ada di
  strack, sesuai instruksi). Semua 50 kini punya expiry; terdekat langensari06.site 01 Sep 2026.

### 2026-08-12 (hapus integrasi Midtrans + rancangan bot Telegram AI)
Dua topik. (1) Hapus Midtrans (ada kode+DB). (2) DISKUSI rancangan bot Telegram+AI (BELUM ADA KODE).

RANCANGAN BOT TELEGRAM + AI (belum dikerjakan, hanya diskusi/keputusan):
- Tujuan: user bisa tanya data strack lewat Telegram (bahasa Indonesia), dijawab AI. Alur: Telegram bot
  -> webhook Laravel (`/telegram/webhook`) -> AI -> query DB -> jawab balik.
- KEPUTUSAN condong: pendekatan = TEXT-TO-SQL (bukan tools terdefinisi); penyedia = ANTHROPIC API
  (console.anthropic.com), model Haiku (`claude-haiku-4-5`, bisa ganti Sonnet `claude-sonnet-4-6`);
  implementasi via `Http` facade tanpa SDK. Text-to-SQL WAJIB pakai user MySQL read-only + guardrail
  (SELECT saja, blokir DROP/UPDATE/DELETE, timeout, LIMIT). Security: whitelist chat_id + secret webhook.
- Sudah dijelaskan ke user: Claude API (console) TERPISAH dari langganan Pro (Pro=chat claude.ai; API=
  dipanggil program, prabayar per token, bisa set spend limit). Estimasi biaya pribadi ringan ~$1-5/bln (Haiku).
- PENDING: user daftar console + isi credit + API key; buat bot @BotFather (token + chat_id). Setelah itu
  Claude bangun sisi Laravel. Detail lengkap di DOKUMENTASI.md.

HAPUS MIDTRANS (sudah selesai): Fitur pembayaran otomatis Midtrans DIBATALKAN dan dihapus seluruhnya dari
kode + DB produksi. Commit `4959ca6` (kode) + `245db58` (dok), sudah push + deploy + drop objek DB via ssh.
Maintenance dianggap SOLVE (tak diubah). Model `Payment`/`PaymentController` (Pendapatan) TETAP ada, hanya
bagian Midtrans yang dicabut.
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
