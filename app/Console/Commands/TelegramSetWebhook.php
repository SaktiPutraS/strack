<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Mendaftarkan (atau menghapus) URL webhook bot Telegram beserta secret token.
 * Contoh:
 *   php artisan telegram:set-webhook https://strack.my.id/telegram/webhook
 *   php artisan telegram:set-webhook --delete
 *   php artisan telegram:set-webhook --info
 */
class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:set-webhook {url? : URL webhook publik}
                            {--delete : Hapus webhook}
                            {--info : Tampilkan info webhook saat ini}';

    protected $description = 'Daftarkan/hapus/cek webhook bot Telegram';

    public function handle(): int
    {
        $token = config('services.telegram.bot_token');
        if (empty($token)) {
            $this->error('TELEGRAM_BOT_TOKEN belum diisi di .env.');
            return self::FAILURE;
        }

        $base = "https://api.telegram.org/bot{$token}/";

        if ($this->option('info')) {
            $this->line(json_encode(Http::get($base . 'getWebhookInfo')->json(), JSON_PRETTY_PRINT));
            return self::SUCCESS;
        }

        if ($this->option('delete')) {
            $res = Http::post($base . 'deleteWebhook', ['drop_pending_updates' => true])->json();
            $this->line(json_encode($res, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        }

        $url = $this->argument('url');
        if (empty($url)) {
            $this->error('URL webhook wajib diisi. Contoh: php artisan telegram:set-webhook https://strack.my.id/telegram/webhook');
            return self::FAILURE;
        }

        $secret = config('services.telegram.webhook_secret');
        if (empty($secret)) {
            $this->warn('TELEGRAM_WEBHOOK_SECRET kosong. Sangat disarankan mengisinya untuk keamanan.');
        }

        $res = Http::post($base . 'setWebhook', array_filter([
            'url'             => $url,
            'secret_token'    => $secret ?: null,
            // message_reaction WAJIB disebut, kalau tidak Telegram tidak pernah
            // mengirim update reaksi (jempol di rekap = konfirmasi).
            'allowed_updates' => json_encode(['message', 'edited_message', 'message_reaction']),
        ]))->json();

        $this->line(json_encode($res, JSON_PRETTY_PRINT));

        return ($res['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
    }
}
