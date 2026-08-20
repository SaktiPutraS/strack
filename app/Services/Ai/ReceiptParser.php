<?php

namespace App\Services\Ai;

use App\Models\Expense;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

/**
 * Membaca FOTO STRUK belanja lewat AI (vision) menjadi daftar item terstruktur
 * yang sudah diberi kategori pengeluaran strack.
 *
 * Kategori TIDAK ditebak dari daftar kata kunci di kode, melainkan dicontohkan
 * dari riwayat pengeluaran pemilik sendiri (lihat categoryExamples), supaya
 * pengelompokan mengikuti kebiasaan yang sudah ada dan ikut menyesuaikan kalau
 * kebiasaannya berubah.
 */
class ReceiptParser
{
    /** Batas ukuran gambar yang dikirim ke AI (foto Telegram jauh di bawah ini). */
    private const MAX_BYTES = 8 * 1024 * 1024;

    /** Riwayat yang dibaca untuk contoh + berapa contoh per kategori yang dikirim. */
    private const HISTORY_ROWS = 400;
    private const EXAMPLES_PER_CATEGORY = 14;
    private const EXAMPLES_TTL_MINUTES = 180;

    public const EXAMPLES_CACHE_KEY = 'receipt_category_examples';

    public function __construct(private AiGateway $ai) {}

    /**
     * @param  string  $binary  isi biner gambar struk
     * @param  string  $mime    tipe gambar (image/jpeg, image/png, ...)
     * @param  string  $hint    catatan tambahan dari user (caption Telegram)
     * @return array{toko: string, tanggal: ?string, ref: ?string, items: array<int, array>, voucher: int, ongkir: int, total: int}
     */
    public function parse(string $binary, string $mime = 'image/jpeg', string $hint = ''): array
    {
        if ($binary === '') {
            throw new RuntimeException('Gambar struk kosong.');
        }

        if (strlen($binary) > self::MAX_BYTES) {
            throw new RuntimeException('Gambar struk terlalu besar. Kirim ulang dengan ukuran lebih kecil.');
        }

        $instruction = 'Baca struk pada gambar ini lalu balas JSON sesuai aturan.';
        if (trim($hint) !== '') {
            $instruction .= ' Catatan dari pengirim: ' . trim($hint);
        }

        $raw = $this->ai->text(
            $this->systemPrompt(),
            [[
                'role' => 'user',
                'content' => [
                    ['type' => 'image', 'mime' => $mime, 'data' => base64_encode($binary)],
                    ['type' => 'text', 'text' => $instruction],
                ],
            ]],
            3000,
        );

        return $this->normalize($this->decodeJson($raw));
    }

    /** Ambil objek JSON dari balasan AI (tahan terhadap blok kode / kalimat pengantar). */
    public function decodeJson(string $raw): array
    {
        $text = trim($raw);
        $start = strpos($text, '{');
        $end = strrpos($text, '}');

        if ($start === false || $end === false || $end <= $start) {
            throw new RuntimeException('Saya tidak berhasil membaca struk itu. Coba foto ulang lebih jelas.');
        }

        $data = json_decode(substr($text, $start, $end - $start + 1), true);

        if (! is_array($data)) {
            throw new RuntimeException('Saya tidak berhasil membaca struk itu. Coba foto ulang lebih jelas.');
        }

        return $data;
    }

    /** Bersihkan & lengkapi hasil baca AI supaya aman dipakai perhitungan. */
    public function normalize(array $data): array
    {
        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            if (! is_array($item)) {
                continue;
            }

            $nama = trim((string) ($item['nama'] ?? ''));
            if ($nama === '') {
                continue;
            }

            $harga = $this->int($item['harga'] ?? 0);
            $diskon = min($this->int($item['diskon'] ?? 0), $harga);
            $kategori = strtoupper(trim((string) ($item['kategori'] ?? '')));

            $items[] = [
                'nama'     => mb_substr($nama, 0, 60),
                'qty'      => max(1, $this->int($item['qty'] ?? 1)),
                'harga'    => $harga,
                'diskon'   => $diskon,
                'net'      => $harga - $diskon,
                'kategori' => array_key_exists($kategori, Expense::CATEGORIES) ? $kategori : 'LAINNYA',
            ];
        }

        if ($items === []) {
            throw new RuntimeException('Tidak ada satu pun item belanja yang terbaca di gambar itu.');
        }

        $toko = trim((string) ($data['toko'] ?? ''));
        $tanggal = trim((string) ($data['tanggal'] ?? ''));
        $ref = trim((string) ($data['ref'] ?? ''));

        return [
            'toko'    => $toko !== '' ? mb_substr($toko, 0, 40) : 'Belanja',
            'tanggal' => $tanggal !== '' ? $tanggal : null,
            'ref'     => $ref !== '' ? mb_substr($ref, 0, 40) : null,
            'items'   => $items,
            'voucher' => $this->int($data['voucher'] ?? 0),
            'ongkir'  => $this->int($data['ongkir'] ?? 0),
            'total'   => $this->int($data['total'] ?? 0),
        ];
    }

    /** Angka dari AI bisa berupa "48.600" / "-1.400" / 48600. Ambil nilai bulat positif. */
    private function int(mixed $value): int
    {
        return (int) abs((int) preg_replace('/[^0-9-]/', '', (string) $value));
    }

    private function systemPrompt(): string
    {
        $kategori = collect(Expense::CATEGORIES)
            ->map(fn ($label, $key) => "{$key} = {$label}")
            ->implode("\n");

        $contoh = $this->categoryExamples();

        return <<<PROMPT
Kamu pembaca struk belanja untuk aplikasi keuangan pribadi "strack".
Dari GAMBAR struk, keluarkan datanya sebagai JSON. BALAS HANYA JSON, tanpa kalimat pengantar,
tanpa blok kode.

Bentuk JSON:
{
  "toko": "Alfagift",
  "tanggal": "2026-08-20",
  "ref": "S-260820-AGRYVQB",
  "items": [
    {"nama": "Aqua Galon", "qty": 2, "harga": 48600, "diskon": 1400, "kategori": "SEMBAKO"}
  ],
  "voucher": 1000,
  "ongkir": 0,
  "total": 72600
}

Aturan pengisian:
- "items" memuat SEMUA baris barang di struk. Jangan ada yang dilewati, jangan mengarang barang.
- "nama": nama PENDEK dan umum, bukan nama panjang di struk.
  Contoh: "Aqua Air Mineral Galon (Isi Ulang) 19 L" jadi "Aqua Galon";
  "Kun Minuman Susu UHT Chocomalt Nata de Coco 100 ml" jadi "Kun Susu UHT";
  "Bebelac Susu Formula Cair Rasa Stroberi 105 ml" jadi "Bebelac".
- "qty": jumlah barang pada baris itu (minimal 1).
- "harga": total baris SEBELUM potongan (harga satuan dikali qty), angka bulat tanpa titik.
- "diskon": potongan yang tertulis DI BARIS ITU (mis. "Disc. -1.400" jadi 1400). Isi 0 bila tidak ada.
- "voucher": potongan yang berlaku untuk SELURUH belanja (voucher, potongan tambahan, cashback).
  Isi 0 bila tidak ada. JANGAN memasukkan jumlah diskon per item ke sini.
- "ongkir": biaya pengiriman atau biaya layanan. Isi 0 bila gratis atau tidak ada.
- "total": jumlah akhir yang dibayar sesuai struk.
- "toko": nama toko. Struk Alfamart yang dikirim lewat aplikasi Alfagift ditulis "Alfagift".
- "tanggal": tanggal transaksi di struk dengan format Y-m-d. Isi null bila tidak terbaca.
  Perhatikan urutan bulan dan tanggal: "08-20-2026" berarti 2026-08-20.
- "ref": nomor referensi atau nomor struk bila ada, selain itu null.
- "kategori": WAJIB salah satu KODE di bawah ini (kodenya, bukan labelnya).

KODE KATEGORI:
{$kategori}

{$contoh}
PROMPT;
    }

    /**
     * Contoh pengelompokan dari riwayat pengeluaran pemilik sendiri, supaya AI
     * mengikuti kebiasaan yang sudah ada (mis. Bebelac dan Tango ke SIERRA).
     * Di-cache karena isinya jarang berubah.
     */
    public function categoryExamples(): string
    {
        return Cache::remember(
            self::EXAMPLES_CACHE_KEY,
            now()->addMinutes(self::EXAMPLES_TTL_MINUTES),
            function () {
                $rows = Expense::query()
                    ->select('category', 'description')
                    ->where('description', 'like', '%alfa%')
                    ->orderByDesc('expense_date')
                    ->orderByDesc('id')
                    ->limit(self::HISTORY_ROWS)
                    ->get();

                $byCategory = [];
                foreach ($rows as $row) {
                    if (! array_key_exists($row->category, Expense::CATEGORIES)) {
                        continue;
                    }

                    foreach ($this->itemsFromDescription((string) $row->description) as $item) {
                        $key = mb_strtolower($item);
                        if (! isset($byCategory[$row->category][$key])) {
                            $byCategory[$row->category][$key] = $item;
                        }
                    }
                }

                $lines = [];
                foreach ($byCategory as $category => $items) {
                    $sample = array_slice(array_values($items), 0, self::EXAMPLES_PER_CATEGORY);
                    if ($sample === []) {
                        continue;
                    }

                    $lines[] = $category . ': ' . implode(', ', $sample);
                }

                if ($lines === []) {
                    return '';
                }

                return "ACUAN dari catatan pemilik sendiri (barang serupa taruh di kategori yang sama):\n"
                    . implode("\n", $lines);
            }
        );
    }

    /**
     * Pecah deskripsi lama menjadi nama-nama barang.
     * "Alfagift (28/7) - Susu Beruang, Coca-Cola & Roti Sisir"
     * jadi [Susu Beruang, Coca-Cola, Roti Sisir].
     */
    private function itemsFromDescription(string $description): array
    {
        $pos = mb_strpos($description, ' - ');
        $body = $pos === false ? $description : mb_substr($description, $pos + 3);

        $parts = preg_split('/\s*(?:,|&|\bdan\b)\s*/iu', $body) ?: [];

        $items = [];
        foreach ($parts as $part) {
            // Buang embel-embel jumlah di ujung: "Galon (2)" atau "Mie Sedaap 5".
            $clean = trim(preg_replace('/\s*(\(\d+\)|\d+)\s*$/u', '', trim($part)));
            $length = mb_strlen($clean);

            if ($length >= 3 && $length <= 30 && ! preg_match('/^dll\.?$/iu', $clean)) {
                $items[] = $clean;
            }
        }

        return $items;
    }
}
