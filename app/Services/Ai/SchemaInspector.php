<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Membaca struktur tabel dari information_schema lalu menyusunnya jadi teks
 * ringkas untuk konteks AI. Dengan begitu skema tidak perlu ditulis manual dan
 * otomatis ikut berubah bila kolom bertambah. Hasil di-cache 1 jam.
 */
class SchemaInspector
{
    /** Tabel sistem Laravel yang tidak relevan untuk pertanyaan data. */
    private const IGNORED_TABLES = [
        'migrations', 'sessions', 'password_reset_tokens', 'password_resets',
        'personal_access_tokens', 'cache', 'cache_locks', 'jobs', 'job_batches',
        'failed_jobs', 'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring',
    ];

    private const CACHE_KEY = 'telegram_bot_db_schema';

    public function schemaText(bool $fresh = false): string
    {
        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, now()->addHour(), function () {
            return $this->buildSchemaText();
        });
    }

    private function buildSchemaText(): string
    {
        $connection = DB::connection('mysql_ro');
        $database = $connection->getDatabaseName();

        $columns = $connection->select(
            'SELECT TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, COLUMN_COMMENT
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ?
             ORDER BY TABLE_NAME, ORDINAL_POSITION',
            [$database]
        );

        $tables = [];
        foreach ($columns as $col) {
            if (in_array($col->TABLE_NAME, self::IGNORED_TABLES, true)) {
                continue;
            }

            $line = $col->COLUMN_NAME . ' ' . $col->COLUMN_TYPE;
            if (! empty($col->COLUMN_COMMENT)) {
                $line .= ' -- ' . $col->COLUMN_COMMENT;
            }

            $tables[$col->TABLE_NAME][] = $line;
        }

        $out = [];
        foreach ($tables as $table => $cols) {
            $out[] = "TABLE {$table} (";
            $out[] = '  ' . implode(",\n  ", $cols);
            $out[] = ')';
            $out[] = '';
        }

        return trim(implode("\n", $out));
    }
}
