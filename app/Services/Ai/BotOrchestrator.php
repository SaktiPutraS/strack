<?php

namespace App\Services\Ai;

use App\Services\Ai\Actions\ActionRegistry;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

/**
 * Otak bot: untuk tiap pesan memutuskan BACA (Text-to-SQL) atau TULIS (aksi
 * terdefinisi via tool use). Aksi tulis selalu lewat langkah KONFIRMASI:
 * aksi tertunda disimpan di cache per chat, dieksekusi setelah user balas "ya".
 */
class BotOrchestrator
{
    /** Kata konfirmasi/pembatalan (dicek sebagai teks utuh). */
    private const AFFIRM = ['ya', 'iya', 'y', 'ok', 'oke', 'okay', 'yes', 'betul', 'benar', 'lanjut', 'simpan', 'gas', 'boleh', 'setuju', 'yoi'];
    private const DENY = ['tidak', 'ga', 'gak', 'engga', 'enggak', 'nggak', 'batal', 'jangan', 'no', 'n', 'cancel', 'stop'];

    private const PENDING_TTL_MINUTES = 5;

    public function __construct(
        private AnthropicClient $ai,
        private TextToSqlService $textToSql,
        private ActionRegistry $registry,
    ) {}

    public function handle(int|string $chatId, string $text): string
    {
        $text = trim($text);
        $normalized = $this->normalize($text);
        $pendingKey = 'tg_pending:' . $chatId;
        $pending = Cache::get($pendingKey);

        // 1. Ada aksi menunggu konfirmasi?
        if ($pending) {
            if (in_array($normalized, self::AFFIRM, true)) {
                Cache::forget($pendingKey);
                return $this->runPending($pending);
            }

            if (in_array($normalized, self::DENY, true)) {
                Cache::forget($pendingKey);
                return 'Baik, dibatalkan.';
            }

            // Selain ya/tidak -> anggap permintaan baru, buang yang tertunda.
            Cache::forget($pendingKey);
        }

        // 2. Klasifikasi via AI + tool use.
        $response = $this->ai->raw([
            'system' => [[
                'type' => 'text',
                'text' => $this->systemPrompt(),
                'cache_control' => ['type' => 'ephemeral'],
            ]],
            'messages' => [['role' => 'user', 'content' => $text]],
            'tools' => array_merge([$this->tanyaDataTool()], $this->registry->toolDefinitions()),
            'tool_choice' => ['type' => 'auto'],
            'max_tokens' => 1024,
        ]);

        $tool = AnthropicClient::extractToolUse($response);

        // Tidak memanggil tool -> AI menjawab/menanyakan sesuatu langsung.
        if (! $tool) {
            $reply = AnthropicClient::extractText($response);
            return $reply !== '' ? $reply : 'Maaf, saya belum paham. Coba jelaskan lagi.';
        }

        // Baca data.
        if ($tool['name'] === 'tanya_data') {
            $pertanyaan = $tool['input']['pertanyaan'] ?? $text;
            return $this->textToSql->ask($pertanyaan);
        }

        // Aksi tulis -> siapkan + minta konfirmasi.
        $action = $this->registry->find($tool['name']);
        if (! $action) {
            return 'Maaf, aksi itu belum tersedia.';
        }

        try {
            $prepared = $action->prepare($tool['input']);
        } catch (RuntimeException $e) {
            return $e->getMessage();
        }

        Cache::put($pendingKey, [
            'action' => $action->name(),
            'prepared' => $prepared,
        ], now()->addMinutes(self::PENDING_TTL_MINUTES));

        return $action->preview($prepared);
    }

    private function runPending(array $pending): string
    {
        $action = $this->registry->find($pending['action']);
        if (! $action) {
            return 'Maaf, aksi sudah tidak tersedia.';
        }

        try {
            return $action->execute($pending['prepared']);
        } catch (RuntimeException $e) {
            return 'Gagal menyimpan: ' . $e->getMessage();
        }
    }

    private function normalize(string $text): string
    {
        return trim(preg_replace('/[^a-z0-9\s]/', '', mb_strtolower($text)));
    }

    private function tanyaDataTool(): array
    {
        return [
            'name' => 'tanya_data',
            'description' => 'Jawab PERTANYAAN tentang data yang sudah ada (melihat, mencari, menghitung). '
                . 'Gunakan untuk semua pertanyaan informasi. Tidak mengubah data.',
            'input_schema' => [
                'type' => 'object',
                'properties' => [
                    'pertanyaan' => ['type' => 'string', 'description' => 'Pertanyaan user apa adanya.'],
                ],
                'required' => ['pertanyaan'],
            ],
        ];
    }

    private function systemPrompt(): string
    {
        $today = now()->toDateString();

        return <<<PROMPT
Hari ini {$today}. Kamu asisten data keuangan aplikasi "strack" lewat Telegram, berbahasa Indonesia.
Pilih SATU tindakan paling tepat untuk pesan user:
- Jika user BERTANYA / ingin MELIHAT / MENGHITUNG data yang sudah ada -> panggil tool `tanya_data`
  (salin pertanyaannya apa adanya).
- Jika user ingin MENCATAT atau MENGUBAH data -> panggil tool tulis yang sesuai dan ekstrak datanya.

Aturan:
- Untuk perintah tulis, JANGAN mengarang nilai. Bila ada data WAJIB yang belum jelas (mis. nominal),
  JANGAN panggil tool; balas singkat menanyakan data yang kurang.
- Konversi waktu ke tanggal Y-m-d: "hari ini"={$today}; "kemarin"=sehari sebelumnya.
- Ubah nominal ke angka bulat: "50rb"->50000, "1,5jt"->1500000, "2 juta"->2000000.
- Jangan menjawab pertanyaan data dari pengetahuanmu sendiri; selalu lewat `tanya_data`.
PROMPT;
    }
}
