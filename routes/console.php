<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pengingat domain akan habis -> Telegram (butuh cron `schedule:run` di hosting).
Schedule::command('domains:remind')->dailyAt('08:00');

// Isi kalender hari ini (agenda + deadline proyek + domain + maintenance +
// jatuh tempo) -> Telegram. Todo TIDAK ikut. Hari yang kosong tidak dikirim.
Schedule::command('calendar:remind')->dailyAt('07:00');
