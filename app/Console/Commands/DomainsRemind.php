<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Services\Telegram\TelegramService;
use Illuminate\Console\Command;

/**
 * Kirim pengingat domain yang akan/segera kedaluwarsa ke Telegram.
 * Contoh: php artisan domains:remind --days=30
 */
class DomainsRemind extends Command
{
    protected $signature = 'domains:remind {--days=30 : Ambang hari sebelum kedaluwarsa}
                            {--force : Kirim walau tidak ada domain yang mendekati kedaluwarsa}';

    protected $description = 'Kirim pengingat domain akan habis ke Telegram';

    public function handle(TelegramService $telegram): int
    {
        $days = (int) $this->option('days');

        $domains = Domain::expiringWithin($days)
            ->orderBy('expires_at')
            ->get();

        if ($domains->isEmpty()) {
            $this->info('Tidak ada domain yang mendekati kedaluwarsa.');
            if (! $this->option('force')) {
                return self::SUCCESS;
            }
        }

        $chatIds = config('services.telegram.allowed_chat_ids', []);
        if (empty($chatIds)) {
            $this->warn('TELEGRAM_ALLOWED_CHAT_IDS kosong, tidak ada tujuan.');
            return self::SUCCESS;
        }

        $text = $this->buildMessage($domains, $days);

        foreach ($chatIds as $chatId) {
            $telegram->sendMessage($chatId, $text);
        }

        $this->info("Pengingat terkirim ({$domains->count()} domain) ke " . count($chatIds) . " chat.");

        return self::SUCCESS;
    }

    private function buildMessage($domains, int $days): string
    {
        if ($domains->isEmpty()) {
            return "🔔 Pengingat Domain\n\nTidak ada domain yang akan habis dalam {$days} hari ke depan.";
        }

        $lines = ["🔔 Pengingat Domain", "Domain yang perlu diperhatikan:\n"];

        foreach ($domains as $d) {
            $tgl = $d->expires_at?->format('d M Y');
            $sisa = $d->days_until_expiry;

            if ($sisa < 0) {
                $ket = "SUDAH LEWAT " . abs($sisa) . " hari";
            } elseif ($sisa === 0) {
                $ket = "HABIS HARI INI";
            } else {
                $ket = "{$sisa} hari lagi";
            }

            $lines[] = "- {$d->name} — {$ket} ({$tgl})";
        }

        return implode("\n", $lines);
    }
}
