<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiResult;
use App\Services\Ai\AnthropicClient;
use App\Services\Ai\Contracts\AiProvider;

/**
 * Provider Claude (Anthropic). Membungkus AnthropicClient ke antarmuka umum.
 */
class AnthropicProvider implements AiProvider
{
    public function __construct(private AnthropicClient $client) {}

    public function name(): string
    {
        return 'anthropic';
    }

    public function isConfigured(): bool
    {
        return ! empty(config('services.anthropic.api_key'));
    }

    public function generate(array $req): AiResult
    {
        $payload = [
            'messages'   => $this->normalizeMessages($req['messages']),
            'max_tokens' => $req['max_tokens'] ?? 1024,
        ];

        if (! empty($req['system'])) {
            $block = ['type' => 'text', 'text' => $req['system']];
            if (! empty($req['cache_system'])) {
                $block['cache_control'] = ['type' => 'ephemeral'];
            }
            $payload['system'] = [$block];
        }

        if (! empty($req['tools'])) {
            $payload['tools'] = $req['tools'];
            $payload['tool_choice'] = ['type' => 'auto'];
        }

        $response = $this->client->raw($payload);

        return new AiResult(
            AnthropicClient::extractText($response),
            AnthropicClient::extractToolUse($response),
        );
    }

    /**
     * Terjemahkan pesan ternormalisasi ke bentuk Anthropic. Isi pesan boleh
     * berupa teks biasa, atau daftar bagian (text/image) untuk pesan bergambar.
     */
    private function normalizeMessages(array $messages): array
    {
        return array_map(function (array $message) {
            if (! is_array($message['content'])) {
                return $message;
            }

            $parts = [];
            foreach ($message['content'] as $part) {
                $parts[] = ($part['type'] ?? 'text') === 'image'
                    ? [
                        'type' => 'image',
                        'source' => [
                            'type'       => 'base64',
                            'media_type' => $part['mime'] ?? 'image/jpeg',
                            'data'       => $part['data'] ?? '',
                        ],
                    ]
                    : ['type' => 'text', 'text' => (string) ($part['text'] ?? '')];
            }

            return ['role' => $message['role'], 'content' => $parts];
        }, $messages);
    }
}
