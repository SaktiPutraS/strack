<?php

namespace App\Console\Commands;

use App\Services\Ai\Actions\ActionRegistry;
use App\Services\Ai\AiGateway;
use App\Services\Ai\TransferProofParser;
use Illuminate\Console\Command;
use Throwable;

/**
 * Uji pembacaan foto BUKTI TRANSFER tanpa lewat Telegram. Berguna untuk
 * memeriksa hasil baca AI di hosting memakai gambar nyata:
 *   php artisan transfer:coba storage/app/bukti.jpg
 *   php artisan transfer:coba storage/app/bukti.jpg --simpan
 */
class TransferCoba extends Command
{
    protected $signature = 'transfer:coba
                            {file : Path file gambar bukti transfer}
                            {--simpan : Catat sebagai transfer (tanpa opsi ini hanya ditampilkan)}
                            {--catatan= : Catatan tambahan, seperti caption di Telegram}';

    protected $description = 'Baca foto bukti transfer lewat AI lalu cocokkan dengan pembayaran yang belum ditransfer';

    public function handle(TransferProofParser $parser, ActionRegistry $registry, AiGateway $ai): int
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

        $this->info('Membaca bukti transfer lewat AI...');

        try {
            $proof = $parser->parse(file_get_contents($file), $mime, (string) $this->option('catatan'));
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('Dibaca oleh: ' . ($ai->lastProvider() ?? 'tidak diketahui'));
        $this->newLine();

        $this->line('HASIL BACA:');
        foreach ($proof as $key => $value) {
            $this->line(sprintf('  %-11s : %s', $key, $value === null ? '-' : $value));
        }

        if ($proof['jenis'] !== TransferProofParser::KIND_TRANSFER) {
            $this->newLine();
            $this->warn("Gambar itu tidak terbaca sebagai bukti transfer (jenis: {$proof['jenis']}).");
            $this->comment('Kalau memang struk belanja, pakai: php artisan struk:coba ' . $file);

            return self::SUCCESS;
        }

        $action = $registry->find('catat_transfer_bukti');

        try {
            $prepared = $action->prepare($proof);
        } catch (Throwable $e) {
            $this->newLine();
            $this->line($e->getMessage());

            return self::SUCCESS;
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
