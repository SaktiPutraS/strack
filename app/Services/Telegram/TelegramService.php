<?php

namespace App\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Pembungkus tipis Telegram Bot API untuk mengirim balasan & indikator mengetik.
 */
class TelegramService
{
    private function apiUrl(string $method): string
    {
        $token = config('services.telegram.bot_token');

        return "https://api.telegram.org/bot{$token}/{$method}";
    }

    public function sendMessage(int|string $chatId, string $text): void
    {
        // Telegram batasi 4096 karakter per pesan.
        foreach (str_split($text, 4000) as $chunk) {
            $response = Http::timeout(15)->post($this->apiUrl('sendMessage'), [
                'chat_id' => $chatId,
                'text'    => $chunk,
            ]);

            if ($response->failed()) {
                Log::warning('Telegram sendMessage gagal', [
                    'chat_id' => $chatId,
                    'error'   => $response->body(),
                ]);
            }
        }
    }

    public function sendChatAction(int|string $chatId, string $action = 'typing'): void
    {
        Http::timeout(10)->post($this->apiUrl('sendChatAction'), [
            'chat_id' => $chatId,
            'action'  => $action,
        ]);
    }
}
