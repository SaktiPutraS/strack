<?php

namespace App\Services\Ai;

use App\Services\Ai\Contracts\AiProvider;
use App\Services\Ai\Providers\AnthropicProvider;
use App\Services\Ai\Providers\GeminiProvider;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

/**
 * Gerbang AI: coba provider sesuai urutan (default Gemini dulu), dan bila
 * sebuah provider GAGAL/tak merespons, jatuh ke provider berikutnya (Claude).
 * Provider tanpa kredensial dilewati diam-diam.
 */
class AiGateway
{
    /** @var array<int, AiProvider> */
    private array $providers;

    /** Nama provider yang berhasil menjawab pada panggilan terakhir. */
    private ?string $lastProvider = null;

    public function __construct(GeminiProvider $gemini, AnthropicProvider $anthropic)
    {
        $this->providers = config('services.ai.primary') === 'anthropic'
            ? [$anthropic, $gemini]
            : [$gemini, $anthropic];
    }

    /** Nama provider yang menjawab panggilan terakhir (gemini/anthropic), atau null. */
    public function lastProvider(): ?string
    {
        return $this->lastProvider;
    }

    /** Kosongkan pelacakan provider (dipanggil di awal tiap pesan). */
    public function resetProviderTracking(): void
    {
        $this->lastProvider = null;
    }

    /** Klasifikasi/tool use. Sistem prompt stabil -> di-cache (khusus Claude). */
    public function classify(string $system, array $messages, array $tools, int $maxTokens = 1024): AiResult
    {
        return $this->generate([
            'system' => $system,
            'messages' => $messages,
            'tools' => $tools,
            'max_tokens' => $maxTokens,
            'cache_system' => true,
        ]);
    }

    /** Hasil teks murni (tanpa tool). */
    public function text(string $system, array $messages, int $maxTokens = 800, bool $cacheSystem = false): string
    {
        return $this->generate([
            'system' => $system,
            'messages' => $messages,
            'max_tokens' => $maxTokens,
            'cache_system' => $cacheSystem,
        ])->text;
    }

    private function generate(array $req): AiResult
    {
        $lastError = null;

        foreach ($this->providers as $provider) {
            if (! $provider->isConfigured()) {
                continue;
            }

            try {
                $result = $provider->generate($req);
                $this->lastProvider = $provider->name();
                return $result;
            } catch (Throwable $e) {
                $lastError = $e;
                Log::warning("AI provider {$provider->name()} gagal, coba cadangan", [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        throw new RuntimeException(
            'Semua provider AI gagal' . ($lastError ? ': ' . $lastError->getMessage() : '.')
        );
    }
}
