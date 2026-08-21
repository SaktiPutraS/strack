<?php

namespace App\Services\Ai;

use App\Services\Ai\Actions\ActionRegistry;
use App\Services\Ai\Actions\NotACorrectionException;
use App\Services\Ai\Actions\WriteAction;
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

    /** Riwayat percakapan singkat sebagai konteks (mis. rujukan "tadi/tersebut"). */
    private const MAX_HISTORY = 6;
    private const HISTORY_TTL_MINUTES = 30;

    public function __construct(
        private AiGateway $ai,
        private TextToSqlService $textToSql,
        private ActionRegistry $registry,
        private ReceiptParser $receipts,
        private TransferProofParser $proofs,
    ) {}

    public function handle(int|string $chatId, string $text): string
    {
        $this->ai->resetProviderTracking();

        $text = trim($text);
        $normalized = $this->normalize($text);
        $pendingKey = 'tg_pending:' . $chatId;
        $pending = Cache::get($pendingKey);

        // 1. Ada aksi menunggu konfirmasi?
        if ($pending) {
            if (in_array($normalized, self::AFFIRM, true)) {
                Cache::forget($pendingKey);
                return $this->finish($chatId, $text, $this->runPending($pending));
            }

            if (in_array($normalized, self::DENY, true)) {
                Cache::forget($pendingKey);
                return $this->finish($chatId, $text, 'Baik, dibatalkan.');
            }

            // Aksi yang bisa dikoreksi (mis. rekap struk): balasan bebas
            // diperlakukan sebagai koreksi selama memang menyoal rekap itu.
            $pendingAction = $this->registry->find($pending['action']);

            if ($pendingAction && $pendingAction->supportsRefine()) {
                try {
                    $prepared = $pendingAction->refine($pending['prepared'], $text);

                    $this->putPending($pendingKey, $pendingAction, $prepared);

                    return $this->finish($chatId, $text, $pendingAction->preview($prepared));
                } catch (NotACorrectionException) {
                    // Bukan koreksi -> lanjut sebagai permintaan baru di bawah.
                } catch (RuntimeException $e) {
                    // Koreksi belum jelas: pertahankan yang tertunda, minta diperjelas.
                    return $this->finish($chatId, $text, $e->getMessage());
                }
            }

            // Selain ya/tidak -> anggap permintaan baru, buang yang tertunda.
            Cache::forget($pendingKey);
        }

        // 2. Klasifikasi via AI + tool use (dengan riwayat singkat sebagai konteks).
        $history = $this->loadHistory($chatId);

        $result = $this->ai->classify(
            $this->systemPrompt(),
            array_merge($history, [['role' => 'user', 'content' => $text]]),
            array_merge([$this->tanyaDataTool()], $this->registry->toolDefinitions()),
        );

        $tool = $result->tool;

        // Tidak memanggil tool -> AI menjawab/menanyakan sesuatu langsung.
        if (! $tool) {
            $reply = $result->text;
            return $this->finish($chatId, $text, $reply !== '' ? $reply : 'Maaf, saya belum paham. Coba jelaskan lagi.');
        }

        // Baca data.
        if ($tool['name'] === 'tanya_data') {
            $pertanyaan = $tool['input']['pertanyaan'] ?? $text;
            return $this->finish($chatId, $text, $this->textToSql->ask($pertanyaan));
        }

        // Aksi tulis -> siapkan + minta konfirmasi.
        $action = $this->registry->find($tool['name']);
        if (! $action) {
            return $this->finish($chatId, $text, 'Maaf, aksi itu belum tersedia.');
        }

        try {
            $prepared = $action->prepare($tool['input']);
        } catch (RuntimeException $e) {
            return $this->finish($chatId, $text, $e->getMessage());
        }

        $this->putPending($pendingKey, $action, $prepared);

        return $this->finish($chatId, $text, $action->preview($prepared));
    }

    /**
     * Gambar yang dikirim ke bot: dipilah dulu jadi BUKTI TRANSFER atau STRUK
     * belanja, lalu diteruskan ke alur masing-masing. Gambar yang tidak jelas
     * jenisnya tetap dicoba dibaca sebagai struk (perilaku lama).
     */
    public function handleImage(int|string $chatId, string $binary, string $mime, string $caption = ''): string
    {
        $this->ai->resetProviderTracking();

        Cache::forget('tg_pending:' . $chatId);

        try {
            $proof = $this->proofs->parse($binary, $mime, $caption);
        } catch (RuntimeException $e) {
            $label = trim($caption) !== '' ? "[gambar] {$caption}" : '[gambar]';

            return $this->finish($chatId, $label, $e->getMessage());
        }

        if ($proof['jenis'] === TransferProofParser::KIND_TRANSFER) {
            return $this->handleTransferProof($chatId, $proof, $caption);
        }

        return $this->handleReceipt($chatId, $binary, $mime, $caption);
    }

    /**
     * Bukti transfer: nominalnya dicocokkan dengan seluruh pembayaran yang
     * belum ditransfer. Pas -> minta konfirmasi; tidak pas -> tolak + laporkan
     * selisihnya (pencocokan sebagian dilakukan manual lewat aplikasi).
     */
    private function handleTransferProof(int|string $chatId, array $proof, string $caption): string
    {
        $pendingKey = 'tg_pending:' . $chatId;
        $label = trim($caption) !== '' ? "[bukti transfer] {$caption}" : '[bukti transfer]';

        $action = $this->registry->find('catat_transfer_bukti');
        if (! $action) {
            return 'Maaf, pembacaan bukti transfer belum tersedia.';
        }

        try {
            $prepared = $action->prepare($proof);
        } catch (RuntimeException $e) {
            return $this->finish($chatId, $label, $e->getMessage());
        }

        $this->putPending($pendingKey, $action, $prepared);

        return $this->finish($chatId, $label, $action->preview($prepared));
    }

    /**
     * Foto struk belanja: baca lewat AI, kelompokkan per kategori, lalu minta
     * konfirmasi seperti aksi tulis lain. Struk baru menggantikan yang tertunda.
     */
    private function handleReceipt(int|string $chatId, string $binary, string $mime, string $caption = ''): string
    {
        $pendingKey = 'tg_pending:' . $chatId;

        $action = $this->registry->find('catat_struk');
        if (! $action) {
            return 'Maaf, pembacaan struk belum tersedia.';
        }

        $label = trim($caption) !== '' ? "[struk] {$caption}" : '[struk]';

        try {
            $parsed = $this->receipts->parse($binary, $mime, $caption);
            $prepared = $action->prepare($parsed + ['hint' => $caption]);
        } catch (RuntimeException $e) {
            return $this->finish($chatId, $label, $e->getMessage());
        }

        $this->putPending($pendingKey, $action, $prepared);

        return $this->finish($chatId, $label, $action->preview($prepared));
    }

    /** Simpan aksi yang menunggu konfirmasi (masa berlaku ditentukan aksinya). */
    private function putPending(string $key, WriteAction $action, array $prepared): void
    {
        Cache::put($key, [
            'action' => $action->name(),
            'prepared' => $prepared,
        ], now()->addMinutes($action->pendingTtlMinutes()));
    }

    /** Simpan giliran ke riwayat lalu kembalikan balasan (diberi penanda AI). */
    private function finish(int|string $chatId, string $userText, string $reply): string
    {
        $history = $this->loadHistory($chatId);
        $history[] = ['role' => 'user', 'content' => mb_substr($userText, 0, 1500)];
        $history[] = ['role' => 'assistant', 'content' => mb_substr($reply, 0, 1500)];

        // Simpan hanya beberapa giliran terakhir (tanpa ikon, agar konteks bersih).
        $history = array_slice($history, -self::MAX_HISTORY);

        Cache::put('tg_history:' . $chatId, $history, now()->addMinutes(self::HISTORY_TTL_MINUTES));

        // Penanda AI penjawab: biru = Gemini (gratis), oranye = Claude (cadangan).
        // Hanya ditampilkan bila ada panggilan AI di giliran ini.
        $provider = $this->ai->lastProvider();
        if ($provider !== null) {
            $reply = ($provider === 'anthropic' ? '🟠' : '🔵') . ' ' . $reply;
        }

        return $reply;
    }

    /** @return array<int, array{role: string, content: string}> */
    private function loadHistory(int|string $chatId): array
    {
        return Cache::get('tg_history:' . $chatId, []);
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
- Data OPSIONAL (mis. tanggal, referensi, metode) JANGAN ditanyakan - biarkan kosong / pakai default.
- Untuk transfer bank: bila user tidak menyebut proyek tertentu (mis. "transfer semua yang belum
  ditransfer"), panggil `catat_transfer_bank` dengan proyek dikosongkan.
- Konversi waktu ke tanggal Y-m-d: "hari ini"={$today}; "kemarin"=sehari sebelumnya.
- Ubah nominal ke angka bulat: "50rb"->50000, "1,5jt"->1500000, "2 juta"->2000000.
- Jangan menjawab pertanyaan data dari pengetahuanmu sendiri; selalu lewat `tanya_data`.
PROMPT;
    }
}
