<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Catatan "sudah dikerjakan" untuk satu tanggal kemunculan agenda/todo berulang.
 *
 * Kolom `is_done` di calendar_events hanya dipakai untuk data sekali jalan.
 * Untuk data berulang, tiap tanggal dicentang sendiri-sendiri lewat tabel ini,
 * supaya todo harian yang beres hari ini tetap muncul lagi besok.
 */
class CalendarEventCompletion extends Model
{
    protected $fillable = [
        'event_id',
        'occurrence_date',
        'completed_at',
    ];

    protected $casts = [
        'occurrence_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class, 'event_id');
    }
}
