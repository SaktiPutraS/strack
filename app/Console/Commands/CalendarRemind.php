<?php

namespace App\Console\Commands;

use App\Services\Calendar\DailyDigest;
use App\Services\Telegram\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;

/**
 * Kirim isi kalender hari ini ke Telegram (agenda pribadi + deadline proyek +
 * domain kedaluwarsa + maintenance + jatuh tempo hutang piutang). TODO TIDAK IKUT.
 *
 * Contoh: php artisan calendar:remind
 *         php artisan calendar:remind --date=2026-09-01 --force
 */
class CalendarRemind extends Command
{
    protected $signature = 'calendar:remind {--date= : Tanggal Y-m-d, default hari ini}
                            {--user=admin : Pemilik agenda pribadi (kolom user_id)}
                            {--force : Kirim walau hari itu kosong}
                            {--dry : Tampilkan pesan di layar tanpa mengirim}';

    protected $description = 'Kirim agenda hari ini ke Telegram';

    public function handle(DailyDigest $digest, TelegramService $telegram): int
    {
        try {
            $date = $this->option('date')
                ? Carbon::parse($this->option('date'))->startOfDay()
                : Carbon::today();
        } catch (\Throwable $e) {
            $this->error('Tanggal tidak valid: ' . $this->option('date'));

            return self::FAILURE;
        }

        $text = $digest->buildMessage($date, (string) $this->option('user'));

        if ($text === null) {
            $this->info('Tidak ada agenda pada ' . $date->format('Y-m-d') . '.');

            if (! $this->option('force')) {
                return self::SUCCESS;
            }

            $text = $digest->emptyMessage($date);
        }

        if ($this->option('dry')) {
            $this->line($text);

            return self::SUCCESS;
        }

        $chatIds = config('services.telegram.allowed_chat_ids', []);

        if (empty($chatIds)) {
            $this->warn('TELEGRAM_ALLOWED_CHAT_IDS kosong, tidak ada tujuan.');

            return self::SUCCESS;
        }

        foreach ($chatIds as $chatId) {
            $telegram->sendMessage($chatId, $text);
        }

        $this->info('Pengingat agenda terkirim ke ' . count($chatIds) . ' chat.');

        return self::SUCCESS;
    }
}
