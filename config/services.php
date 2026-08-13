<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Anthropic API (dipakai bot Telegram Text-to-SQL). Prabayar per token,
    // TERPISAH dari langganan Claude Pro. Model default Haiku (murah).
    'anthropic' => [
        'api_key'        => env('ANTHROPIC_API_KEY'),
        'model'          => env('ANTHROPIC_MODEL', 'claude-haiku-4-5'),
        'model_fallback' => env('ANTHROPIC_MODEL_FALLBACK', 'claude-sonnet-4-6'),
        'base_url'       => env('ANTHROPIC_BASE_URL', 'https://api.anthropic.com'),
        'version'        => env('ANTHROPIC_VERSION', '2023-06-01'),
    ],

    // Google Gemini API (Google AI Studio). Tier gratis. Dipakai sebagai AI
    // PRIMER bot (hemat biaya); Claude jadi cadangan bila Gemini gagal.
    'gemini' => [
        'api_key'  => env('GEMINI_API_KEY'),
        'model'    => env('GEMINI_MODEL', 'gemini-2.0-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
    ],

    // Urutan provider AI bot. 'gemini' = coba Gemini dulu, gagal -> Claude.
    // 'anthropic' = Claude saja / Claude dulu.
    'ai' => [
        'primary' => env('AI_PRIMARY', 'gemini'),
    ],

    // Groq API (transkripsi voice note bot Telegram). Tier gratis, endpoint
    // kompatibel OpenAI (Whisper). TERPISAH dari Anthropic.
    'groq' => [
        'api_key'   => env('GROQ_API_KEY'),
        'stt_model' => env('GROQ_STT_MODEL', 'whisper-large-v3'),
        'base_url'  => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    ],

    // Bot Telegram tanya-jawab data strack. Keamanan: secret webhook +
    // whitelist chat_id (hanya id yang terdaftar boleh bertanya).
    'telegram' => [
        'bot_token'       => env('TELEGRAM_BOT_TOKEN'),
        'webhook_secret'  => env('TELEGRAM_WEBHOOK_SECRET'),
        'allowed_chat_ids' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('TELEGRAM_ALLOWED_CHAT_IDS', ''))
        ), fn ($id) => $id !== '')),
    ],

];
