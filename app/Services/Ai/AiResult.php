<?php

namespace App\Services\Ai;

/**
 * Hasil ternormalisasi dari sebuah provider AI, apa pun penyedianya.
 */
class AiResult
{
    /**
     * @param  string  $text  teks jawaban (bila ada)
     * @param  array{name: string, input: array}|null  $tool  pemanggilan tool (bila ada)
     */
    public function __construct(
        public string $text = '',
        public ?array $tool = null,
    ) {}
}
