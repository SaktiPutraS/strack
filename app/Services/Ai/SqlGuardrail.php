<?php

namespace App\Services\Ai;

use RuntimeException;

/**
 * Pertahanan aplikasi (lapis kedua setelah user MySQL read-only): memastikan SQL
 * dari AI benar-benar hanya SELECT tunggal yang aman sebelum dijalankan.
 */
class SqlGuardrail
{
    /** Kata kunci yang menandakan operasi tulis / berbahaya. */
    private const FORBIDDEN = [
        'INSERT', 'UPDATE', 'DELETE', 'DROP', 'ALTER', 'TRUNCATE', 'CREATE',
        'REPLACE', 'GRANT', 'REVOKE', 'RENAME', 'MERGE', 'CALL', 'EXEC', 'EXECUTE',
        'LOCK', 'UNLOCK', 'SET', 'HANDLER', 'LOAD', 'OUTFILE', 'DUMPFILE',
        'INFILE', 'INTO', 'SLEEP', 'BENCHMARK', 'INFORMATION_SCHEMA',
    ];

    private const DEFAULT_LIMIT = 200;

    /**
     * Validasi + normalisasi. Mengembalikan SQL siap jalan atau melempar exception.
     */
    public function sanitize(string $sql): string
    {
        $sql = trim($sql);

        // Buang pembungkus markdown ```sql ... ``` bila model menyertakannya.
        $sql = preg_replace('/^```(?:sql)?\s*|\s*```$/i', '', $sql);
        $sql = trim($sql);

        // Buang titik koma di akhir.
        $sql = rtrim($sql, "; \t\n\r");

        if ($sql === '') {
            throw new RuntimeException('SQL kosong.');
        }

        // Tolak multiple statement (titik koma di tengah).
        if (str_contains($sql, ';')) {
            throw new RuntimeException('Hanya satu perintah SELECT yang diizinkan.');
        }

        // Tolak komentar SQL (bisa dipakai menyelundupkan perintah).
        if (preg_match('/(--|#|\/\*)/', $sql)) {
            throw new RuntimeException('Komentar SQL tidak diizinkan.');
        }

        // Harus diawali SELECT atau WITH (CTE yang berujung SELECT).
        if (! preg_match('/^\s*(SELECT|WITH)\b/i', $sql)) {
            throw new RuntimeException('Hanya query SELECT yang diizinkan.');
        }

        // Blokir kata kunci berbahaya (word-boundary, case-insensitive).
        foreach (self::FORBIDDEN as $keyword) {
            if (preg_match('/\b' . preg_quote($keyword, '/') . '\b/i', $sql)) {
                throw new RuntimeException("Kata kunci terlarang terdeteksi: {$keyword}.");
            }
        }

        // Paksa LIMIT bila belum ada.
        if (! preg_match('/\bLIMIT\b/i', $sql)) {
            $sql .= ' LIMIT ' . self::DEFAULT_LIMIT;
        }

        return $sql;
    }
}
