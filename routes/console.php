<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Isi kalender hari ini (agenda + deadline proyek + domain + maintenance +
// jatuh tempo) -> Telegram. Todo TIDAK ikut. Hari yang kosong tidak dikirim.
//
// Pengingat domain (`domains:remind`) SENGAJA tidak dijadwalkan lagi: peringatan
// H-30 sudah masuk pesan ini, jadi jadwal terpisah cuma bikin pesan dobel tiap
// pagi. Command-nya tetap ada untuk dipanggil manual.
Schedule::command('calendar:remind')->dailyAt('07:00');
