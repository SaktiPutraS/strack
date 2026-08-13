<?php

namespace App\Http\Controllers;

use App\Services\Ai\BotOrchestrator;
use App\Services\Ai\TranscriptionService;
use App\Services\Telegram\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Endpoint webhook Telegram. Alur:
 *   verifikasi secret -> whitelist chat_id -> (voice note ditranskrip dulu) ->
 *   proses pesan -> balas ke Telegram. Selalu balas 200 agar Telegram tidak retry.
 */
class TelegramWebhookController extends Controller
{
    public function __construct(
        private TelegramService $telegram,
        private BotOrchestrator $orchestrator,
        private TranscriptionService $transcription,
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
        $voice = data_get($message, 'voice') ?? data_get($message, 'audio');

        // Update tanpa isi yang bisa diproses (join, sticker, dll) -> abaikan.
        if (! $chatId || ($text === '' && ! $voice)) {
            return response('ok');
        }

        // 2. Whitelist chat_id (data keuangan sensitif).
        $allowed = config('services.telegram.allowed_chat_ids', []);
        if (! empty($allowed) && ! in_array((string) $chatId, $allowed, true)) {
            Log::warning('Telegram chat_id tidak diizinkan', ['chat_id' => $chatId]);
            $this->telegram->sendMessage($chatId, 'Maaf, Anda tidak memiliki akses ke bot ini.');
            return response('ok');
        }

        // 2b. Voice note -> transkrip jadi teks lebih dulu.
        $isVoice = false;
        if ($text === '' && $voice) {
            $this->telegram->sendChatAction($chatId, 'typing');
            try {
                $audio = $this->telegram->downloadFile(data_get($voice, 'file_id'));
                $text = trim($this->transcription->transcribe($audio));
            } catch (Throwable $e) {
                Log::warning('Telegram voice transkripsi gagal', ['error' => $e->getMessage()]);
                $this->telegram->sendMessage($chatId, 'Maaf, saya gagal memproses voice note itu. Coba ketik atau ulangi.');
                return response('ok');
            }

            if ($text === '') {
                $this->telegram->sendMessage($chatId, 'Maaf, suara di voice note tidak terdengar jelas. Coba ulangi.');
                return response('ok');
            }

            $isVoice = true;
        }

        // 3. Perintah dasar.
        if ($text === '/start' || $text === '/help') {
            $this->telegram->sendMessage(
                $chatId,
                "Halo! Saya bot strack. Saya bisa:\n\n"
                . "MENJAWAB pertanyaan data, misalnya:\n"
                . "- total pendapatan bulan ini\n- proyek yang masih dikerjakan\n- sisa piutang\n\n"
                . "MENCATAT/MENGUBAH data (selalu minta konfirmasi dulu), misalnya:\n"
                . "- catat pengeluaran bensin 50rb dari cash\n- catat pembayaran DP 2jt proyek Website Starvvo\n"
                . "- bayar hutang ke Budi 500rb\n- tandai proyek X selesai\n\n"
                . "Setiap perubahan data akan saya konfirmasi dulu (balas *ya* untuk simpan).\n\n"
                . "Penanda di awal balasan: 🔵 = dijawab Gemini (gratis), 🟠 = dijawab Claude (cadangan)."
            );
            return response('ok');
        }

        // 4. Proses pesan (baca via Text-to-SQL / tulis via aksi + konfirmasi).
        $this->telegram->sendChatAction($chatId, 'typing');

        try {
            $answer = $this->orchestrator->handle($chatId, $text);
            // Untuk voice note, tampilkan hasil transkripsi agar user tahu yang saya dengar.
            if ($isVoice) {
                $answer = "🎤 \"{$text}\"\n\n{$answer}";
            }
            $this->telegram->sendMessage($chatId, $answer);
        } catch (RuntimeException $e) {
            if ($e->getMessage() === '__TIDAK_BISA__') {
                $this->telegram->sendMessage($chatId, 'Maaf, itu belum bisa saya proses dari data yang ada.');
            } else {
                Log::warning('Telegram bot guardrail/validasi', ['error' => $e->getMessage(), 'q' => $text]);
                $this->telegram->sendMessage($chatId, 'Maaf, saya tidak bisa memproses itu dengan aman. Coba ubah kalimatnya.');
            }
        } catch (Throwable $e) {
            Log::error('Telegram bot error', ['error' => $e->getMessage(), 'q' => $text]);
            $this->telegram->sendMessage($chatId, 'Maaf, terjadi kendala. Coba lagi sebentar.');
        }

        return response('ok');
    }
}
