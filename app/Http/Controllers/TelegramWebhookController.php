<?php

namespace App\Http\Controllers;

use App\Services\Ai\TextToSqlService;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Endpoint webhook Telegram. Alur:
 *   verifikasi secret -> whitelist chat_id -> proses pertanyaan (Text-to-SQL)
 *   -> balas ke Telegram. Selalu balas 200 agar Telegram tidak retry.
 */
class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramService $telegram,
        private TextToSqlService $textToSql,
    ) {}

    public function handle(Request $request)
    {
        // 1. Verifikasi secret header (diset saat mendaftarkan webhook).
        $secret = config('services.telegram.webhook_secret');
        if ($secret && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            abort(403);
        }

        $message = $request->input('message') ?? $request->input('edited_message');
        $chatId = data_get($message, 'chat.id');
        $text = trim((string) data_get($message, 'text', ''));

        // Update tanpa pesan teks (join, sticker, dll) -> abaikan.
        if (! $chatId || $text === '') {
            return response('ok');
        }

        // 2. Whitelist chat_id (data keuangan sensitif).
        $allowed = config('services.telegram.allowed_chat_ids', []);
        if (! empty($allowed) && ! in_array((string) $chatId, $allowed, true)) {
            Log::warning('Telegram chat_id tidak diizinkan', ['chat_id' => $chatId]);
            $this->telegram->sendMessage($chatId, 'Maaf, Anda tidak memiliki akses ke bot ini.');
            return response('ok');
        }

        // 3. Perintah dasar.
        if ($text === '/start' || $text === '/help') {
            $this->telegram->sendMessage(
                $chatId,
                "Halo! Saya bot data strack. Tanyakan apa saja soal keuangan/proyek, "
                . "misalnya:\n- total pendapatan bulan ini\n- proyek yang masih WAITING\n"
                . "- sisa piutang klien"
            );
            return response('ok');
        }

        // 4. Proses pertanyaan.
        $this->telegram->sendChatAction($chatId, 'typing');

        try {
            $answer = $this->textToSql->ask($text);
            $this->telegram->sendMessage($chatId, $answer);
        } catch (RuntimeException $e) {
            if ($e->getMessage() === '__TIDAK_BISA__') {
                $this->telegram->sendMessage($chatId, 'Maaf, pertanyaan itu belum bisa saya jawab dari data yang ada.');
            } else {
                Log::warning('Telegram bot guardrail/validasi', ['error' => $e->getMessage(), 'q' => $text]);
                $this->telegram->sendMessage($chatId, 'Maaf, saya tidak bisa memproses pertanyaan itu dengan aman. Coba ubah kalimatnya.');
            }
        } catch (Throwable $e) {
            Log::error('Telegram bot error', ['error' => $e->getMessage(), 'q' => $text]);
            $this->telegram->sendMessage($chatId, 'Maaf, terjadi kendala saat mengambil data. Coba lagi sebentar.');
        }

        return response('ok');
    }
}
