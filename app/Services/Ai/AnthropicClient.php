<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Klien tipis untuk Anthropic Messages API (tanpa SDK, pakai Http facade).
 * Dipakai bot Telegram: Text-to-SQL (baca) + tool use (tulis).
 */
class AnthropicClient
{
    /**
     * Panggilan mentah ke Messages API, kembalikan seluruh respons ter-decode.
     * Payload bebas (messages, system, tools, tool_choice, dll). Model &
     * max_tokens diisi default dari config bila belum ada.
     */
    public function raw(array $payload): array
    {
        $config = config('services.anthropic');

        if (empty($config['api_key'])) {
            throw new RuntimeException('ANTHROPIC_API_KEY belum diisi di .env.');
        }

        $payload['model'] ??= $config['model'];
        $payload['max_tokens'] ??= 1024;

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

        return $response->json();
    }

    /**
     * Kirim satu giliran percakapan, kembalikan teks jawaban model.
     *
     * @param  array<int, array{role: string, content: mixed}>  $messages
     * @param  array{system?: string, model?: string, max_tokens?: int, cache_system?: bool}  $options
     */
    public function chat(array $messages, array $options = []): string
    {
        $payload = [
            'messages'   => $messages,
            'max_tokens' => $options['max_tokens'] ?? 1024,
        ];

        if (! empty($options['model'])) {
            $payload['model'] = $options['model'];
        }

        if (! empty($options['system'])) {
            $systemBlock = ['type' => 'text', 'text' => $options['system']];

            if ($options['cache_system'] ?? false) {
                $systemBlock['cache_control'] = ['type' => 'ephemeral'];
            }

            $payload['system'] = [$systemBlock];
        }

        return static::extractText($this->raw($payload));
    }

    /** Gabungkan semua blok teks pada content respons. */
    public static function extractText(array $response): string
    {
        $text = '';
        foreach ($response['content'] ?? [] as $block) {
            if (($block['type'] ?? null) === 'text') {
                $text .= $block['text'];
            }
        }

        return trim($text);
    }

    /**
     * Ambil blok tool_use pertama (jika ada) dari respons.
     *
     * @return array{name: string, input: array}|null
     */
    public static function extractToolUse(array $response): ?array
    {
        foreach ($response['content'] ?? [] as $block) {
            if (($block['type'] ?? null) === 'tool_use') {
                return ['name' => $block['name'], 'input' => $block['input'] ?? []];
            }
        }

        return null;
    }
}
