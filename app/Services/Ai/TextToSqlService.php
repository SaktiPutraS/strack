<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

/**
 * Orkestrasi tanya-jawab data:
 *   pertanyaan -> AI membuat SQL SELECT -> guardrail -> jalankan (read-only)
 *   -> AI merangkai jawaban natural Bahasa Indonesia dari hasilnya.
 */
class TextToSqlService
{
    public function __construct(
        private AnthropicClient $ai,
        private SchemaInspector $schema,
        private SqlGuardrail $guardrail,
    ) {}

    public function ask(string $question): string
    {
        $sql = $this->generateSql($question);
        $rows = $this->runQuery($sql);

        return $this->summarize($question, $sql, $rows);
    }

    /** Minta AI menuliskan satu query SELECT dari pertanyaan. */
    private function generateSql(string $question): string
    {
        $system = <<<PROMPT
Kamu asisten yang mengubah pertanyaan Bahasa Indonesia menjadi SATU query MySQL SELECT
untuk aplikasi keuangan bernama "strack". Balas HANYA dengan query SQL mentah, tanpa
penjelasan, tanpa markdown, tanpa titik koma.

ATURAN WAJIB:
- Hanya SELECT (boleh diawali WITH untuk CTE). DILARANG menulis/mengubah data.
- Selalu sertakan LIMIT yang wajar (maksimal 200).
- Gunakan HANYA tabel dan kolom yang ada pada skema di bawah.
- Nilai uang dalam rupiah (angka bulat). Status proyek: LEAD, WAITING, PROGRESS, FINISHED, CANCELLED.
- Jika pertanyaan tidak bisa dijawab dari skema, balas persis: TIDAK_BISA

SKEMA DATABASE:
{$this->schema->schemaText()}
PROMPT;

        $raw = $this->ai->chat(
            [['role' => 'user', 'content' => $question]],
            ['system' => $system, 'cache_system' => true, 'max_tokens' => 700]
        );

        if (str_contains(strtoupper($raw), 'TIDAK_BISA')) {
            throw new RuntimeException('__TIDAK_BISA__');
        }

        return $this->guardrail->sanitize($raw);
    }

    /** Jalankan query pada koneksi read-only dengan timeout singkat. */
    private function runQuery(string $sql): array
    {
        $connection = DB::connection('mysql_ro');

        try {
            // Timeout query 8 detik agar tidak menggantung webhook.
            $connection->statement('SET SESSION max_execution_time = 8000');
        } catch (Throwable) {
            // Sebagian versi/hosting tak mengizinkan; abaikan, ada LIMIT sbagai pengaman.
        }

        return $connection->select($sql);
    }

    /** Minta AI merangkai jawaban natural dari hasil query. */
    private function summarize(string $question, string $sql, array $rows): string
    {
        $data = json_encode($rows, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Batasi ukuran data yang dikirim balik ke model.
        if (strlen($data) > 12000) {
            $data = substr($data, 0, 12000) . "\n... (data dipotong)";
        }

        $system = <<<PROMPT
Kamu menjawab pertanyaan pengguna tentang data keuangan strack berdasarkan HASIL QUERY.
Jawab dalam Bahasa Indonesia yang ringkas dan jelas. Format angka rupiah dengan pemisah
ribuan (mis. Rp1.500.000). Jika hasil kosong, katakan datanya tidak ditemukan. Jangan
menyebut SQL atau istilah teknis. Jangan mengarang angka di luar hasil query.
PROMPT;

        $user = "Pertanyaan: {$question}\n\nHASIL QUERY (JSON):\n{$data}";

        return $this->ai->chat(
            [['role' => 'user', 'content' => $user]],
            ['system' => $system, 'max_tokens' => 800]
        );
    }
}
