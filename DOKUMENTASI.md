# Dokumentasi Sesi - strack

Log pekerjaan per sesi. Sesi terbaru di atas.

---

## Sesi 2026-08-21 (Sinkron folder kerja lokal: dibangun lalu DIBATALKAN user)

Permintaan awal user: pastikan `D:\Project Saktify` hanya berisi pekerjaan yang masih jalan; folder yang
proyeknya sudah tutup di-RAR ke `E:\Backup Joki`; proyek jalan yang belum punya folder dikabari.

Fitur SUDAH selesai dibangun, diuji, dan deploy (`0f1eb86` + `6d88302`), lalu USER MEMBATALKANNYA dengan
alasan pengaturan folder mau dikerjakan MANUAL saja. Dibatalkan lewat `git revert` (`ab08047`), bukan
menghapus riwayat, jadi kodenya masih utuh di dua commit itu kalau suatu saat mau dihidupkan lagi.

Yang dihapus kembali: `app/Console/Commands/ProyekDaftar.php` (command `proyek:daftar --json`),
`scripts/sinkron-folder-proyek.ps1`, dan catatan dokumentasinya. Sudah diverifikasi hilang di hosting
(`proyek:daftar` tidak terdaftar lagi, isi `scripts/` kembali cuma `deploy.ps1`).

TIDAK ADA sisa di PC user: `D:\Project Saktify` dan `E:\Backup Joki` tidak berubah sama sekali, file peta
`_peta-folder-proyek.json` tidak pernah tertulis (skrip dijalankan dengan `-TanpaTanya`), dan seluruh folder
percobaan di scratchpad sudah dibersihkan.

Fakta hasil penelusuran yang tetap berguna kalau topik ini muncul lagi:
- Folder kerja berpola `Project_<Klien>_<Judul>`, tiap folder berisi `CLAUDE.md`, `DOKUMENTASI.md`, dan
  `.bat` pelanjut sesi Claude Code yang menunjuk path absolutnya. Ada juga `Saktify_Assistant` (bukan proyek).
- `E:\Backup Joki` sudah berisi 103 arsip berpola `Project_X.rar` di akarnya, isi arsip memuat folder induk.
  WinRAR ada di `C:\Program Files\WinRAR\Rar.exe`.
- `Project_SMG_Item_Gantung` = proyek #222 PT Sinai Maju Gemilang (SMG itu singkatan nama PT-nya).
- strack di hosting TIDAK BISA melihat drive lokal, jadi pekerjaan seperti ini memang harus dari sisi PC.

JANGAN tawarkan ulang otomatisasi folder kerja kecuali user sendiri yang memulai.

---

## Sesi 2026-08-21 (Bukti transfer di Telegram jadi transfer bank otomatis)

Permintaan user: kirim foto bukti transfer ke bot, nominalnya dicocokkan dengan total pembayaran yang
BELUM ditransfer ke Bank Octo, kalau pas langsung dicatat sebagai transfer.

KEPUTUSAN user (ditanyakan di awal):
1. Pencocokan hanya ke TOTAL KESELURUHAN. Kalau nominal tidak sama persis, bot MENOLAK dan melaporkan
   selisihnya. Tidak ada pencarian kombinasi sebagian (transfer cicilan dicatat manual lewat aplikasi).
2. Tetap minta konfirmasi "ya" walau nominalnya sudah pas (konsisten dengan aksi tulis lain).
3. Pencegahan duplikat BELUM dibuat. Bukti yang sama terkirim dua kali akan tercatat dua kali selama
   masih ada pembayaran yang belum ditransfer.

### Alur
Foto ke Telegram -> `TelegramWebhookController` -> `BotOrchestrator::handleImage` (BARU, menggantikan
`handleReceipt` sebagai pintu masuk gambar) -> `TransferProofParser` memilah jenis gambar:
- TRANSFER -> `CatatTransferBuktiAction` (cocokkan + rekap + konfirmasi) -> beberapa `BankTransfer`
  dibuat sekaligus dalam satu transaksi + `BankBalance::updateBalance()`.
- STRUK atau tidak jelas -> alur struk belanja LAMA, tidak berubah sama sekali.

### File baru
- `app/Services/Ai/TransferProofParser.php`: satu panggilan AI vision yang sekaligus MEMILAH jenis gambar
  (TRANSFER / STRUK / LAIN) dan MEMBACA isi bukti transfer (nominal, tanggal, ref, bank, keterangan).
  Digabung supaya jalur bukti transfer cukup 1 panggilan AI; jalur struk jadi 2 panggilan (pemilah +
  pembaca struk). `int()` tahan format "IDR 415,000.00", "Rp 1.250.000,00", dan "415.000".
  Balasan AI yang tidak berbentuk JSON sengaja dianggap STRUK supaya perilaku lama tidak berubah.
- `app/Services/Ai/Actions/CatatTransferBuktiAction.php`: aksi tersembunyi (tidak ditawarkan ke AI
  sebagai tool). `prepare()` mengambil semua `payments` dengan `is_transferred = false`, menjumlahkannya,
  lalu MEMBANDINGKAN di kode (bukan di AI). Tidak sama = lempar RuntimeException berisi nominal bukti,
  total belum transfer, selisih, dan rincian per pembayaran (maksimal 12 baris). Sama = rekap +
  konfirmasi. `execute()` MENGECEK ULANG totalnya karena data bisa berubah selagi menunggu konfirmasi.
- `app/Console/Commands/TransferCoba.php`: `php artisan transfer:coba <file> [--simpan] [--catatan=]`,
  kembaran `struk:coba` untuk menguji baca bukti transfer tanpa Telegram.

### Perubahan file lain
- `app/Services/Ai/BotOrchestrator.php`: `handleReceipt()` jadi PRIVATE, pintu masuk gambar sekarang
  `handleImage()`. Tambah `handleTransferProof()`. Reset penanda provider + buang aksi tertunda
  dipindah ke `handleImage`.
- `app/Services/Ai/Actions/ActionRegistry.php`: daftarkan `CatatTransferBuktiAction`.
- `app/Http/Controllers/TelegramWebhookController.php`: panggil `handleImage`, pesan error jadi
  "gambar" (bukan "struk"), teks /help ditambah bagian bukti transfer.

### Catatan penting
- TIDAK ada perubahan skema DB, tidak ada delta SQL. Kolom `bank_transfers.reference_number` yang sudah
  ada dipakai untuk menyimpan nomor referensi dari bukti (mis. 760967985900), `notes` diisi
  "Via bukti transfer di bot Telegram".
- Kolom "arah" (MASUK/KELUAR) SEMPAT dibuat lalu DIBUANG: pada bukti asli user ("TRF TO OCTO PAY")
  Gemini membacanya KELUAR, padahal uangnya masuk. Peringatan arah jadi salah alarm, sedangkan
  pencocokan nominal sudah cukup sebagai pagar. Jangan dihidupkan lagi tanpa cara baca yang lebih andal.
- Aksi tertunda bukti transfer berlaku 20 menit, sama seperti struk.
- Duplikat: kalau bukti yang sama dikirim dua kali BERTURUT-TURUT, yang kedua otomatis ditolak karena
  sudah tidak ada pembayaran belum-transfer yang cocok. Yang belum tertutup adalah kasus ada pembayaran
  baru dengan total kebetulan sama persis.

### Konfirmasi longgar + reaksi jempol (lanjutan hari yang sama)
Permintaan user: kata "ya" sering tidak terdengar lewat voice note, dan maunya bisa mengonfirmasi cukup
dengan REAKSI emoji jempol pada pesan bot.
- KATA: daftar AFFIRM/DENY diperluas (lakukan, kerjakan, konfirm, konfirmasi, sip, siap, acc, eksekusi,
  jalankan, proses, silakan, mantap, dst). Pengecekan tidak lagi harus SATU KATA PERSIS: balasan sampai
  4 kata diterima selama semua katanya ada di AFFIRM/DENY atau FILLER ("oke lakukan", "ya simpan aja",
  "tidak usah"). Ada satu kata di luar daftar = dianggap perintah/koreksi baru ("ok tapi ganti tanggalnya"
  tetap masuk alur koreksi). Kalau tercampur, yang MENOLAK menang ("ya jangan" = batal).
- `normalize()` kini mengubah tanda baca jadi spasi dan memendekkan huruf berulang ("iyaaa" jadi "iya",
  "okeee" jadi "oke"), berguna untuk hasil transkripsi voice note.
- REAKSI EMOJI: Telegram mengirimnya sebagai update `message_reaction`, dan update itu HANYA dikirim bila
  `allowed_updates` menyebutnya. `telegram:set-webhook` diperbarui, dan webhook WAJIB didaftarkan ULANG
  setelah deploy (kalau tidak, reaksi tidak akan pernah sampai).
- Setuju: 👍 👌 ✅ ✔ ☑ 🆗 💯 🤝 🔥 ❤ 🎉. Batal: 👎 ❌ 🚫 ⛔ 🙅. Emoji lain diabaikan diam-diam.
  Penanda gaya emoji (variation selector U+FE0F dan warna kulit U+1F3FB-1F3FF) dibuang dulu sebelum dicocokkan.
- PENGAMAN: reaksi hanya dihitung bila mengenai PESAN REKAP TERAKHIR. `TelegramService::sendMessage`
  sekarang mengembalikan message_id, controller menyimpannya di cache `tg_confirm_msg:{chat}` (20 menit)
  hanya bila memang ada aksi menunggu. Reaksi di pesan lain diabaikan.
- File: `BotOrchestrator` (+verdict, +reactionVerdict, +resolvePending, +hasPending, FILLER,
  MAX_CONFIRM_WORDS), `TelegramService::sendMessage` jadi `?int`, `TelegramWebhookController`
  (+cabang message_reaction, +reply(), +allowed(), teks /help), `TelegramSetWebhook` (+allowed_updates).
- UJI: 85 uji baru LULUS semua (44 kata setuju, 21 kata batal, 7 kalimat yang HARUS tetap netral,
  9 reaksi emoji, 4 resolvePending/hasPending) + 34 uji bukti transfer diulang, tetap lulus.

### Verifikasi
- Uji lokal (SQLite in-memory, MySQL lokal masih mati): **34 uji, 34 LULUS**. Cakupan: pembacaan angka
  berbagai format, pemilahan jenis gambar, JSON berantakan, nominal pas / lebih besar / lebih kecil /
  selisih Rp1, tidak ada pembayaran belum transfer, nominal tak terbaca, pemotongan rincian panjang,
  isi rekap, penyimpanan (BankTransfer per pembayaran + saldo naik), simpan dua kali, data berubah saat
  menunggu konfirmasi, dan alur bot (rekap lalu "ya", "tidak", struk tetap ke alur struk, gambar tak
  dikenal, gambar baru membuang aksi tertunda, caption ikut terkirim, perintah teks transfer lama).
- CATATAN UJI: migrasi lengkap TIDAK bisa dipakai di SQLite (ada migrasi lama yang meng-alter
  `budget_items` sebelum tabelnya dibuat). Skrip uji membuat sendiri tabel yang dipakai lewat `Schema`.
  Sama seperti sesi struk: `Http::fake()` menumpuk antrean, jadi `Http::swap(new Factory())` dulu.
- Lint bersih, `view:cache` sukses, `transfer:coba` terdaftar, tidak ada em dash / en dash.
- UJI BACA DENGAN GAMBAR ASLI user (bukti OCTO Rp415.000) lewat Gemini: SEMUA benar - nominal 415000,
  tanggal 2026-08-21, ref 760967985900, bank OCTO, keterangan "SAKTI PUTRA S", jenis TRANSFER.
- SUDAH commit + deploy (`060da51` kode, `8c61ce0` dokumentasi).
- UJI DI PRODUKSI dengan foto bukti transfer ASLI user lewat `php artisan transfer:coba ~/bukti-uji.jpeg`
  (file uji sudah dihapus lagi): dibaca Gemini, jenis TRANSFER, nominal 415000, tanggal 2026-08-21,
  ref 760967985900, bank OCTO, keterangan "SAKTI PUTRA S". Semua benar.
  Rekapnya cocok dengan 2 pembayaran belum transfer di produksi (Lisa Cust - Revisi Website Rp15.000 dan
  Rendi Fuji - Revisi Tesis Rp400.000, total persis Rp415.000).
- PENDING: user mengirim bukti transfer langsung lewat Telegram (belum dilakukan sampai sesi berakhir).

---

## Sesi 2026-08-20 (Foto struk di Telegram jadi pengeluaran otomatis)

Permintaan user: belanja lewat aplikasi Alfagift merepotkan dicatat karena harus dirinci, dikelompokkan
per kategori, baru diinput satu per satu. Sekarang cukup KIRIM FOTO STRUK ke bot Telegram, bot merekap
per kategori, user tinggal balas ya.

POLA LAMA YANG DITIRU (dibaca dulu dari 80 pengeluaran berawalan "Alfagift" di produksi): satu struk
dipecah jadi BEBERAPA baris pengeluaran, satu baris per kategori, deskripsi "Alfagift - Barang, Barang",
sumber selalu BANK, dan nominalnya tidak bulat (tanda bahwa potongan memang sudah dibagi ke item).

KEPUTUSAN user (ditanyakan di awal):
1. VOUCHER (potongan seluruh belanja) dibebankan ke kategori ENTERTAIN lebih dulu. Kalau belanjaan itu
   tidak punya item Entertain, atau nilainya tidak cukup, sisanya dibagi PROPORSIONAL ke kategori lain.
   Diskon per item tetap dipotong di itemnya sendiri.
2. Sebelum disimpan, rekap BISA DIKOREKSI lewat balasan biasa (bukan cuma ya/tidak).
3. Cakupan: struk toko APA PUN (nama toko dibaca dari struk dan jadi awalan deskripsi), dengan
   pengelompokan yang tetap belajar dari kebiasaan pencatatan sendiri.

### Alur
Foto ke Telegram -> `TelegramWebhookController` (ambil foto resolusi terbesar / dokumen image/*) ->
`TelegramService::downloadFile` -> `BotOrchestrator::handleReceipt` -> `ReceiptParser` (AI vision, balas
JSON item + kategori) -> `ReceiptTally` (hitung voucher, kelompokkan per kategori) -> `CatatStrukAction`
(rekap + konfirmasi, disimpan sebagai aksi tertunda di cache) -> balas "ya" -> beberapa `Expense` dibuat
sekaligus dalam satu transaksi.

### File baru
- `app/Services/Ai/ReceiptParser.php`: kirim gambar + prompt ke AI, terima JSON (toko, tanggal, ref,
  items[nama/qty/harga/diskon/kategori], voucher, ongkir, total). `decodeJson()` tahan blok kode dan
  kalimat pengantar; `normalize()` membuang item tanpa nama, membatasi diskon <= harga, qty minimal 1,
  kategori asing jadi LAINNYA, angka bertitik/negatif dirapikan.
  KATEGORI TIDAK DITEBAK DARI DAFTAR KATA KUNCI DI KODE. `categoryExamples()` membaca 400 pengeluaran
  terakhir yang deskripsinya mengandung "alfa", memecah deskripsinya jadi nama barang, lalu mengirimnya
  ke AI sebagai acuan ("SEMBAKO: Beras, Aqua Galon, ..."). Jadi kebiasaan lama otomatis diikuti (Bebelac
  dan Tango ke SIERRA, Kojiesan ke SKINCARE, dst) dan ikut menyesuaikan kalau kebiasaannya berubah.
  Di-cache 3 jam dengan key `receipt_category_examples`; kalau habis memperbaiki kategori lama dan ingin
  langsung terpakai, hapus cache itu.
- `app/Services/Ai/ReceiptTally.php`: SELURUH hitung-hitungan uang (sengaja tidak diserahkan ke AI).
  Voucher ke ENTERTAIN dulu, sisanya proporsional; sisa pembulatan ditambahkan ke kategori terbesar
  supaya jumlah semua baris SAMA PERSIS dengan total struk; kategori yang habis dimakan voucher tidak
  dicatat; ongkir (bila ada) jadi baris LAINNYA dengan nama item "Ongkir".
- `app/Services/Ai/Actions/CatatStrukAction.php`: aksi tulis banyak-baris. `prepare/preview/execute`
  seperti aksi lain, plus `refine()` untuk koreksi.
- `app/Services/Ai/Actions/NotACorrectionException.php`: penanda bahwa balasan user ternyata bukan koreksi.
- `app/Console/Commands/StrukCoba.php`: `php artisan struk:coba <file> [--simpan] [--catatan=]`.
  Menguji pembacaan struk TANPA lewat Telegram (default hanya menampilkan, tidak menyimpan). Dipakai untuk
  memeriksa ketepatan baca AI di hosting dengan gambar nyata.

### Perubahan file lain
- `app/Services/Ai/Providers/GeminiProvider.php` + `AnthropicProvider.php`: isi pesan kini boleh berupa
  daftar bagian `['type' => 'text'|'image', ...]`. Gemini menerjemahkannya ke `inline_data`
  (mime_type + base64), Anthropic ke blok `image.source.base64`. Pesan teks biasa tetap seperti dulu,
  jadi seluruh fitur bot lama tidak terpengaruh.
- `app/Services/Ai/Actions/WriteAction.php`: tambahan `hidden()`, `pendingTtlMinutes()`, `supportsRefine()`,
  `refine()`. Semuanya punya default, aksi lama tidak perlu diubah.
- `app/Services/Ai/Actions/ActionRegistry.php`: menerima `AiGateway` (dibutuhkan aksi struk) dan
  MENYARING aksi tersembunyi dari daftar tool AI (aksi struk tidak bisa dipicu dari teks).
- `app/Services/Ai/BotOrchestrator.php`: `handleReceipt()`; cabang KOREKSI untuk aksi yang menunggu
  konfirmasi; masa berlaku aksi tertunda kini ditentukan aksinya (konstanta PENDING_TTL_MINUTES dibuang).
- `app/Http/Controllers/TelegramWebhookController.php`: cabang foto + helper `photoFrom()` (foto biasa
  ambil ukuran TERBESAR, atau dokumen ber-mime image/*; PDF diabaikan), teks /help ditambah.
- `app/Providers/AppServiceProvider.php`: `AiGateway` jadi SINGLETON. Ketahuan dari uji: penanda provider
  (biru/oranye) hilang karena ReceiptParser, aksi, dan orchestrator masing-masing memegang instance
  sendiri, sedangkan `lastProvider()` disimpan di instance.

### Bentuk percakapan
Kirim foto struk, bot membalas rekap: judul "Struk Alfagift - 20 Agustus 2026", sumber dana, lalu daftar
bernomor per kategori (nominal + nama barang + catatan di kategori mana voucher dipotong), ditutup jumlah
baris dan totalnya. Balas "ya" untuk simpan, "tidak" untuk batal, atau kalimat koreksi bebas
("tango masukkan ke sierra", "ini pakai cash", "tanggalnya kemarin"). Koreksi memakai AI HANYA untuk
memetakan barang ke kategori; nominalnya dihitung ulang di kode. Kalau balasan ternyata pertanyaan lain,
struk yang menunggu dibuang dan pesan diproses seperti biasa. Aksi tertunda struk berlaku 20 menit
(aksi lain tetap 5 menit).

### Catatan penting
- Caption foto ikut dikirim sebagai petunjuk ke AI, dan kata "cash"/"tunai" di caption mengubah sumber
  dana jadi CASH (default BANK, mengikuti kebiasaan belanja daring).
- Bila hasil baca tidak cocok dengan total di struk, rekap diberi tanda peringatan beserta selisihnya.
  Data tetap bisa disimpan (keputusan di user), tapi selisihnya terlihat jelas.
- Saldo dicek sebelum menyimpan, sama seperti `catat_pengeluaran`.
- TIDAK ada perubahan skema DB, tidak ada delta SQL. Deploy cukup push + pull.
- Struk yang sama bisa terkirim dua kali dan akan tercatat dua kali (belum ada penolakan duplikat).
  Nomor referensi struk sudah ikut dibaca (`ref`), jadi kalau nanti mau dicegah, jalannya sudah ada.

### Verifikasi
- Uji lokal (SQLite in-memory, MySQL lokal masih mati): **94 uji, 94 LULUS**. Cakupan: perhitungan voucher
  (ke Entertain, proporsional, voucher lebih besar dari Entertain, pembulatan, ongkir, tanpa voucher,
  voucher menutup seluruh belanja), pembacaan JSON AI yang berantakan, acuan kategori dari riwayat,
  aksi struk (rekap, simpan, saldo kurang, caption cash, peringatan selisih, koreksi kategori/sumber,
  balasan yang bukan koreksi), alur bot (konfirmasi, koreksi, ganti topik, struk kedua menimpa yang
  pertama, gambar gagal dibaca), dan webhook (foto resolusi terbesar, dokumen PNG, PDF diabaikan).
- Lint bersih, `view:cache` sukses, `struk:coba` terdaftar, tidak ada em dash / en dash.
- Uji dengan AI sungguhan dilakukan SETELAH deploy (kunci Gemini/Claude hanya ada di .env hosting).
  Hasilnya di bagian "Hasil di produksi" di bawah.
- CATATAN UJI: `Http::fake()` MENUMPUK stub, bukan mengganti. Antrean respons dari blok uji sebelumnya
  ikut terpakai dan memicu error "response sequence is empty". Solusi di skrip uji: `Http::swap(new
  Illuminate\Http\Client\Factory())` sebelum memasang fake baru. Skrip uji juga perlu
  `config(['session.driver' => 'array'])` karena default project memakai tabel sessions.


### Hasil di produksi (sama hari, sesudah deploy)
Deploy `b0bb828` + `0dabd10` lalu `5e00c2e`. Diuji dengan FOTO STRUK ASLI user lewat
`php artisan struk:coba ~/struk-uji.jpeg` di hosting (file uji sudah dihapus lagi).
- BACA AI (Gemini, `gemini-flash-lite-latest`) TEPAT: 5 item terbaca semua, harga + diskon per item
  benar, toko "Alfagift", tanggal 2026-08-20, total Rp72.600 sama persis dengan struk.
- Nama panjang dipendekkan sesuai kebiasaan ("Aqua Air Mineral Galon (Isi Ulang) 19 L" jadi "Aqua Galon",
  "Bebelac Susu Formula Cair Rasa Stroberi 105 ml" jadi "Bebelac").
- KATEGORI: Tango Wafer dan Kun Susu UHT ditaruh di SIERRA (bukan ENTERTAIN). Itu MEMANG ikut data lama,
  di riwayat Tango pernah masuk SIERRA ("Alfagift - Tango & Bebelac") maupun ENTERTAIN. Karena tidak ada
  kategori Entertain di struk itu, vouchernya dibagi proporsional (Sembako Rp799, Sierra Rp201) sesuai
  aturan. Kalau maunya beda, tinggal balas koreksi di Telegram.
- TEMUAN & PERBAIKAN (`5e00c2e`): saldo Bank Octo produksi saat itu Rp28.858, dan pengecekan saldo di
  `prepare()` membuat rekap TIDAK BISA DILIHAT SAMA SEKALI ("saldo tidak cukup"). Pengecekan dipindah ke
  `execute()`; rekap kini tetap tampil lengkap dengan peringatan berapa kekurangannya. Menyimpan saat
  saldo kurang tetap ditolak, sama seperti aksi pengeluaran biasa.
- Uji lokal setelah perbaikan: **98 uji, 98 LULUS**.

---

## Sesi 2026-08-20 (Pengingat domain H-30 di pesan harian + bersih-bersih em dash)

Lanjutan pengingat Telegram di hari yang sama. Dua permintaan user: (1) domain jangan cuma diingatkan
tepat di hari kedaluwarsa, mulai H-30 saja; (2) bereskan sisa em dash di kode lama.

KEPUTUSAN user (ditanyakan dulu karena berpengaruh ke jumlah pesan yang masuk tiap pagi):
(1) domain diingatkan di TITIK TERTENTU saja, bukan tiap hari selama sebulan;
(2) jadwal `domains:remind` 08:00 DIMATIKAN supaya tidak ada dua pesan beririsan tiap pagi.

### Pengingat domain H-30
- `DailyDigest::DOMAIN_REMIND_DAYS = [30, 14, 7, 3, 1, 0]`. Domain hanya disebut kalau sisa harinya PERSIS
  salah satu angka itu. Domain dengan sisa 20 hari (bukan titik) atau 31 hari (di luar jangkauan) dilewati.
- Domain yang tanggalnya SUDAH LEWAT sengaja TIDAK ikut. Kalau ikut, domain lama yang tidak diperpanjang
  akan ditagih tiap pagi selamanya.
- Format baris: "nama.com habis hari ini (Rp x)" untuk H-0, dan "nama.com habis 30 hari lagi,
  19 September 2026 (Rp x)" untuk sisanya. Diurutkan dari yang paling dekat.
- Helper baru: `sisaHari()` (selisih hari, dihitung dari awal hari supaya tidak terpengaruh jam) dan
  `formatShort()` (tanggal ringkas Bahasa Indonesia).

### Jadwal domains:remind dimatikan
`routes/console.php` tidak lagi menjadwalkan `domains:remind`; ada komentar yang menjelaskan alasannya
(peringatan H-30 sudah masuk pesan 07:00). COMMAND-NYA TETAP ADA, tinggal `php artisan domains:remind`
kalau sewaktu-waktu mau daftar domain lengkap di luar jadwal.

### Bersih-bersih em dash / en dash di kode lama
22 kemunculan di 6 file dihapus (aturan gaya user: em dash & en dash dilarang di output mana pun).
Diganti sesuai konteks: titik dua, koma, atau tanda hubung biasa.
- `app/Http/Controllers/BudgetController.php` (3): judul komentar + rentang "A-Z".
- `app/Services/BudgetExcelService.php` (6): termasuk 3 teks yang DILIHAT user di file Excel hasil export
  ("Petunjuk: Kolom A (ID) jangan diubah, kosongkan untuk item baru", judul "SEMUA BUDGET - Export: ...").
- `resources/views/budgets/index.blade.php` (6) & `show.blade.php` (3): judul halaman "... - STRACK" dan
  placeholder sel kosong yang tadinya karakter em dash, sekarang tanda hubung biasa.
- `resources/views/dashboard/index.blade.php` (2) & `expenses/create.blade.php` (1): komentar.
HATI-HATI kalau mengulang: garis pemisah komentar `──` itu karakter BOX DRAWING (U+2500), BUKAN dash, dan
dipakai di banyak file termasuk kode baru. Jangan ikut diganti. Penggantian kemarin hanya U+2014 & U+2013,
sudah diverifikasi garis pemisahnya utuh.

### Verifikasi
- Uji lokal SQLite: **30 uji sumber (naik dari 22) + 22 uji digest/command = 52 uji, semua lulus.**
  Tambahan uji domain: H-1/H-3/H-7/H-14/H-30 masing-masing ikut dengan kalimat & tanggal yang benar,
  H-20 dan H-31 dilewati, domain kedaluwarsa dilewati, urutan dari yang terdekat, biaya perpanjangan ikut,
  dan `domains:remind` sudah TIDAK terdaftar di scheduler.
- Lint bersih di semua file yang disentuh, `view:cache` sukses, grep em dash/en dash di
  `app/ resources/ routes/ config/ database/ public/` hasilnya NIHIL.

---

## Sesi 2026-08-20 (Pengingat Telegram: isi kalender hari ini, tiap pagi)

Permintaan user: setiap hari kirim semua catatan/agenda hari ini ke Telegram, KECUALI todo.

KEPUTUSAN user (ditanyakan di awal): (1) cakupan = SEMUA isi kalender hari ini, jadi agenda pribadi +
deadline proyek + domain kedaluwarsa + maintenance + jatuh tempo hutang piutang; todo tetap tidak ikut;
(2) jam kirim 07:00 WIB (`APP_TIMEZONE` server sudah Asia/Jakarta lewat `config/app.php`).

### File baru
- `app/Services/Calendar/DailyDigest.php`: kumpulkan isi kalender untuk SATU tanggal jadi teks polos.
  `forDate()` mengembalikan kelompok yang ADA ISINYA saja; `buildMessage()` merangkai pesan Telegram dan
  mengembalikan NULL kalau hari itu kosong; `emptyMessage()` dipakai kalau pengiriman dipaksa;
  `formatDate()` menulis tanggal Bahasa Indonesia sendiri (nama hari + bulan) karena `APP_LOCALE` masih `en`
  dan mengubah locale global terlalu berisiko buat seluruh aplikasi.
- `app/Console/Commands/CalendarRemind.php`: command `calendar:remind` dengan opsi `--date=` (default hari
  ini), `--user=admin` (kolom user_id pemilik agenda), `--force` (kirim walau kosong), `--dry` (tampilkan
  pesan di layar, tidak mengirim apa pun - dipakai buat uji di hosting).

### Perubahan file lain
- `routes/console.php`: `Schedule::command('calendar:remind')->dailyAt('07:00')`.
- `app/Console/Commands/DomainsRemind.php`: em dash di baris pesan Telegram diganti titik dua (melanggar
  aturan gaya user yang melarang em dash / en dash di output mana pun).

### Bentuk pesan
Judul "🗓️ Agenda Hari Ini" + tanggal Indonesia, lalu kelompok berikon yang ada isinya saja:
📌 Agenda (jam + judul + deskripsi dalam kurung), 📋 Deadline Proyek (klien - judul + sisa tagihan),
🌐 Domain Kedaluwarsa (+ biaya perpanjangan), 🔧 Maintenance (+ label jadwal), 💰 Jatuh Tempo
(hutang/piutang - nama pihak + sisa). HARI YANG KOSONG TIDAK DIKIRIM sama sekali (anti-spam), kecuali
dijalankan manual dengan `--force`.

### Keputusan teknis
- Aturan tanggal tiap sumber DIDUPLIKASI dari `CalendarController` (feed kalender), bukan di-refactor jadi
  satu service bersama. Alasannya: feed kalender baru selesai diuji dan bekerja untuk RENTANG tanggal +
  payload FullCalendar (warna/ikon/url), sedangkan digest cuma butuh SATU tanggal dalam bentuk teks. Ada
  komentar silang di kedua file: kalau aturan tanggal salah satu diubah, ubah keduanya. Yang TIDAK
  diduplikasi: agenda pribadi tetap lewat `CalendarEvent::expandRange(..., TYPE_EVENT)` yang sama.
- Maintenance mengikuti kalender: DATE = tanggal pasti, MONTH = tanggal 1 pada bulan yang dipilih,
  YEAR = 1 Januari. TEXT & ODOMETER tidak punya tanggal, jadi tidak pernah muncul.
- Agenda BERULANG yang jatuh hari itu ikut terkirim (lewat expandRange), agenda user lain tidak.

### CRON DI HOSTING (ini yang bikin pengingat benar-benar jalan)
Daftar cron user sebelumnya cuma dua, dan TIDAK ADA yang untuk strack:
`0 8 1 * * public_html/cron_reminder.php` dan `* * * * * /usr/bin/php /home/u137841455/domains/
horawranghae.com/public_html/artisan schedule:run` (menunjuk domain LAIN).
Artinya `domains:remind` yang dijadwalkan sejak 2026-08-13 SELAMA INI TIDAK PERNAH JALAN.
Yang ditambah di hPanel (satu cron, tiap menit, menghidupkan semua jadwal strack sekaligus):
`* * * * * /usr/bin/php /home/u137841455/domains/strack.my.id/public_html/artisan schedule:run`

SUDAH DIPASANG user 2026-08-20 dan TERVERIFIKASI BENAR-BENAR DIEKSEKUSI, bukan cuma terdaftar. Cara
mengujinya tanpa menunggu jam jadwal, dan tanpa deploy: di hosting tambahkan sementara
`Schedule::call(fn () => file_put_contents(storage_path('logs/cron-alive.txt'), date('c') . PHP_EOL,
FILE_APPEND))->everyMinute();` di `routes/console.php`, `optimize:clear`, tunggu ~150 detik, lalu baca
file penanda; kembalikan dengan `git checkout -- routes/console.php` + `optimize:clear` + `view:cache`.
Hasil uji: penanda tercatat 3 kali berturut-turut (12:47, 12:48, 12:49 WIB), repo hosting kembali bersih.
Jam server sudah WIB, jadi `dailyAt('07:00')` memang jam 7 pagi waktu setempat.

### Verifikasi
- Lokal (SQLite in-memory, MySQL lokal masih mati): **21 uji, 21 lulus**. Cakupan: hari kosong -> null,
  format tanggal Indonesia, agenda berjam (jam + deskripsi ikut), todo sekali jalan & berulang TIDAK ikut,
  agenda berulang yang jatuh hari ini ikut, agenda user lain tidak ikut, tidak bocor ke tanggal lain,
  `--dry` tidak menyentuh Telegram, tanggal ngawur exit 1, hari kosong tidak mengirim, `--force` mengirim
  pesan kosong, pesan berisi terkirim ke chat id, pesan bebas em dash, jadwal terdaftar `0 7 * * *`.
  CATATAN UJI: SQLite menyimpan kolom `date` sebagai datetime penuh sehingga `scopeInRange` (perbandingan
  string) meleset; di skrip uji tanggal dinormalkan ke `Y-m-d` setelah insert. Bukan masalah di MySQL.

---

## Sesi 2026-08-20 (Kalender: todo tidak lagi tampil di kotak tanggal)

Permintaan user: todo (terutama todo RUTIN) jangan dimasukkan ke kotak tanggal kalender karena bikin
tampilan bulanan penuh/spam. Todo cukup di panel sebelah kanan saja. Agenda TIDAK berubah, tetap tampil
di kalender seperti biasa.

KEPUTUSAN user (ditanyakan di awal): (1) SEMUA todo disembunyikan, bukan cuma yang berulang, dan TANPA
saklar tambahan di filter; (2) kalender kecil di Dashboard ikut disamakan supaya perilakunya konsisten.

### Perubahan
- `app/Models/CalendarEvent.php`
  - `expandRange()` dapat parameter keempat `?string $type = null` (EVENT / TODO / null = keduanya),
    diterapkan lewat `when($type !== null, ...)`. Pemanggil lama tanpa parameter tetap dapat keduanya.
  - `getEventsForMonth()` (sumber kalender Dashboard) kini memanggil `expandRange(..., self::TYPE_EVENT)`.
- `app/Http/Controllers/CalendarController.php`
  - `ownEvents()` (feed FullCalendar, sumber `own`) kini `expandRange(..., CalendarEvent::TYPE_EVENT)`.
- `resources/views/calendar/index.blade.php`
  - Label filter sumber "Agenda & Todo" jadi "Agenda" (isinya memang tinggal agenda).
  - Catatan baru `#todoNotice` di modal form, muncul saat tipe Todo dipilih: "Todo tidak ditampilkan di
    kotak tanggal, hanya di panel Todo sebelah kanan." Ditampilkan/disembunyikan di `syncTypeUi()`, yang
    sudah dipanggil saat form dibuka maupun saat radio tipe diganti.

TIDAK ada perubahan skema DB, jadi TIDAK ada file delta di `database/sql/`. Deploy cukup push + pull.

### Yang sengaja TIDAK diubah
- Panel Todo di kanan: query-nya (`CalendarController::todos`) memang terpisah dari feed, jadi tidak
  tersentuh sama sekali. Todo berulang tetap diwakili satu baris lewat `activeOccurrence`.
- Todo tetap bisa DIEDIT dan DIHAPUS: klik badan todo di panel memanggil `openForm(todo)`, form yang sama
  seperti sebelumnya. Jadi tidak ada todo yang jadi tak terjangkau gara-gara hilang dari kalender.
- `calendar.refetchEvents()` sesudah centang todo dibiarkan (mubazir tapi tidak merugikan, dan tetap perlu
  kalau lewat form tipe diubah Todo <-> Agenda).
- Dashboard hanya bisa MEMBUAT tipe EVENT (payload di `noteForm` sudah `type: 'EVENT'`), jadi tidak mungkin
  ada data yang dibuat di dashboard lalu langsung hilang dari pandangan.

### Konsekuensi yang perlu diingat
- Todo SEKALI JALAN yang sudah dicentang selesai kini tidak terlihat di kalender lagi. Jejaknya tinggal di
  daftar "Selesai terakhir" panel Todo (20 terbaru). Data di DB tetap utuh, cuma tidak dirender.
- Kalau suatu saat todo ingin bisa dimunculkan lagi di kalender, jalur termurah: tambah sumber filter baru
  (mis. `own_todo`) di `CalendarController::SOURCES` + satu checkbox di view. Parameter `$type` di
  `expandRange()` sudah menyiapkan jalannya.

### Verifikasi
- MySQL lokal MATI lagi (Laragon), jadi diuji lewat SQLite in-memory (skrip di scratchpad, dua migrasi
  kalender dijalankan langsung): **17 uji, 17 lulus, 0 gagal**. Cakupan:
  feed sumber `own` (agenda sekali jalan & berulang tetap tampil, agenda berulang tetap banyak kemunculan,
  todo sekali jalan & berulang TIDAK tampil); panel todo (kedua jenis todo tetap ada, todo berulang tetap
  SATU baris, agenda tidak nyasar ke panel); `getEventsForMonth` + endpoint `monthEvents` (todo tidak ikut,
  agenda ikut); centang todo berulang tetap 200 dan masuk daftar selesai.
- Lint PHP bersih, `view:cache` sukses, render halaman kalender tetap 95 KB, penanda `todoNotice` ada dan
  label lama "Agenda &amp; Todo" sudah hilang.

---

## Sesi 2026-08-19 (Kalender: agenda & todo BERULANG / terjadwal)

Lanjutan menu Kalender. Agenda dan todo sekarang bisa dijadwalkan berulang: setiap hari, setiap hari kerja
(Sen-Jum), mingguan pilih hari, bulanan pada tanggal tertentu, tahunan, dan kustom tiap N hari/minggu/bulan/
tahun, dengan batas akhir opsional. SUDAH commit `f75efa6` + delta SQL diterapkan + deploy + smoke test
produksi.

KEPUTUSAN user (ditanyakan di awal): (1) opsi pola LENGKAP, bukan ringkas; (2) berlaku untuk Todo DAN Agenda;
(3) centang selesai todo berulang PER TANGGAL, jadi todo harian yang beres hari ini muncul lagi besok.

### Data
- Kolom BARU di `calendar_events` (migrasi `2026_08_19_000002`, delta `database/sql/2026_08_19_calendar_recurrence.sql`,
  batch 9): `repeat_type` (NULL = sekali jalan; DAILY/WEEKDAY/WEEKLY/MONTHLY/YEARLY), `repeat_interval`
  (default 1), `repeat_days` (CSV 0-6, 0 = Minggu, khusus WEEKLY), `repeat_day_of_month` (1-31, atau -1 =
  hari terakhir bulan, khusus MONTHLY), `repeat_until` (batas akhir inklusif, NULL = tanpa batas).
  Semua baris LAMA otomatis `repeat_type` NULL, jadi perilakunya tidak berubah sama sekali.
- Tabel BARU `calendar_event_completions`: event_id (FK cascade), occurrence_date, completed_at, unique
  (event_id, occurrence_date). Menyimpan centang selesai PER TANGGAL untuk rangkaian berulang.
  Kolom `is_done` di calendar_events tetap dipakai, TAPI hanya untuk data sekali jalan (untuk data berulang
  dipaksa false saat simpan).
- Rangkaian berulang disimpan SATU BARIS saja. Tanggal kemunculannya dihitung saat dibaca, tidak
  dimaterialisasi jadi banyak baris.

### File baru
- `app/Models/CalendarEventCompletion.php`: model centang per tanggal.
- `database/migrations/2026_08_19_000002_add_recurrence_to_calendar_events.php`
- `database/sql/2026_08_19_calendar_recurrence.sql` (delta untuk hosting; JANGAN dijalankan dua kali,
  ALTER TABLE akan error "Duplicate column name").

### Logika kemunculan (`CalendarEvent::occurrencesBetween`)
Rentang yang diminta dimundurkan sepanjang durasi acara supaya acara multi-hari yang mulai sebelum rentang
tetap kelihatan ekornya. Dipagari `MAX_OCCURRENCES = 500` biar rentang lebar tidak meledak.
- DAILY: tiap N hari, diselaraskan dari `start_date` (jadi kalau dibaca dari tengah rentang, fase-nya tetap benar).
- WEEKDAY: Senin-Jumat, interval diabaikan (dipaksa 1 saat simpan).
- WEEKLY: minggu dihitung mulai hari MINGGU (sama dengan `firstDay: 0` di FullCalendar); tiap N minggu,
  hari-hari dari `repeat_days`. Kalau tak ada hari dicentang, dipakai hari dari `start_date`.
- MONTHLY: tiap N bulan pakai `addMonthsNoOverflow`. Bulan yang tidak punya tanggalnya DILEWATI, bukan
  digeser (mis. tanggal 31 tidak muncul di Februari/April/Juni). `-1` = hari terakhir bulan.
- YEARLY: tiap N tahun pada tanggal & bulan dari `start_date`. 29 Februari hanya muncul di tahun kabisat.

### Panel todo: satu baris per rangkaian (`CalendarEvent::activeOccurrence`)
Todo berulang TIDAK ditampilkan sebagai puluhan baris. Satu rangkaian diwakili satu baris, aturannya meniru
aplikasi todo umum (Todoist/Google Tasks):
1. Ada kemunculan <= hari ini yang belum dicentang -> ambil yang PALING BARU (jadi todo harian menunjuk
   HARI INI, bukan tanggal basi beberapa hari lalu).
2. Kemunculan yang tanggalnya di bawah centang TERAKHIR dianggap sudah terlewati, tidak ditagih lagi.
   Efeknya: sekali dicentang, baris langsung lompat ke kemunculan berikutnya.
3. Tidak ada yang tertunggak -> ambil kemunculan berikutnya.
Contoh: todo mingguan tiap Selasa, hari ini Rabu, Selasa belum dicentang -> baris menunjuk Selasa dengan
badge "Terlewat". Begitu dicentang -> lompat ke Selasa minggu depan.
Jendela pencarian: `TODO_LOOKBACK_DAYS = 92`, `TODO_LOOKAHEAD_DAYS = 400` (di CalendarController).
Daftar "Selesai terakhir" kini gabungan todo sekali jalan (kolom is_done) + centang per tanggal rangkaian
berulang, diurutkan `completed_at` menurun, ambil 20.

### Perubahan file lain
- `app/Models/CalendarEvent.php`: konstanta REPEAT_*, DAY_NAMES; accessor is_recurring / duration_days /
  repeat_every / repeat_day_numbers / repeat_label (kalimat Indonesia, mis. "Setiap 2 minggu pada Selasa,
  Kamis, sampai 31 Des 2026"); `occurrencesBetween`, `activeOccurrence`, `lastCompletedOccurrence`,
  `isDoneOn`, `expandRange`, `loadCompletionsFor`, `formProps`. `scopeInRange` dipecah dua cabang (sekali
  jalan vs berulang; untuk yang berulang batas atas tidak dipakai karena rangkaian bisa mulai jauh di masa
  lalu). `toCalendarPayload()` & `toDashboardArray()` sekarang menerima tanggal kemunculan.
- `app/Http/Controllers/CalendarController.php`: `ownEvents` pakai `expandRange`; `todos` + helper
  `recentlyDoneTodos` & `todoRow`; `toggleDone` menerima param `date` untuk data berulang (422 kalau tanggal
  tidak valid); `move` MENOLAK rangkaian berulang (422); `validatePayload` + `normalizeRepeat` +
  `repeatRuleChanged`; `destroy` ikut menghapus completions.
- `resources/views/calendar/index.blade.php`: blok "Ulangi" di form (preset + interval kustom + tombol hari
  + tanggal bulanan + batas akhir + kalimat ringkasan langsung), CSS `.repeat-box`/`.day-toggle`, modul JS
  pengulangan, panel todo dikunci per KEMUNCULAN (`data-key` = id|tanggal, bukan id saja - satu rangkaian
  bisa nongol di daftar pending dan selesai sekaligus), centang mengirim `date`.
- `resources/views/dashboard/index.blade.php`: ikon berulang di daftar catatan harian; konfirmasi hapus
  memperingatkan kalau yang dihapus SELURUH rangkaian.

### Batasan yang disengaja (bukan bug)
- Kemunculan berulang TIDAK bisa digeser drag & drop (`editable: false` di payload, `move` juga menolak).
  Yang harus berubah aturannya, lewat form. Ini menghindari kerumitan "ubah acara ini saja vs semua".
- Edit rangkaian berlaku untuk SELURUH kemunculan (ada catatan di form). Belum ada pengecualian per tanggal.
- Kalau aturan pengulangan atau tanggal mulai DIUBAH, riwayat centang lama dihapus (sudah tidak cocok lagi
  dengan kemunculan yang baru). Ubah judul/warna/catatan saja TIDAK menghapus riwayat.
- Hapus rangkaian = hapus semua kemunculan + riwayat centangnya.

### Verifikasi
- MySQL lokal MATI lagi, jadi diuji lewat SQLite sementara (skrip di scratchpad, skema minimal):
  **81 uji, 81 lulus, 0 gagal**. Cakupan: perhitungan 7 pola + interval kustom + tanggal 31/hari terakhir/
  29 Feb/batas akhir/multi-hari; validasi & normalisasi controller (pola ngawur ditolak, WEEKDAY interval
  dipaksa 1, is_done dipaksa false, repeat_until sebelum start ditolak, hari/tanggal kosong diisi dari
  tanggal mulai); feed (id unik per tanggal, editable false untuk berulang); centang per tanggal (hari ini
  selesai tapi besok belum, batal centang, tanggal ngawur 422); aturan kemunculan aktif (4 skenario);
  ubah aturan membersihkan centang, ubah judul TIDAK; hapus ikut membuang completions; kepemilikan
  (centang milik user lain 404); dashboard membentangkan berulang; pagar MAX_OCCURRENCES.
- Lint PHP bersih, `view:cache` sukses, `route:list --path=calendar` tetap 9 route.
- Render halaman kalender: 95 KB HTML (sebelumnya 69 KB), semua penanda form pengulangan ada.
- Uji visual di browser: PENDING user.

### Urutan penerapan (PENTING) - SUDAH DIJALANKAN
1. Terapkan `database/sql/2026_08_19_calendar_recurrence.sql` di hosting DULU (ALTER TABLE + tabel baru +
   catat migrasi batch 9). Jalankan SEKALI saja.
2. BARU deploy kode. Kalau dibalik, halaman kalender & dashboard error karena kolom `repeat_type` belum ada.

Cara menerapkan delta lewat SSH (pola yang berhasil, menghindari masalah kutip-ganda yang dihapus transport):
`scp file.sql saktify:~/` lalu jalankan skrip sh kecil yang membaca DB_DATABASE/DB_USERNAME/DB_PASSWORD dari
`.env`, `export MYSQL_PWD`, dan `mysql -u"$US" "$DB" < file.sql`. Jauh lebih tenang daripada `mysql -e "..."`.

### Hasil di produksi (2026-08-19)
- Sebelum delta: `calendar_events` ada 6 baris, kolom repeat BELUM ada, batch terakhir 8. Sesuai dugaan.
- Sesudah delta: 5 kolom repeat_* terbentuk (repeat_type varchar(20) NULL, repeat_interval smallint unsigned
  default 1, repeat_days varchar(20), repeat_day_of_month smallint, repeat_until date), tabel
  `calendar_event_completions` terbentuk, migrasi tercatat batch 9, 6 agenda lama UTUH dan semuanya
  `repeat_type` NULL (perilaku tidak berubah).
- Deploy: push `cd93cd5..f75efa6`, hosting `git pull --ff-only` + `optimize:clear` + `view:cache` sukses.
- SMOKE TEST (login lewat curl): `/calendar` 200 (95 KB), `/calendar/todos` 200, `/dashboard-admin` 200,
  data lama terbaca `isRecurring: false`.
- UJI TULIS di produksi lalu dibersihkan:
  (a) Todo "setiap hari kerja" 1-11 Sep -> feed mengembalikan TEPAT 9 kemunculan (1-4, 7-11 Sep), Sabtu 5 dan
      Minggu 6 dilewati, berhenti di 11 Sep sesuai `repeat_until`.
  (b) Agenda "mingguan Selasa+Kamis tiap 2 minggu" -> 1, 3, 15, 17, 29 Sep (minggu 6-12 dan 20-26 dilompati).
  (c) Panel todo menunjuk 1 Sep; setelah dicentang, pindah ke 2 Sep dan 1 Sep masuk daftar selesai.
  (d) Centang tanpa tanggal -> 422. Geser rangkaian berulang -> 422 dengan pesan yang benar.
  (e) Kedua data uji dihapus; feed September kosong lagi, DB kembali 6 agenda + 0 completions.

### Pending / lanjutan
- Uji visual: buat todo "setiap hari kerja", centang, pastikan besok muncul lagi; coba mingguan multi-hari,
  bulanan tanggal 25, kustom tiap 2 minggu; cek tampilan mobile.
- Opsi lanjutan: pengecualian per tanggal (lewati/geser satu kemunculan tanpa mengubah rangkaian),
  pengingat Telegram untuk todo berulang (pola `domains:remind` sudah ada), tampilan riwayat kepatuhan
  (berapa kali dikerjakan dari sekian kemunculan) - datanya sudah tersimpan di calendar_event_completions.

### Domain & Hosting: provider diseragamkan jadi Hostinger (sama hari)
Permintaan user: semua domain pakai satu hosting, jadi kolom `provider` diisi "Hostinger" semua.
- Kondisi awal di produksi: 50 domain, 32 provider NULL, 18 sudah "Hostinger". TIDAK ADA nilai provider lain,
  jadi tidak ada data yang tertimpa. Dicek dulu sebelum menulis.
- Dijalankan lewat SSH: `UPDATE domains SET provider = 'Hostinger', updated_at = NOW()
  WHERE provider IS NULL OR provider = ''` -> 32 baris diperbarui. Query sengaja dibuat IDEMPOTEN (yang sudah
  terisi tidak disentuh), jadi aman kalau diulang.
- Verifikasi: 50/50 provider = Hostinger, 0 kosong. Halaman `/domains` 200 dan kata "Hostinger" muncul 100x
  (50 domain x 2 tampilan: tabel desktop + card mobile).
- Ini perubahan DATA, bukan skema, jadi TIDAK dibuatkan file delta di `database/sql/` (kalau DB lokal diimpor
  ulang dari dump produksi, nilainya sudah ikut terbawa).
- TEMUAN SAMPINGAN: 1 domain belum punya `expires_at` yaitu `starvvoindonesia.com` (id 52, kemungkinan
  ditambahkan setelah pengisian massal 2026-08-13). Selama kosong, domain ini TIDAK akan kena reminder
  `domains:remind`. Perlu diisi manual oleh user.
- CATATAN untuk nanti: `DomainController::sync()` (impor dari folder ~/domains) masih menyimpan provider NULL
  untuk domain baru. Kalau mau, provider bisa di-default "Hostinger" di sync/form biar tidak kosong lagi.

---

## Sesi 2026-08-19 (Menu Kalender: agenda + todo, gaya Google Calendar)

Menu baru "Kalender" (`/calendar`) sebagai halaman penuh: agenda berjam, todo, dan seluruh tanggal penting
strack dalam satu tampilan. Kalender dashboard TETAP ada, kini bersumber data yang sama + ditautkan ke menu ini.
KEPUTUSAN user: tabel baru `calendar_events`; tampilan pakai FullCalendar 6 (CDN); dashboard tetap ada + tombol
buka; sumber data tambahan = deadline proyek, domain, maintenance, hutang piutang (keempatnya dipilih).

### Data
- Tabel BARU `calendar_events` (migrasi `2026_08_19_000001`, delta `database/sql/2026_08_19_calendar_events.sql`):
  user_id, title, description, type(ENUM EVENT/TODO), start_date, end_date, start_time, end_time, all_day,
  color, is_done, completed_at, timestamps. Index di user_id & start_date.
- Tabel LAMA `calendar_notes` (id/user_id/date/title/content, 5 baris di produksi) TIDAK di-drop, isinya
  DIPINDAH lewat `INSERT ... SELECT` di migrasi + delta SQL. Batasan lama "1 catatan per hari" DIBUANG.
- Model `CalendarNote` + `CalendarNoteController` DIHAPUS, digantikan `CalendarEvent` + `CalendarController`.

### File baru
- `app/Models/CalendarEvent.php`: scope forUser/inRange/todo, accessor is_todo/display_color/effective_end_date/
  time_label, `toCalendarPayload()` (bentuk event FullCalendar), `toDashboardArray()`, `getEventsForMonth()`.
  PENTING: untuk acara seharian FullCalendar memakai `end` EKSKLUSIF, jadi tanggal akhir +1 hari di payload.
- `app/Http/Controllers/CalendarController.php`: index, feed (semua sumber, param start/end/sources), todos
  (panel samping), monthEvents (dipakai dashboard), store/update/destroy/toggleDone/move. Kepemilikan dicek
  lewat `forUser(session('role'))` di tiap operasi. Helper `normalizeTime()` menyamakan jam ke H:i:s.
- `resources/views/calendar/index.blade.php`: FullCalendar 6 (CDN `index.global.min.js` + `locales-all`),
  locale id, view Bulan/Minggu/Hari/Agenda (mobile: Bulan + Agenda, default listWeek). Drag & drop geser
  jadwal, resize durasi, klik-seret area kosong = buat agenda. Panel samping: filter sumber data (disimpan di
  localStorage) + daftar todo (centang selesai, badge Terlewat/Hari ini, tambah cepat, daftar selesai).
  Modal form (Agenda/Todo, seharian atau berjam, warna) + modal detail untuk data modul lain.
  CATATAN: FullCalendar 6 build global MENYUNTIKKAN CSS-nya sendiri, tidak ada file CSS terpisah di CDN.

### Sumber data yang ikut tampil (read-only, klik -> modal detail + tombol buka)
- Deadline proyek: status WAITING/PROGRESS. Warna merah bila terlewat, kuning <=7 hari, biru selebihnya.
- Domain: `expires_at`. Merah bila lewat, kuning <=30 hari (`Domain::EXPIRING_DAYS`).
- Maintenance: tipe DATE (tanggal pasti), MONTH (berulang, ditaruh tanggal 1 tiap bulan yang cocok),
  YEAR (1 Januari). Tipe TEXT & ODOMETER DILEWATI karena tak punya tanggal.
- Hutang piutang: `due_date` dari `debt_records` yang status != PAID.

### Perubahan file lain
- `routes/web.php`: 4 route `calendar-notes.*` DIGANTI grup `calendar.*` (9 route: index, feed, todos,
  events.month, events.store/update/destroy/toggle-done/move).
- `resources/views/layouts/app.blade.php`: menu sidebar "Kalender" (bi-calendar-week) di bawah Proyek.
- `app/Http/Controllers/DashboardController.php`: `$calendarNotes` kini dari `CalendarEvent::getEventsForMonth`.
- `resources/views/dashboard/index.blade.php`: 3 URL fetch dialihkan ke `/calendar/events*`, tombol
  "Buka Kalender" di header kartu, tombol "Buka di Kalender" di modal harian (deep link `?date=YYYY-MM-DD`
  -> FullCalendar langsung membuka tanggal itu).

### Verifikasi
- Commit `63bcb9d` (+ `563c585` beres-beres). Delta SQL diterapkan ke hosting SEBELUM push, lalu deploy.
  Verifikasi hosting: tabel `calendar_events` terbentuk, 5 catatan lama pindah utuh, migrasi tercatat batch 8.
- Smoke test PRODUKSI (login lewat curl): `/calendar` 200 (69 KB), `/calendar/feed` Agu-Sep 2026 = 7 event
  (2 deadline proyek, 4 domain, 1 maintenance), `/calendar/todos` + `/calendar/events/month/...` 200,
  catatan lama muncul di bulan aslinya (Okt 2025 & Jun 2026) lengkap dengan deskripsinya, dashboard 200 dan
  tombol "Buka Kalender" ada. Uji tulis: buat agenda berjam -> muncul di feed -> dihapus lagi (bersih).
- Lint PHP bersih; `view:cache` sukses; `route:list --path=calendar` = 9 route (lokal & hosting).
- MySQL lokal MATI, jadi diuji lewat SQLite sementara (skrip scratchpad, skema minimal + data contoh):
  feed 10 event dari 5 sumber BENAR; proyek CANCELLED & hutang PAID dikecualikan; maintenance MONTH muncul
  2x (Agu & Sep), ODOMETER dilewati; multi-hari `end` +1 hari; filter `sources=own,projects` -> 5 event;
  todo terlewat terdeteksi; store/move/toggle/destroy OK; hapus milik user lain ditolak "Data tidak ditemukan".
- View dirender langsung (69 KB HTML) - semua penanda penting ada.

### Penyesuaian tampilan (sama hari, `1e4829e`)
Permintaan user setelah melihat hasil pertama: kotak tanggal terlalu kecil untuk halaman khusus kalender.
- TINGGI: `height: 'auto'` DIGANTI hasil hitung `window.innerHeight - offsetTop kalender - 28`, minimal 560px,
  jadi grid bulanan mengisi satu layar penuh. Dihitung ulang di event `load` (posisi bergeser setelah font/CSS
  CDN selesai dimuat, kalau tidak tingginya meleset) dan saat `resize` (debounce 150ms). Di bawah 992px
  tetap `auto` karena mode Agenda lebih enak digulir.
- `dayMaxEvents: 3` -> `true` (menyesuaikan tinggi sel sendiri). Padding card-body dikurangi ke p-2/p-md-3,
  header row mb-4 -> mb-3.
- TIPOGRAFI dinaikkan agar sebanding: nomor tanggal .82->.95rem, lingkaran hari ini 26->32px, nama hari
  .78->.88rem, judul bulan 1.15->1.35rem, label acara .74->.82rem, slot jam timeGrid 2.4em.
- Panel samping `.calendar-side` jadi sticky (top 1rem) di lg+, `.todo-list` max-height clamp(180px,34vh,460px).
- SIDEBAR: menu Kalender dipindah dari bawah Proyek ke ATAS Sierra Berak (di bawah Domain & Hosting).

### Urutan penerapan (PENTING)
1. Terapkan `database/sql/2026_08_19_calendar_events.sql` di hosting DULU (buat tabel + pindah data + catat
   migrasi batch 8). INSERT ... SELECT jalankan SEKALI saja, kalau diulang data jadi dobel.
2. BARU deploy kode. Kalau dibalik, dashboard error karena `calendar_events` belum ada.

### Pending / lanjutan
- User uji visual di browser: buat agenda berjam, todo, drag & drop, filter sumber, tampilan mobile.
- Opsi lanjutan bila diinginkan: acara berulang (mingguan/bulanan), pengingat Telegram untuk agenda/todo
  (infrastruktur sudah ada: TelegramService + pola command `domains:remind`), undangan/berbagi ke klien.

---

## Sesi 2026-08-13 (Modul Domain & Hosting + reminder Telegram)

Menu baru "Domain & Hosting": pendataan domain + pengingat perpanjangan. Commit `9134fe9`, deploy + tabel
dibuat + teruji. KEPUTUSAN user: tautkan ke Klien DAN Project (opsional); reminder dikirim ke Telegram.
- Tabel `domains` (delta `database/sql/2026_08_13_domains.sql` + migrasi `2026_08_13_000001`, dicatat batch 7):
  name(unique), client_id/project_id (FK nullOnDelete), provider, registered_at, expires_at, renewal_cost,
  is_hosted, notes. Model `Domain`: relasi client/project, accessor status (EXPIRED/EXPIRING_SOON<=30h/ACTIVE/
  UNKNOWN) + days_until_expiry + status_color/label, scope search/expiringWithin.
- `DomainController`: resource (kecuali show) + `sync`. Index: ringkasan (total/di hosting/akan habis/kedaluwarsa)
  + filter status+search + tabel/card, badge sisa hari. Form (create+edit share `domains/form.blade.php`).
- SYNC dari hosting: `sync()` baca folder `~/domains` (=`dirname(base_path(),2)` = /home/u137841455/domains)
  via `scandir` (BUKAN exec - exec DINONAKTIFKAN di hosting; open_basedir KOSONG jadi PHP boleh baca).
  Impor nama domain yang belum ada (is_hosted=true). Config `services.hosting.domains_path` (env override).
  TERUJI: impor 50 domain.
- REMINDER: command `domains:remind {--days=30}` cari domain expiringWithin -> kirim daftar ke
  `TELEGRAM_ALLOWED_CHAT_IDS` via TelegramService. Dijadwalkan `Schedule::command('domains:remind')->dailyAt('08:00')`
  di routes/console.php. TERUJI: set 1 domain +10 hari -> "Pengingat terkirim (1 domain) ke 1 chat" (pesan nyata
  masuk Telegram), lalu di-revert.
- FAKTA HOSTING (penting): whois TIDAK ADA + exec/shell_exec/system/passthru/popen DINONAKTIFKAN -> tgl
  kedaluwarsa TAK bisa auto (manual). open_basedir KOSONG -> PHP boleh baca ~/domains. crontab CLI TIDAK ADA
  (cron lewat hPanel). php di /usr/bin/php.
- PENDING: user set CRON di hPanel (Advanced -> Cron Jobs) untuk reminder harian:
  `/usr/bin/php ~/domains/strack.my.id/public_html/artisan domains:remind` (jadwalkan sekali sehari, mis. 08:00).

### Lanjutan Domain (sama hari)
- FORM DOMAIN `533c464`: select Klien jadi SEARCHABLE pakai Select2 (CDN; jQuery 3.7.1 sudah dimuat layout),
  opsi tampilkan "Nama - nomor telepon". Select Project DIHAPUS dari form (kolom `project_id` di DB tetap ada),
  diganti tombol "Show Project" yang mengarah ke `clients.show` (halaman klien berisi daftar project-nya) di
  tab baru, aktif hanya bila klien dipilih (href di-update via JS). CSS Select2 disisipkan lewat @push('scripts')
  (layout hanya punya @stack('scripts'), tak ada stack styles). Controller create/edit tak lagi query $projects.
- DATA EXPIRY: user kasih daftar 66 domain + tanggal berakhir dari Hostinger. Di-update via tinker: cocokkan
  by name, UPDATE expires_at utk yang ADA di strack. Hasil: 50 diperbarui (semua domain strack kini punya
  tanggal), 16 DIABAIKAN (ada di daftar tapi tak ada di strack -> per instruksi user = domain tak diperpanjang).
  "sanggartari juju.my.id" (ada spasi) dinormalkan ke `sanggartarijuju.my.id`. Terdekat kedaluwarsa:
  langensari06.site 01 Sep 2026, sdnkebonpala12pagi 02 Sep, politikus.id 03 Sep, saktify.my.id 12 Sep.

### Kolom Nilai Proyek di /projects (sama hari, `404e172`)
Tambah info nilai proyek (`total_value`) di daftar `/projects`: kolom "Nilai" baru (sortable, antara Deadline &
Status) di tabel desktop + baris nilai di card mobile. `ProjectController::index` & `search`: tambah case sort
`total_value`. File: `ProjectController`, `projects/index.blade.php`.

---

## Sesi 2026-08-13 (Print Invoice: opsi Quotation/DP/Progress/Pelunasan)

Penyesuaian strack (bukan bot). Gabung tombol "Print Quotation" + "Print Invoice" jadi SATU dropdown
"Print Invoice" berisi: Quotation, Down Payment, Progress, Pelunasan. Commit `8cb02cd`, deploy + smoke test.
- KEPUTUSAN user: jumlah tagihan DIISI MANUAL (default nilai proyek, editable di preview); tampilan DROPDOWN.
- `show.blade.php`: 2 tombol -> 1 dropdown Bootstrap. Quotation -> alur lama (`preview-quotation`). DP/Progress/
  Pelunasan -> `preview-invoice?type=dp|progress|pelunasan`. PENTING: query param via
  `route('projects.preview-invoice', ['project'=>$project,'type'=>'dp'])` (arg ke-3 route() itu $absolute, bukan query).
- `ProjectInvoiceController`: `previewInvoice(Request)` + `printInvoice` terima `type` + `billed_amount` (manual,
  helper `parseAmount` buang titik, fallback total_value). itemData total = billed. Kirim `$stageLabel`
  (dp=Down Payment/progress=Progress/pelunasan=Pelunasan) + `$paymentInfo` (total_value, paid_amount, billed,
  remaining_after=max(0,total-paid-billed)). Const `STAGE_LABELS`.
- `invoice-preview.blade.php`: judul + label tahap, hidden `type`, field "Jumlah Ditagih" (name=billed_amount,
  default number_format(total), editable), unit_price & total_display dihitung JS dari billed/qty (fungsi
  recalc/parseBilled, format blur), validasi billed>0.
- Template cetak `invoice-general` (teal) & `invoice` BTOOLS (ocean blue): label tahap di header (STRTOUPPER),
  + box "Rincian Pembayaran Proyek" (Nilai Proyek, Sudah Dibayar, Ditagih invoice ini, Sisa) - hanya bila
  $stageLabel ada. total pakai billed. Quotation views TIDAK diubah.
- LANJUTAN (`2a6e971`): tambah opsi **Invoice Penuh** (type=full, tagih total, tanpa label tahap) di dropdown.
  Rincian Pembayaran Proyek kini tampil di SEMUA invoice (`@isset($paymentInfo)`, bukan hanya bertahap) dan
  memuat **DP** (`dp_amount`) + **Pelunasan** (`total-dp`) + Sudah Dibayar + Sisa; baris "Ditagih invoice ini"
  hanya bila ada $stageLabel. Dropdown final: Quotation, Invoice Penuh, Down Payment, Progress, Pelunasan.
- Teruji: view:cache OK; render via tinker OK (full -> DP/Pelunasan muncul tanpa baris "Ditagih"; DP -> ada
  baris "Ditagih (Down Payment)"); route hasilkan `?type=dp`.
- PENDING: user uji visual di browser (buka proyek -> Print Invoice -> tiap opsi -> cetak).

---

## Sesi 2026-08-13 (bangun + deploy bot Telegram tanya-data, Text-to-SQL read-only - FASE 1)

### Ringkasan
Rancangan bot Telegram dari sesi 2026-08-12 DIEKSEKUSI. Bot read-only (tanya data strack lewat
Telegram, dijawab AI Bahasa Indonesia) sudah dibangun, di-commit (`b379119`), deploy ke hosting,
webhook terdaftar, dan TERUJI end-to-end. Bot: **t.me/Saktify_strack_bot**. User sudah topup credit
Anthropic $5 + kasih API key. Fitur TULIS (INSERT/UPDATE) ditunda jadi FASE 2 (lihat bawah).

### Arsitektur (Fase 1, read-only)
Alur: Telegram -> `POST /telegram/webhook` -> `TelegramWebhookController` -> `TextToSqlService`:
(1) AI (Haiku) ubah pertanyaan ID jadi 1 query SELECT (skema DB dikirim sebagai konteks),
(2) `SqlGuardrail` validasi, (3) jalankan di koneksi `mysql_ro`, (4) AI rangkai jawaban natural ID.
Implementasi Anthropic pakai `Http` facade (tanpa SDK), prompt caching pada system prompt (skema).

### File dibuat
- `app/Services/Ai/AnthropicClient.php` - panggil `api.anthropic.com/v1/messages`, cache_control ephemeral.
- `app/Services/Ai/SchemaInspector.php` - baca `information_schema.COLUMNS` -> teks skema, cache 1 jam,
  buang tabel sistem Laravel. Pakai koneksi `mysql_ro`.
- `app/Services/Ai/SqlGuardrail.php` - hanya SELECT/WITH tunggal; blokir kata kunci tulis
  (INSERT/UPDATE/DELETE/DROP/ALTER/dll), tolak `;` ganda + komentar (`--`/`#`/`/*`), paksa LIMIT 200.
- `app/Services/Ai/TextToSqlService.php` - orkestrasi 2 panggilan AI (buat SQL, lalu rangkai jawaban),
  timeout query `SET SESSION max_execution_time=8000`. Balas `TIDAK_BISA` bila di luar skema.
- `app/Services/Telegram/TelegramService.php` - `sendMessage` (pecah per 4000 char) + `sendChatAction`.
- `app/Http/Controllers/TelegramWebhookController.php` - verifikasi secret header, whitelist chat_id,
  perintah `/start`/`/help`, selalu balas 200 (Telegram tak retry).
- `app/Console/Commands/TelegramSetWebhook.php` - `php artisan telegram:set-webhook <url>|--delete|--info`.

### File diedit
- `config/services.php`: blok `anthropic` (api_key, model Haiku default, fallback Sonnet, base_url,
  version) + `telegram` (bot_token, webhook_secret, allowed_chat_ids parse CSV).
- `config/database.php`: koneksi `mysql_ro`. PENTING: password fallback = `env('DB_RO_PASSWORD',
  env('DB_PASSWORD',''))` supaya kalau DB_RO kosong benar-benar jatuh ke DB utama.
- `bootstrap/app.php`: `validateCsrfTokens(except: ['telegram/webhook'])`.
- `.env.example`: placeholder ANTHROPIC_*/TELEGRAM_*/DB_RO_*.
- `routes/web.php`: `POST /telegram/webhook` (di luar grup simpleauth).

### Keamanan (berlapis)
1. Secret webhook (`X-Telegram-Bot-Api-Secret-Token`) -> tanpa/ salah = 403.
2. Whitelist chat_id (hanya `8588404484` = akun user) -> chat lain ditolak, tak ada query.
3. Guardrail SQL SELECT-only (aplikasi).
4. Koneksi read-only + LIMIT + timeout.
Semua 4 sudah DIUJI di produksi (lihat verifikasi).

### KENDALA HOSTING PENTING: user MySQL read-only TIDAK BISA dibuat
- Rencana awal: buat user MySQL read-only (GRANT SELECT saja) via SSH. GAGAL: user DB hosting
  (`u137841455_7BIdx`) cuma punya `USAGE` global + `ALL PRIVILEGES` di DB sendiri, TIDAK punya
  `CREATE USER`. Di shared hosting Hostinger pembuatan user dikunci ke hPanel, dan hPanel memberi
  ALL PRIVILEGES saat menautkan user (tak ada opsi SELECT-only granular).
- KEPUTUSAN user: koneksi `mysql_ro` FALLBACK ke kredensial DB utama; keamanan tulis diandalkan pada
  guardrail aplikasi (SELECT-only) + whitelist + secret. Praktis: satu-satunya query yang lolos ke DB
  hanyalah SELECT dari user sendiri. DB_RO_USERNAME/DB_RO_PASSWORD SENGAJA tidak ditulis di .env hosting
  (biar env() null -> fallback; kalau ditulis kosong, Laravel anggap string kosong -> koneksi gagal).

### Kredensial (di .env HOSTING saja, TIDAK di repo)
- `ANTHROPIC_API_KEY` (dari console user), `ANTHROPIC_MODEL=claude-haiku-4-5`, fallback Sonnet.
- `TELEGRAM_BOT_TOKEN` (BotFather, bot @Saktify_strack_bot), `TELEGRAM_WEBHOOK_SECRET` (hex acak),
  `TELEGRAM_ALLOWED_CHAT_IDS=8588404484`.
- Backup .env hosting dibuat sebelum diedit: `.env.bak.telegram.<timestamp>`.
- CATATAN KEAMANAN: API key + bot token sempat tampil di chat sesi ini -> SEBAIKNYA user regenerate
  keduanya nanti (console + BotFather) lalu update .env hosting.

### Deploy & verifikasi
- Commit `b379119` (12 file, partial commit - tak menyertakan perubahan lain yang sudah ter-stage).
  Deploy via `scripts/deploy.ps1` (push + pull + optimize:clear + view:cache). HEAD hosting = b379119.
- Webhook didaftarkan: `php artisan telegram:set-webhook https://strack.my.id/telegram/webhook` -> ok.
- UJI end-to-end (tinker di hosting): "ada berapa total proyek?" -> "Ada 215 total proyek." BENAR.
- UJI kirim Telegram (simulasi webhook + secret + chat_id user): balas 200 `ok`, pesan nyata terkirim.
- UJI keamanan: tanpa secret -> 403; chat_id asing -> ditolak (tak query). Guardrail: UPDATE/DELETE/
  multi-statement DROP/komentar semua DITOLAK; SELECT lolos + auto LIMIT.

### Catatan teknis
- Nested heredoc `ssh saktify 'bash -s' <<'OUTER'` (+ heredoc dalam untuk `mysql`/`php artisan tinker
  <<'PHP'`) JALAN via tool Bash (beda dgn transport PowerShell yg bermasalah). tinker REPL evaluasi
  baris-demi-baris: hindari try/foreach multi-baris, pakai closure/statement satu baris.

### FASE 2 (SELESAI, sama hari) - fitur TULIS (insert/update) via tool use + konfirmasi
Commit `9c7811f`, deploy + teruji. KEPUTUSAN arsitektur (disepakati): tulis TIDAK via Text-to-SQL mentah
(bahaya: UPDATE tanpa WHERE, lewati aturan bisnis). Pola = AKSI TERDEFINISI (tool use Anthropic): tiap
pesan diklasifikasi AI -> BACA (tool `tanya_data` -> TextToSqlService) atau TULIS (panggil 1 aksi + ekstrak
data). Aksi tulis lewat model/controller yang ada (validasi saldo/sisa + sinkronisasi tetap jalan) dan
SELALU minta KONFIRMASI dulu (aksi tertunda disimpan di cache per chat_id, TTL 5 menit; dieksekusi setelah
user balas "ya"; "tidak" batal; pesan lain = permintaan baru).
- 6 AKSI (`app/Services/Ai/Actions/`): `CatatPengeluaranAction` (Expense, cek saldo Bank/Cash),
  `CatatPendapatanAction` (Payment, resolusi proyek via `search()`, tolak > sisa tagihan),
  `CatatBayarHutangAction` (DebtPayment, resolusi DebtRecord, tolak > sisa), `UpdateStatusProyekAction`
  (Project.status enum), `CatatTransferBankAction` (BankTransfer utk semua pembayaran proyek yg belum
  ditransfer + BankBalance::updateBalance), `CatatSierraBerakAction` (SierraBerak). Base `WriteAction`
  (helper parseAmount/rp/parseDate) + trait `ResolvesProject` (cari 1 proyek, pesan bila ambigu/none) +
  `ActionRegistry`. Orkestrasi: `BotOrchestrator`. `AnthropicClient` ditambah `raw()` + `extractToolUse()`
  + `extractText()` untuk tool use (chat() lama tetap dipakai TextToSqlService). Controller webhook kini
  panggil `BotOrchestrator::handle()` (bukan TextToSqlService langsung).
- UJI di hosting (tinker, chat_id dummy): (baca) "ada berapa total proyek" -> 215 OK; (pengeluaran) "kopi
  15rb dari cash" -> DITOLAK "Saldo Cash tidak cukup (Rp6.800)" (validasi jalan, tak menulis); (Sierra
  Berak) preview -> "ya" -> tersimpan (count 230->231) -> row uji dihapus (cleanup ke 230); routing 4 aksi
  lain benar (pendapatan resolve "Website Starvvo/PT Global Mitra Proteksindo", status Penawaran->Selesai,
  hutang "Budi tidak ditemukan", transfer "tak ada yg belum ditransfer"). Tak ada data uji tertinggal.
- CATATAN: aksi tulis pakai koneksi DB DEFAULT (mysql, read-write) via Eloquent; baca tetap `mysql_ro`.
  Cache konfirmasi pakai CACHE_STORE=database (sudah ada). Untuk MENAMBAH aksi baru: buat kelas turunan
  `WriteAction` + daftarkan di `ActionRegistry`.

### FASE 3 (SELESAI, sama hari) - dukungan VOICE NOTE (transkripsi Groq Whisper)
Commit `ff53050`, deploy + teruji. User tanya bisa VN atau tidak; dijelaskan Anthropic API tak menerima
audio (hanya teks/gambar/PDF) -> butuh speech-to-text terpisah. User tanya opsi gratis -> pilih GROQ
(tier gratis, Whisper large v3, endpoint kompatibel OpenAI). User kasih GROQ_API_KEY.
- Alur: `message.voice`/`message.audio` -> `TelegramService::downloadFile()` (getFile + unduh biner dari
  `api.telegram.org/file/bot<token>/<path>`) -> `TranscriptionService::transcribe()` (POST multipart ke
  `api.groq.com/openai/v1/audio/transcriptions`, model `whisper-large-v3`, language=id, via Http::attach)
  -> teks -> `BotOrchestrator::handle()` (pipeline sama: baca/tulis + konfirmasi). Balasan VN diberi prefix
  transkripsi (emoji mikrofon + teks) agar user tahu yang didengar - penting utk verifikasi sebelum konfirmasi tulis.
- File: `config/services.php` (blok `groq`: api_key, stt_model, base_url), `.env.example` (+GROQ_*),
  `TranscriptionService`, `TelegramService::downloadFile`, `TelegramWebhookController` (cabang voice: transkrip
  dulu setelah whitelist, sebelum /start & proses; gagal transkrip/senyap dibalas ramah).
- KREDENSIAL di .env hosting saja: GROQ_API_KEY (+GROQ_STT_MODEL=whisper-large-v3). Backup `.env.bak.groq.*`.
  Uji: key valid (models list, whisper-large-v3 & -turbo ada); endpoint transcriptions 200 (WAV senyap ->
  Whisper halusinasi "Terima kasih", wajar) baik lokal maupun via TranscriptionService di hosting. Uji VN
  ASLI berisi ucapan menunggu user (kirim VN ke bot). Biaya Groq: tier gratis (rate limit) - VN Rp0.
- Catatan model tersedia di Groq: whisper-large-v3 (dipakai, akurat) & whisper-large-v3-turbo (lebih cepat).
  Ganti lewat GROQ_STT_MODEL.

### PERBAIKAN UX bot (sama hari, dari feedback VN pertama user)
User uji VN asli: (1) "berapa total belum ditransfer" -> Rp220.000 (BENAR), lalu (2) "transfer senilai
total tersebut dengan catatan MB..." -> bot MENOLAK/terus menanya proyek+nominal. Tiga akar + fix:
- `catat_transfer_bank` dulu WAJIB proyek -> bot terus tanya. FIX `e60f9ff`: proyek OPSIONAL. Kosong =
  transfer SEMUA pembayaran belum ditransfer (pola batch, `Payment::where is_transferred=false`), diisi =
  khusus proyek. + instruksi AI jangan tanya field opsional (tanggal/referensi).
- AI kira transfer butuh NOMINAL. FIX `c7a01bc`: tegaskan di deskripsi tool bahwa transfer SELALU
  memindahkan seluruh pembayaran belum ditransfer, TIDAK butuh nominal, jangan tanya nominal.
- Bot STATELESS -> "total tersebut" tak terpahami. FIX `805d808`: INGATAN PERCAKAPAN singkat -
  `BotOrchestrator` simpan 6 giliran terakhir per chat di cache (`tg_history:{chatId}`, TTL 30 mnt),
  diikutkan sebagai `messages` konteks ke klasifikasi AI (disimpan via `finish()` di semua cabang).
  Rujukan "tadi/tersebut" kini nyambung antar pesan.
- Teruji (tinker, preview tanpa konfirmasi): multi-turn "berapa total belum ditransfer" -> "Rp220.000",
  lalu "transfer total tersebut ref MB..." -> preview "semua, 2 pembayaran, Rp220.000, ref MB..." BENAR.

### FAILOVER 2 AI - Gemini (gratis) primer, Claude cadangan (`054dcca`)
User ingin hemat biaya: pakai Gemini gratis dulu, kalau tak merespons baru Claude. KEPUTUSAN user:
failover HANYA saat Gemini gagal/tak respons (error/timeout/rate-limit), BUKAN saat jawaban kurang akurat.
- Arsitektur: lapisan provider di belakang `AiGateway`. `AiProvider` interface (name/isConfigured/generate)
  + `AiResult` (text + tool ternormalisasi). `AnthropicProvider` (bungkus AnthropicClient) + `GeminiProvider`
  (Google AI Studio, tier gratis). `AiGateway` coba provider sesuai urutan `config('services.ai.primary')`
  (default gemini -> anthropic); provider GAGAL -> jatuh ke berikutnya; provider tanpa kredensial DILEWATI.
- GeminiProvider: endpoint `generativelanguage.googleapis.com/v1beta/models/{model}:generateContent?key=`,
  map messages (assistant->model, systemInstruction), terjemahkan tool Anthropic-style (`input_schema`) ->
  `functionDeclarations` dgn `toGeminiSchema()` (UPPERCASE nilai "type": STRING/OBJECT/...), toolConfig AUTO.
  Parse `candidates.0.content.parts` -> text + functionCall{name,args} jadi tool{name,input}.
- BotOrchestrator (`classify`) & TextToSqlService (`text`) kini lewat AiGateway, bukan AnthropicClient
  langsung. AnthropicClient tetap ada (dipakai AnthropicProvider). Model Gemini default `gemini-2.0-flash`.
- Config `services.gemini` + `services.ai.primary`; .env: GEMINI_API_KEY, GEMINI_MODEL, AI_PRIMARY=gemini.
  BACKWARD-COMPATIBLE: selama GEMINI_API_KEY kosong, Gemini dilewati -> Claude saja (perilaku lama).
- AKTIF & TERUJI: user kasih GEMINI_API_KEY (format baru `AQ.` dari AI Studio; auth via header
  `X-goog-api-key`, BUKAN `?key=`). MODEL: `gemini-flash-lite-latest` (dipilih setelah `gemini-2.0-flash`
  ternyata 404/dihentikan, dan `gemini-flash-latest` adalah model THINKING yang memotong output di token
  kecil + `thinkingBudget:0` ditolak 400). flash-lite: ringan, tanpa truncation, function-calling akurat.
  Teruji end-to-end via Gemini di hosting: baca "215 proyek" & "pengeluaran Rp2.222.071" (Text-to-SQL),
  tulis pengeluaran ditolak saldo, pendapatan proyek resolve "Website Starvvo" - semua BENAR. Claude jadi
  cadangan (hanya saat Gemini gagal). GEMINI_MODEL di .env hosting.
- PENANDA AI (`fec6d87`): tiap balasan diawali ikon penjawab - 🔵 Gemini (gratis) / 🟠 Claude (cadangan) -
  supaya user tahu AI mana yang dipakai. `AiGateway::lastProvider()` (di-reset tiap pesan); ikon ditambah
  di `BotOrchestrator::finish()` HANYA bila ada panggilan AI (giliran konfirmasi ya/tidak tak beri ikon).
  Keterangan ikon di /start. Teruji: baca/preview -> 🔵; batal -> tanpa ikon.
- FIX GLOSARIUM (`8f2978b`): user lapor jawaban "piutang" salah (AI tak paham istilah -> "tidak ditemukan",
  lalu daftar piutang MENYERTAKAN proyek LEAD/data uji tanpa filter status). Sebab: skema DB saja tak beri
  aturan bisnis. Solusi: tambah "ISTILAH & ATURAN BISNIS" ke prompt Text-to-SQL - piutang proyek = sisa
  (total_value-paid_amount) HANYA status WAITING/PROGRESS (cocok `DashboardController:46`); penjualan
  kecualikan CANCELLED+LEAD; pendapatan=payments; hutang/piutang umum=debt_records. Teruji: total piutang
  Rp5.025.000 (cocok aturan app, 4 proyek), rincian hanya 3 proyek WAITING/PROGRESS sisa>0 (Website Starvvo
  LEAD kini DIKECUALIKAN). Bila muncul istilah domain lain yang salah, tambah ke glosarium ini.

### Pending / lanjutan
- Bot 2 AI SELESAI: Gemini primer (gemini-flash-lite-latest) live + teruji; Claude cadangan. Pantau kualitas
  Gemini di pemakaian nyata (kalau sering meleset, bisa naikkan ke model lebih pintar via GEMINI_MODEL).
- Uji VN ASLI (ucapan) oleh user langsung di bot untuk cek akurasi Bahasa Indonesia (terutama nominal uang).
- CATATAN model Gemini: `gemini-2.0-flash` sudah 404. Model tersedia a.l. gemini-2.5-flash(-lite),
  gemini-flash(-lite)-latest, gemini-3.x-* (lihat ListModels). flash-lite = paling murah/ringan.
- User sebaiknya regenerate ANTHROPIC_API_KEY + TELEGRAM_BOT_TOKEN + GROQ_API_KEY (sempat di chat) lalu update .env hosting.
- Menggantung lama: `Claude-strack.bat` (untracked), `resources/views/errors/` + logo dari hosting belum
  di-commit. Odometer maintenance auto-DUE, multi-pilih Tahun (opsional).
- Bila mau, tambah aksi tulis lain (mis. buat proyek/klien baru, catat penarikan tunai, transaksi emas).

---

## Sesi 2026-08-12 (hapus integrasi Midtrans + rancangan bot Telegram AI)

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

### Diskusi rancangan: bot Telegram + AI tanya-jawab data (BELUM ADA KODE)
Sesi kedua hari ini murni DISKUSI/rancangan. Tidak ada file yang diubah untuk fitur ini. User ingin
kelak bisa bertanya soal data strack lewat Telegram dalam bahasa Indonesia dan dijawab AI.
- KONSEP yang disepakati: Telegram bot (BotFather) -> webhook ke Laravel (`/telegram/webhook`) ->
  panggil AI -> AI ubah pertanyaan jadi query -> jalankan ke DB -> jawab balik ke Telegram.
- KEPUTUSAN (condong): 
  - Pendekatan query = TEXT-TO-SQL (user pilih ini, di atas opsi "tools read-only terdefinisi").
    CATATAN WAJIB saat implementasi: buat USER MySQL READ-ONLY khusus (SELECT saja), blokir
    DROP/UPDATE/DELETE, pasang timeout + LIMIT, batasi tabel. Model murah/gratis lebih rawan salah
    tulis SQL untuk skema serumit strack -> pakai model yang cukup pintar (min Haiku).
  - Penyedia AI = condong ke ANTHROPIC API (console.anthropic.com), kemungkinan model Haiku.
    Model bisa diatur/diganti (Haiku `claude-haiku-4-5` / Sonnet `claude-sonnet-4-6`), bisa routing
    (simpel->Haiku, kompleks->Sonnet) atau perintah manual. API Anthropic TIDAK memakai data untuk training.
  - Implementasi sisi Laravel rencananya pakai `Http` facade (raw HTTP), TANPA SDK composer, biar
    ringan di Hostinger (pola sama seperti integrasi lama).
- KLARIFIKASI PENTING yang sudah dijelaskan ke user:
  - Claude API (console) TERPISAH TOTAL dari langganan Claude Pro. Pro = chat di claude.ai (produk jadi,
    manusia mengetik); API = mesin yang dipanggil program, tagihan sendiri (prabayar credit, per token).
    Bot hanya menyentuh dompet API; Pro tidak kena biaya tambahan.
  - Beda Claude Code/Pro (produk jadi, ada antarmuka, langganan flat) vs Console/API (mesin mentah untuk
    developer, dipanggil kode, bayar per pemakaian). Bot WAJIB pakai API karena tak ada manusia mengetik.
  - Estimasi biaya (Text-to-SQL, ~$0.015/tanya Haiku, ~$0.05/tanya Sonnet): pemakaian pribadi RINGAN
    (~beberapa tanya/hari) ~ $1-5/bulan (Haiku). Prompt caching menurunkan biaya. Bisa set SPEND LIMIT
    di console + sistem prabayar -> tak bakal kebobolan.
- SECURITY yang ditekankan untuk implementasi nanti: WHITELIST chat_id user saja (data keuangan sensitif),
  secret token webhook, user DB read-only. Hosting shared: cukup proses sinkron + indikator "mengetik",
  tak perlu queue worker.
- STATUS: user mau CEK dulu (daftar console + buat bot), sesi disudahi. Belum ada persetujuan mulai koding.

### Pending / lanjutan
- FITUR AI/TELEGRAM (langkah berikut, menunggu user):
  1. User: daftar `console.anthropic.com`, isi credit kecil (mis. $5), set spend limit, ambil API key.
  2. User: buat bot via @BotFather, ambil bot token + chat_id sendiri.
  3. Claude: bangun sisi Laravel (endpoint webhook, whitelist chat_id + secret, user MySQL read-only,
     mesin Text-to-SQL Haiku + guardrail, kirim balasan Telegram via Http facade).
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
