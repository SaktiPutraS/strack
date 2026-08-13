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
