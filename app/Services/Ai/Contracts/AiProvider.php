<?php

namespace App\Services\Ai\Contracts;

use App\Services\Ai\AiResult;

/**
 * Satu penyedia model AI (Gemini, Anthropic, dll). Menerima permintaan
 * ternormalisasi dan mengembalikan AiResult ternormalisasi.
 */
interface AiProvider
{
    public function name(): string;

    /** Apakah kredensial provider ini terisi (kalau tidak, gateway melewatinya). */
    public function isConfigured(): bool;

    /**
     * Isi tiap pesan boleh berupa string, atau daftar bagian untuk pesan
     * bergambar: ['type' => 'text', 'text' => ...] dan
     * ['type' => 'image', 'mime' => 'image/jpeg', 'data' => <base64>].
     *
     * @param  array{system?: string, messages: array<int, array{role: string, content: string|array}>, tools?: array, max_tokens?: int, cache_system?: bool}  $req
     */
    public function generate(array $req): AiResult;
}
