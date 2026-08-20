<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiResult;
use App\Services\Ai\Contracts\AiProvider;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Provider Google Gemini (Google AI Studio, tier gratis). Menerjemahkan
 * permintaan/format tool ke skema Gemini dan menormalkan responsnya.
 */
class GeminiProvider implements AiProvider
{
    public function name(): string
    {
        return 'gemini';
    }

    public function isConfigured(): bool
    {
        return ! empty(config('services.gemini.api_key'));
    }

    public function generate(array $req): AiResult
    {
        $config = config('services.gemini');

        if (empty($config['api_key'])) {
            throw new RuntimeException('GEMINI_API_KEY belum diisi.');
        }

        $contents = [];
        foreach ($req['messages'] as $m) {
            $contents[] = [
                'role'  => ($m['role'] === 'assistant') ? 'model' : 'user',
                'parts' => $this->toGeminiParts($m['content']),
            ];
        }

        $body = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => $req['max_tokens'] ?? 1024,
                'temperature' => 0,
            ],
        ];

        if (! empty($req['system'])) {
            $body['systemInstruction'] = ['parts' => [['text' => $req['system']]]];
        }

        if (! empty($req['tools'])) {
            $body['tools'] = [['functionDeclarations' => array_map(
                fn ($t) => [
                    'name' => $t['name'],
                    'description' => $t['description'] ?? '',
                    'parameters' => $this->toGeminiSchema($t['input_schema'] ?? ['type' => 'object']),
                ],
                $req['tools']
            )]];
            $body['toolConfig'] = ['functionCallingConfig' => ['mode' => 'AUTO']];
        }

        $url = rtrim($config['base_url'], '/') . '/models/' . $config['model'] . ':generateContent';

        $response = Http::timeout(60)
            ->withHeaders(['X-goog-api-key' => $config['api_key']])
            ->post($url, $body);

        if ($response->failed()) {
            $detail = $response->json('error.message') ?? $response->body();
            throw new RuntimeException('Gemini error: ' . $detail);
        }

        $text = '';
        $tool = null;
        foreach ($response->json('candidates.0.content.parts', []) as $part) {
            if (isset($part['text'])) {
                $text .= $part['text'];
            }
            if (isset($part['functionCall'])) {
                $tool = [
                    'name' => $part['functionCall']['name'],
                    'input' => $part['functionCall']['args'] ?? [],
                ];
            }
        }

        return new AiResult(trim($text), $tool);
    }

    /**
     * Isi pesan -> parts Gemini. Teks biasa jadi satu part; daftar bagian
     * (text/image) diterjemahkan satu per satu, gambar lewat inline_data.
     */
    private function toGeminiParts(mixed $content): array
    {
        if (! is_array($content)) {
            return [['text' => (string) $content]];
        }

        $parts = [];
        foreach ($content as $part) {
            $parts[] = ($part['type'] ?? 'text') === 'image'
                ? ['inline_data' => [
                    'mime_type' => $part['mime'] ?? 'image/jpeg',
                    'data'      => $part['data'] ?? '',
                ]]
                : ['text' => (string) ($part['text'] ?? '')];
        }

        return $parts;
    }

    /**
     * Konversi JSON Schema (gaya Anthropic input_schema) ke skema Gemini:
     * nilai "type" di-UPPERCASE (STRING/OBJECT/...), sisanya diteruskan.
     */
    private function toGeminiSchema(array $schema): array
    {
        $out = [];
        foreach ($schema as $key => $value) {
            if ($key === 'type' && is_string($value)) {
                $out['type'] = strtoupper($value);
            } elseif ($key === 'properties' && is_array($value)) {
                $out['properties'] = [];
                foreach ($value as $propKey => $propSchema) {
                    $out['properties'][$propKey] = $this->toGeminiSchema($propSchema);
                }
            } elseif ($key === 'items' && is_array($value)) {
                $out['items'] = $this->toGeminiSchema($value);
            } else {
                $out[$key] = $value;
            }
        }

        return $out;
    }
}
