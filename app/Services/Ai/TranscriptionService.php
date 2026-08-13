<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Transkripsi audio (voice note) jadi teks lewat Groq (Whisper), endpoint
 * kompatibel OpenAI. Tanpa SDK, pakai Http facade.
 */
class TranscriptionService
{
    /**
     * @param  string  $audio     isi biner file audio
     * @param  string  $filename  nama file dengan ekstensi benar (mis. voice.ogg)
     */
    public function transcribe(string $audio, string $filename = 'voice.ogg'): string
    {
        $config = config('services.groq');

        if (empty($config['api_key'])) {
            throw new RuntimeException('GROQ_API_KEY belum diisi di .env.');
        }

        $response = Http::withToken($config['api_key'])
            ->timeout(60)
            ->attach('file', $audio, $filename)
            ->post(rtrim($config['base_url'], '/') . '/audio/transcriptions', [
                'model'           => $config['stt_model'],
                'language'        => 'id',
                'temperature'     => '0',
                'response_format' => 'json',
            ]);

        if ($response->failed()) {
            $detail = $response->json('error.message') ?? $response->body();
            throw new RuntimeException('Groq transcription error: ' . $detail);
        }

        return trim((string) $response->json('text', ''));
    }
}
