<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien tipis untuk Anthropic Messages API (tanpa SDK, pakai Http facade).
 * Dipakai bot Telegram Text-to-SQL. Mendukung prompt caching pada system prompt
 * agar konteks skema DB yang panjang tidak dihitung penuh tiap panggilan.
 */
class AnthropicClient
{
    /**
     * Kirim satu giliran percakapan, kembalikan teks jawaban model.
     *
     * @param  array<int, array{role: string, content: mixed}>  $messages
     * @param  array{system?: string, model?: string, max_tokens?: int, cache_system?: bool}  $options
     */
    public function chat(array $messages, array $options = []): string
    {
        $config = config('services.anthropic');

        if (empty($config['api_key'])) {
            throw new RuntimeException('ANTHROPIC_API_KEY belum diisi di .env.');
        }

        $payload = [
            'model'      => $options['model'] ?? $config['model'],
            'max_tokens' => $options['max_tokens'] ?? 1024,
            'messages'   => $messages,
        ];

        if (! empty($options['system'])) {
            // Blok system sebagai array agar bisa dipasang cache_control.
            $systemBlock = ['type' => 'text', 'text' => $options['system']];

            if ($options['cache_system'] ?? false) {
                $systemBlock['cache_control'] = ['type' => 'ephemeral'];
            }

            $payload['system'] = [$systemBlock];
        }

        $response = Http::withHeaders([
            'x-api-key'         => $config['api_key'],
            'anthropic-version' => $config['version'],
            'content-type'      => 'application/json',
        ])
            ->timeout(60)
            ->retry(2, 500, throw: false)
            ->post(rtrim($config['base_url'], '/') . '/v1/messages', $payload);

        if ($response->failed()) {
            $detail = $response->json('error.message') ?? $response->body();
            throw new RuntimeException('Anthropic API error: ' . $detail);
        }

        // Gabungkan semua blok teks pada content.
        $text = collect($response->json('content', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode('');

        return trim($text);
    }
}
