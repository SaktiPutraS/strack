<?php

namespace App\Services\Ai;

use RuntimeException;

/**
 * Memilah GAMBAR yang dikirim ke bot: BUKTI TRANSFER (mutasi rekening / bukti
 * pembayaran) atau STRUK belanja. Untuk bukti transfer, nominal dan
 * keterangannya sekalian dibaca supaya cukup satu panggilan AI.
 *
 * Nilai uangnya hanya DIBACA di sini. Pencocokan dengan pembayaran yang belum
 * ditransfer dikerjakan di CatatTransferBuktiAction, bukan oleh AI.
 */
class TransferProofParser
{
    /** Batas ukuran gambar yang dikirim ke AI (foto Telegram jauh di bawah ini). */
    private const MAX_BYTES = 8 * 1024 * 1024;

    public const KIND_TRANSFER = 'TRANSFER';
    public const KIND_RECEIPT = 'STRUK';
    public const KIND_OTHER = 'LAIN';

    public function __construct(private AiGateway $ai) {}

    /**
     * @param  string  $binary  isi biner gambar
     * @param  string  $mime    tipe gambar (image/jpeg, image/png, ...)
     * @param  string  $hint    catatan tambahan dari user (caption Telegram)
     * @return array{jenis: string, nominal: int, tanggal: ?string, ref: ?string, bank: ?string, keterangan: ?string}
     */
    public function parse(string $binary, string $mime = 'image/jpeg', string $hint = ''): array
    {
        if ($binary === '') {
            throw new RuntimeException('Gambarnya kosong.');
        }

        if (strlen($binary) > self::MAX_BYTES) {
            throw new RuntimeException('Gambarnya terlalu besar. Kirim ulang dengan ukuran lebih kecil.');
        }

        $instruction = 'Periksa gambar ini lalu balas JSON sesuai aturan.';
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
            600,
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
            // Tidak bisa dipilah: perlakukan sebagai struk supaya alur lama tetap jalan.
            return ['jenis' => self::KIND_RECEIPT];
        }

        $data = json_decode(substr($text, $start, $end - $start + 1), true);

        return is_array($data) ? $data : ['jenis' => self::KIND_RECEIPT];
    }

    /** Bersihkan hasil baca AI supaya aman dipakai pencocokan. */
    public function normalize(array $data): array
    {
        $jenis = strtoupper(trim((string) ($data['jenis'] ?? '')));

        if (! in_array($jenis, [self::KIND_TRANSFER, self::KIND_RECEIPT, self::KIND_OTHER], true)) {
            $jenis = self::KIND_OTHER;
        }

        return [
            'jenis'      => $jenis,
            'nominal'    => $this->int($data['nominal'] ?? 0),
            'tanggal'    => $this->str($data['tanggal'] ?? null, 20),
            'ref'        => $this->str($data['ref'] ?? null, 50),
            'bank'       => $this->str($data['bank'] ?? null, 30),
            'keterangan' => $this->str($data['keterangan'] ?? null, 80),
        ];
    }

    /** Angka dari AI bisa berupa "415.000" / "IDR 415,000.00". Ambil nilai bulat. */
    private function int(mixed $value): int
    {
        // Buang pecahan di ujung dulu ("415,000.00" dan "415.000,00" jadi 415000),
        // sisanya cukup ambil angkanya saja. Pemisah ribuan 3 digit tidak ikut terbuang.
        $text = preg_replace('/[.,]\d{1,2}$/', '', trim((string) $value));
        $digits = preg_replace('/[^0-9]/', '', (string) $text);

        return $digits === '' ? 0 : (int) $digits;
    }

    private function str(mixed $value, int $max): ?string
    {
        $text = trim((string) ($value ?? ''));

        if ($text === '' || strtolower($text) === 'null') {
            return null;
        }

        return mb_substr($text, 0, $max);
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Kamu memilah GAMBAR yang dikirim ke aplikasi keuangan pribadi "strack".
Gambar bisa berupa BUKTI TRANSFER, STRUK BELANJA, atau hal lain.
BALAS HANYA JSON, tanpa kalimat pengantar, tanpa blok kode.

Bila gambar adalah BUKTI TRANSFER / bukti pembayaran / mutasi rekening (bank atau dompet digital):
{"jenis":"TRANSFER","nominal":415000,"tanggal":"2026-08-21","ref":"760967985900","bank":"OCTO","keterangan":"SAKTI PUTRA S"}

Bila gambar adalah STRUK BELANJA (ada DAFTAR BARANG beserta harganya):
{"jenis":"STRUK"}

Bila bukan keduanya:
{"jenis":"LAIN"}

Aturan pengisian bukti transfer:
- "nominal": jumlah uang utama pada bukti, angka bulat tanpa titik dan tanpa desimal.
  "IDR 415,000.00" jadi 415000; "Rp 1.250.000" jadi 1250000.
- "tanggal": tanggal transaksi dengan format Y-m-d. "21 Aug 2026 09:34" jadi "2026-08-21".
  Isi null bila tidak terbaca.
- "ref": nomor referensi / nomor transaksi bila ada, selain itu null.
- "bank": nama bank atau aplikasi pembayarannya, mis. OCTO, BCA, Mandiri, BRI, DANA. null bila tidak jelas.
- "keterangan": nama pengirim / penerima atau berita transfernya, singkat saja. null bila tidak ada.

Pembeda utama: struk belanja memuat daftar barang, sedangkan bukti transfer hanya memuat satu
nominal utama beserta data rekening / waktu transaksi.
PROMPT;
    }
}
