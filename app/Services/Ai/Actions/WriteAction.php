<?php

namespace App\Services\Ai\Actions;

use RuntimeException;

/**
 * Kontrak satu "aksi tulis" yang bisa dipanggil bot lewat tool use.
 * Alur: AI panggil tool -> prepare() (validasi + resolusi, boleh throw pesan ID)
 * -> preview() (teks konfirmasi) -> [user balas ya] -> execute() (simpan).
 */
abstract class WriteAction
{
    /** Nama tool (dipakai AI). Harus unik, snake_case. */
    abstract public function name(): string;

    /** Definisi tool untuk Anthropic API (name, description, input_schema). */
    abstract public function toolDefinition(): array;

    /**
     * Validasi + resolusi input mentah dari AI menjadi payload siap simpan.
     * Lempar RuntimeException dengan pesan Bahasa Indonesia bila tidak valid.
     *
     * @return array payload ternormalisasi (harus JSON-serializable untuk cache)
     */
    abstract public function prepare(array $input): array;

    /** Teks konfirmasi yang ditampilkan sebelum menyimpan. */
    abstract public function preview(array $prepared): string;

    /** Eksekusi penyimpanan. Kembalikan pesan sukses. Boleh throw bila state berubah. */
    abstract public function execute(array $prepared): string;

    /**
     * Aksi tersembunyi tidak ditawarkan ke AI sebagai tool (dipicu dari jalur
     * lain, mis. foto struk), tapi tetap bisa dijalankan lewat konfirmasi.
     */
    public function hidden(): bool
    {
        return false;
    }

    /** Berapa lama aksi menunggu konfirmasi sebelum kedaluwarsa. */
    public function pendingTtlMinutes(): int
    {
        return 5;
    }

    /** Apakah user boleh mengoreksi hasil sebelum menyimpan (lihat refine). */
    public function supportsRefine(): bool
    {
        return false;
    }

    /**
     * Terapkan koreksi dari user pada payload yang sedang menunggu konfirmasi.
     * Lempar NotACorrectionException bila balasan user ternyata bukan koreksi
     * (agar bot memperlakukannya sebagai permintaan baru).
     */
    public function refine(array $prepared, string $instruction): array
    {
        throw new NotACorrectionException();
    }

    /** Helper: ambil & bersihkan angka rupiah dari input. */
    protected function parseAmount(mixed $value): int
    {
        $digits = preg_replace('/[^0-9]/', '', (string) $value);

        if ($digits === '' || (int) $digits <= 0) {
            throw new RuntimeException('Jumlah tidak valid. Sebutkan nominal yang jelas.');
        }

        return (int) $digits;
    }

    /** Helper: format rupiah. */
    protected function rp(int|float $n): string
    {
        return 'Rp' . number_format((float) $n, 0, ',', '.');
    }

    /** Helper: normalisasi tanggal ke Y-m-d, default hari ini. */
    protected function parseDate(?string $value): string
    {
        if (empty($value)) {
            return now()->toDateString();
        }

        try {
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return now()->toDateString();
        }
    }
}
