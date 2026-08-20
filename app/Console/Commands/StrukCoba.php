<?php

namespace App\Console\Commands;

use App\Services\Ai\Actions\ActionRegistry;
use App\Services\Ai\AiGateway;
use App\Services\Ai\ReceiptParser;
use Illuminate\Console\Command;
use Throwable;

/**
 * Uji pembacaan foto struk tanpa lewat Telegram. Berguna untuk memeriksa hasil
 * baca AI di hosting memakai gambar nyata:
 *   php artisan struk:coba storage/app/struk.jpg
 *   php artisan struk:coba storage/app/struk.jpg --simpan
 */
class StrukCoba extends Command
{
    protected $signature = 'struk:coba
                            {file : Path file gambar struk}
                            {--simpan : Simpan ke pengeluaran (tanpa opsi ini hanya ditampilkan)}
                            {--catatan= : Catatan tambahan, seperti caption di Telegram}';

    protected $description = 'Baca foto struk lewat AI lalu tampilkan rekap pengeluarannya';

    public function handle(ReceiptParser $parser, ActionRegistry $registry, AiGateway $ai): int
    {
        $file = $this->argument('file');

        if (! is_file($file)) {
            $this->error("File tidak ditemukan: {$file}");

            return self::FAILURE;
        }

        $mime = (string) (mime_content_type($file) ?: 'image/jpeg');

        if (! str_starts_with($mime, 'image/')) {
            $this->error("File itu bukan gambar (terbaca {$mime}).");

            return self::FAILURE;
        }

        $this->info('Membaca struk lewat AI...');

        try {
            $parsed = $parser->parse(file_get_contents($file), $mime, (string) $this->option('catatan'));
            $action = $registry->find('catat_struk');
            $prepared = $action->prepare($parsed + ['hint' => (string) $this->option('catatan')]);
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('Dibaca oleh: ' . ($ai->lastProvider() ?? 'tidak diketahui'));
        $this->newLine();

        $this->line('ITEM TERBACA:');
        foreach ($parsed['items'] as $i => $item) {
            $this->line(sprintf(
                '  %d. %-28s x%d  %10s - %-8s = %10s  [%s]',
                $i + 1,
                $item['nama'],
                $item['qty'],
                number_format($item['harga'], 0, ',', '.'),
                number_format($item['diskon'], 0, ',', '.'),
                number_format($item['net'], 0, ',', '.'),
                $item['kategori']
            ));
        }

        $this->newLine();
        $this->line('REKAP YANG AKAN DICATAT:');
        $this->line($action->preview($prepared));

        if (! $this->option('simpan')) {
            $this->newLine();
            $this->comment('Tidak ada yang disimpan. Tambahkan --simpan bila hasilnya sudah benar.');

            return self::SUCCESS;
        }

        try {
            $this->newLine();
            $this->info($action->execute($prepared));
        } catch (Throwable $e) {
            $this->error('Gagal menyimpan: ' . $e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
