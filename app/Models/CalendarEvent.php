<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Agenda & todo pribadi di menu Kalender.
 * Menggantikan CalendarNote (yang hanya sanggup 1 catatan per hari, tanpa jam).
 */
class CalendarEvent extends Model
{
    public const TYPE_EVENT = 'EVENT';
    public const TYPE_TODO = 'TODO';

    /** Warna pilihan di form (label -> hex). */
    public const COLORS = [
        '#8B5CF6' => 'Ungu',
        '#3B82F6' => 'Biru',
        '#10B981' => 'Hijau',
        '#F59E0B' => 'Kuning',
        '#EF4444' => 'Merah',
        '#EC4899' => 'Merah muda',
        '#6B7280' => 'Abu-abu',
    ];

    public const DEFAULT_COLOR = '#8B5CF6';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'all_day',
        'color',
        'is_done',
        'completed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'all_day' => 'boolean',
        'is_done' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Scope ───────────────────────────────────────────────────────────────
    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Acara yang bersinggungan dengan rentang tanggal (inklusif di kedua ujung).
     * Acara tanpa end_date dianggap berdurasi satu hari.
     */
    public function scopeInRange(Builder $query, string $from, string $to): Builder
    {
        return $query->where('start_date', '<=', $to)
            ->whereRaw('COALESCE(end_date, start_date) >= ?', [$from]);
    }

    public function scopeTodo(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TODO);
    }

    // ── Accessor ────────────────────────────────────────────────────────────
    public function getIsTodoAttribute(): bool
    {
        return $this->type === self::TYPE_TODO;
    }

    public function getDisplayColorAttribute(): string
    {
        return $this->color ?: self::DEFAULT_COLOR;
    }

    /** Tanggal akhir efektif (fallback ke tanggal mulai). */
    public function getEffectiveEndDateAttribute(): Carbon
    {
        return $this->end_date ?: $this->start_date;
    }

    /** Label jam untuk ditampilkan, mis. "09:00 - 11:30" atau "Seharian". */
    public function getTimeLabelAttribute(): string
    {
        if ($this->all_day || ! $this->start_time) {
            return 'Seharian';
        }

        $start = substr((string) $this->start_time, 0, 5);

        return $this->end_time
            ? $start . ' - ' . substr((string) $this->end_time, 0, 5)
            : $start;
    }

    // ── Serialisasi ─────────────────────────────────────────────────────────
    /**
     * Bentuk objek event untuk FullCalendar.
     * Catatan: untuk acara "seharian", FullCalendar memakai `end` EKSKLUSIF,
     * jadi tanggal akhir perlu ditambah 1 hari agar sel terakhir ikut terwarnai.
     */
    public function toCalendarPayload(): array
    {
        $start = $this->start_date->format('Y-m-d');
        $end = $this->effective_end_date->format('Y-m-d');

        if ($this->all_day || ! $this->start_time) {
            $payload = [
                'start' => $start,
                'end' => Carbon::parse($end)->addDay()->format('Y-m-d'),
                'allDay' => true,
            ];
        } else {
            $payload = [
                'start' => $start . 'T' . substr((string) $this->start_time, 0, 8),
                'end' => $this->end_time
                    ? $end . 'T' . substr((string) $this->end_time, 0, 8)
                    : null,
                'allDay' => false,
            ];
        }

        $color = $this->is_done ? '#9CA3AF' : $this->display_color;

        return array_merge($payload, [
            'id' => 'own-' . $this->id,
            'title' => $this->title,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'textColor' => '#ffffff',
            'editable' => true,
            'classNames' => $this->is_done ? ['fc-event-done'] : [],
            'extendedProps' => [
                'source' => 'own',
                'recordId' => $this->id,
                'type' => $this->type,
                'isTodo' => $this->is_todo,
                'isDone' => $this->is_done,
                'description' => $this->description,
                'color' => $this->display_color,
                'startDate' => $start,
                'endDate' => $this->end_date?->format('Y-m-d'),
                'startTime' => $this->start_time ? substr((string) $this->start_time, 0, 5) : null,
                'endTime' => $this->end_time ? substr((string) $this->end_time, 0, 5) : null,
                'allDay' => (bool) $this->all_day,
                'timeLabel' => $this->time_label,
            ],
        ]);
    }

    /**
     * Bentuk ringkas untuk kalender di dashboard (dipakai JS lama).
     */
    public function toDashboardArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->start_date->format('Y-m-d'),
            'title' => $this->title,
            'content' => $this->description,
            'type' => $this->type,
            'is_done' => $this->is_done,
            'color' => $this->display_color,
            'time_label' => $this->time_label,
        ];
    }

    // ── Query siap pakai ────────────────────────────────────────────────────
    /**
     * Catatan/agenda satu bulan untuk kalender dashboard (array polos).
     * Acara multi-hari ikut terbawa selama bersinggungan dengan bulan tersebut.
     */
    public static function getEventsForMonth(string $userId, int $year, int $month): array
    {
        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to = $from->copy()->endOfMonth();

        return self::forUser($userId)
            ->inRange($from->format('Y-m-d'), $to->format('Y-m-d'))
            ->orderBy('start_date')
            ->orderByRaw('start_time IS NULL, start_time')
            ->get()
            ->map(fn (self $event) => $event->toDashboardArray())
            ->all();
    }
}
