<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Agenda & todo pribadi di menu Kalender.
 * Menggantikan CalendarNote (yang hanya sanggup 1 catatan per hari, tanpa jam).
 *
 * Bisa sekali jalan (repeat_type NULL) atau berulang (harian, hari kerja,
 * mingguan, bulanan, tahunan). Data berulang disimpan SATU baris saja; tanggal
 * kemunculannya dihitung saat dibaca lewat occurrencesBetween().
 */
class CalendarEvent extends Model
{
    public const TYPE_EVENT = 'EVENT';
    public const TYPE_TODO = 'TODO';

    // ── Pola pengulangan ────────────────────────────────────────────────────
    public const REPEAT_DAILY = 'DAILY';
    public const REPEAT_WEEKDAY = 'WEEKDAY';
    public const REPEAT_WEEKLY = 'WEEKLY';
    public const REPEAT_MONTHLY = 'MONTHLY';
    public const REPEAT_YEARLY = 'YEARLY';

    public const REPEAT_TYPES = [
        self::REPEAT_DAILY,
        self::REPEAT_WEEKDAY,
        self::REPEAT_WEEKLY,
        self::REPEAT_MONTHLY,
        self::REPEAT_YEARLY,
    ];

    /** Pengaman supaya rentang yang kelewat lebar tidak meledak jadi ribuan baris. */
    public const MAX_OCCURRENCES = 500;

    /** Nama hari, index = dayOfWeek Carbon (0 = Minggu). */
    public const DAY_NAMES = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public const DAY_NAMES_SHORT = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];

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
        'repeat_type',
        'repeat_interval',
        'repeat_days',
        'repeat_day_of_month',
        'repeat_until',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'repeat_until' => 'date',
        'all_day' => 'boolean',
        'is_done' => 'boolean',
        'completed_at' => 'datetime',
        'repeat_interval' => 'integer',
        'repeat_day_of_month' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────────
    /** Centang selesai per tanggal (hanya dipakai data berulang). */
    public function completions(): HasMany
    {
        return $this->hasMany(CalendarEventCompletion::class, 'event_id');
    }

    // ── Scope ───────────────────────────────────────────────────────────────
    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Acara yang bersinggungan dengan rentang tanggal (inklusif di kedua ujung).
     * Acara tanpa end_date dianggap berdurasi satu hari.
     *
     * Untuk data BERULANG, batas atas tidak dipakai (rangkaian yang dimulai jauh
     * di masa lalu tetap ikut terambil); penyaringan tanggalnya dikerjakan
     * occurrencesBetween() setelah baris dimuat.
     */
    public function scopeInRange(Builder $query, string $from, string $to): Builder
    {
        return $query->where(function (Builder $q) use ($from, $to) {
            $q->where(function (Builder $one) use ($from, $to) {
                $one->whereNull('repeat_type')
                    ->where('start_date', '<=', $to)
                    ->whereRaw('COALESCE(end_date, start_date) >= ?', [$from]);
            })->orWhere(function (Builder $series) use ($from, $to) {
                $series->whereNotNull('repeat_type')
                    ->where('start_date', '<=', $to)
                    ->where(function (Builder $until) use ($from) {
                        $until->whereNull('repeat_until')->orWhere('repeat_until', '>=', $from);
                    });
            });
        });
    }

    public function scopeTodo(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_TODO);
    }

    public function scopeRecurring(Builder $query): Builder
    {
        return $query->whereNotNull('repeat_type');
    }

    // ── Accessor ────────────────────────────────────────────────────────────
    public function getIsTodoAttribute(): bool
    {
        return $this->type === self::TYPE_TODO;
    }

    public function getIsRecurringAttribute(): bool
    {
        return in_array($this->repeat_type, self::REPEAT_TYPES, true);
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

    /** Panjang satu kemunculan dalam hari (0 = acara satu hari). */
    public function getDurationDaysAttribute(): int
    {
        if (! $this->end_date) {
            return 0;
        }

        return max(0, (int) $this->start_date->diffInDays($this->end_date, false));
    }

    /** Interval yang sudah dijamin minimal 1. */
    public function getRepeatEveryAttribute(): int
    {
        return max(1, (int) ($this->repeat_interval ?: 1));
    }

    /** @return int[] hari terpilih untuk pola mingguan (0 = Minggu). */
    public function getRepeatDayNumbersAttribute(): array
    {
        if (! $this->repeat_days) {
            return [];
        }

        $days = array_map('intval', array_filter(explode(',', (string) $this->repeat_days), 'strlen'));
        $days = array_values(array_unique(array_filter($days, fn ($d) => $d >= 0 && $d <= 6)));
        sort($days);

        return $days;
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

    /** Kalimat pola pengulangan, mis. "Setiap 2 minggu pada Selasa, Kamis". */
    public function getRepeatLabelAttribute(): ?string
    {
        if (! $this->is_recurring) {
            return null;
        }

        $every = $this->repeat_every;
        $label = match ($this->repeat_type) {
            self::REPEAT_DAILY => $every === 1 ? 'Setiap hari' : "Setiap {$every} hari",
            self::REPEAT_WEEKDAY => 'Setiap hari kerja (Sen-Jum)',
            self::REPEAT_WEEKLY => $this->weeklyLabel($every),
            self::REPEAT_MONTHLY => $this->monthlyLabel($every),
            self::REPEAT_YEARLY => $every === 1
                ? 'Setiap tahun pada ' . $this->start_date->translatedFormat('j F')
                : "Setiap {$every} tahun pada " . $this->start_date->translatedFormat('j F'),
            default => 'Berulang',
        };

        if ($this->repeat_until) {
            $label .= ', sampai ' . $this->repeat_until->translatedFormat('j M Y');
        }

        return $label;
    }

    private function weeklyLabel(int $every): string
    {
        $days = $this->repeat_day_numbers ?: [$this->start_date->dayOfWeek];
        $names = implode(', ', array_map(fn ($d) => self::DAY_NAMES[$d], $days));

        return $every === 1 ? "Setiap {$names}" : "Setiap {$every} minggu pada {$names}";
    }

    private function monthlyLabel(int $every): string
    {
        $dom = $this->repeat_day_of_month ?? $this->start_date->day;
        $on = $dom === -1 ? 'hari terakhir bulan' : 'tanggal ' . $dom;

        return $every === 1 ? "Setiap bulan pada {$on}" : "Setiap {$every} bulan pada {$on}";
    }

    // ── Perhitungan kemunculan ──────────────────────────────────────────────
    /**
     * Tanggal-tanggal MULAI kemunculan yang bersinggungan dengan rentang.
     *
     * Rentang dimundurkan sepanjang durasi acara supaya acara multi-hari yang
     * mulai sebelum rentang tetap ikut terlihat ekornya.
     *
     * @return string[] daftar tanggal Y-m-d, urut menaik
     */
    public function occurrencesBetween(string $from, string $to): array
    {
        $rangeEnd = Carbon::parse($to)->startOfDay();
        $scanStart = Carbon::parse($from)->startOfDay()->subDays($this->duration_days);
        $seriesStart = $this->start_date->copy()->startOfDay();

        if (! $this->is_recurring) {
            return $seriesStart->between($scanStart, $rangeEnd)
                ? [$seriesStart->format('Y-m-d')]
                : [];
        }

        if ($this->repeat_until) {
            $until = $this->repeat_until->copy()->startOfDay();
            if ($until->lt($rangeEnd)) {
                $rangeEnd = $until;
            }
        }

        if ($rangeEnd->lt($seriesStart) || $rangeEnd->lt($scanStart)) {
            return [];
        }

        $dates = match ($this->repeat_type) {
            self::REPEAT_DAILY => $this->dailyDates($seriesStart, $scanStart, $rangeEnd),
            self::REPEAT_WEEKDAY => $this->weekdayDates($seriesStart, $scanStart, $rangeEnd),
            self::REPEAT_WEEKLY => $this->weeklyDates($seriesStart, $scanStart, $rangeEnd),
            self::REPEAT_MONTHLY => $this->monthlyDates($seriesStart, $scanStart, $rangeEnd),
            self::REPEAT_YEARLY => $this->yearlyDates($seriesStart, $scanStart, $rangeEnd),
            default => [],
        };

        sort($dates);

        return $dates;
    }

    /**
     * Satu kemunculan yang mewakili rangkaian di panel todo: yang sedang perlu
     * dikerjakan sekarang.
     *
     * Aturannya meniru aplikasi todo pada umumnya:
     * 1. Kalau ada kemunculan yang sudah lewat / hari ini dan belum dicentang,
     *    ambil yang PALING BARU. Jadi todo harian menunjuk hari ini, bukan
     *    tanggal basi beberapa hari lalu.
     * 2. Kemunculan yang tanggalnya di bawah centang terakhir dianggap sudah
     *    terlewati, tidak ditagih lagi. Sekali dicentang, barisnya langsung
     *    lompat ke kemunculan berikutnya.
     * 3. Kalau tidak ada yang tertunggak, ambil kemunculan berikutnya.
     */
    public function activeOccurrence(string $from, string $to): ?string
    {
        $today = Carbon::today()->format('Y-m-d');
        $lastDone = $this->lastCompletedOccurrence();

        $overdue = null;
        $upcoming = null;

        foreach ($this->occurrencesBetween($from, $to) as $date) {
            if ($this->isDoneOn($date)) {
                continue;
            }

            if ($date <= $today) {
                if ($lastDone === null || $date > $lastDone) {
                    $overdue = $date; // daftar sudah urut menaik, jadi ini yang terbaru
                }
            } elseif ($upcoming === null) {
                $upcoming = $date;
            }
        }

        return $overdue ?? $upcoming;
    }

    /** Tanggal kemunculan terakhir yang pernah dicentang selesai. */
    public function lastCompletedOccurrence(): ?string
    {
        if (! $this->is_recurring) {
            return null;
        }

        if ($this->relationLoaded('completions')) {
            $dates = $this->completions
                ->map(fn (CalendarEventCompletion $c) => $c->occurrence_date->format('Y-m-d'))
                ->all();

            return $dates ? max($dates) : null;
        }

        $max = $this->completions()->max('occurrence_date');

        return $max ? Carbon::parse($max)->format('Y-m-d') : null;
    }

    /** Apakah tanggal kemunculan ini sudah dicentang selesai. */
    public function isDoneOn(?string $date = null): bool
    {
        if (! $this->is_recurring) {
            return (bool) $this->is_done;
        }

        if (! $date) {
            return false;
        }

        // Pakai relasi yang sudah dimuat kalau ada, biar tidak query per tanggal.
        if ($this->relationLoaded('completions')) {
            return $this->completions->contains(
                fn (CalendarEventCompletion $c) => $c->occurrence_date->format('Y-m-d') === $date
            );
        }

        return $this->completions()->whereDate('occurrence_date', $date)->exists();
    }

    // ── Pembangkit tanggal per pola ─────────────────────────────────────────
    /** @return string[] */
    private function dailyDates(Carbon $seriesStart, Carbon $scanStart, Carbon $rangeEnd): array
    {
        $step = $this->repeat_every;
        $cursor = $seriesStart->copy();

        $gap = (int) $seriesStart->diffInDays($scanStart, false);
        if ($gap > 0) {
            $cursor->addDays((int) ceil($gap / $step) * $step);
        }

        $dates = [];
        while ($cursor->lte($rangeEnd) && count($dates) < self::MAX_OCCURRENCES) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor->addDays($step);
        }

        return $dates;
    }

    /** @return string[] */
    private function weekdayDates(Carbon $seriesStart, Carbon $scanStart, Carbon $rangeEnd): array
    {
        $cursor = $seriesStart->gt($scanStart) ? $seriesStart->copy() : $scanStart->copy();

        $dates = [];
        while ($cursor->lte($rangeEnd) && count($dates) < self::MAX_OCCURRENCES) {
            if ($cursor->isWeekday()) {
                $dates[] = $cursor->format('Y-m-d');
            }
            $cursor->addDay();
        }

        return $dates;
    }

    /** @return string[] */
    private function weeklyDates(Carbon $seriesStart, Carbon $scanStart, Carbon $rangeEnd): array
    {
        $step = $this->repeat_every;
        $days = $this->repeat_day_numbers ?: [$seriesStart->dayOfWeek];

        // Minggu dihitung mulai hari Minggu, sama dengan firstDay kalender.
        $baseWeek = $seriesStart->copy()->startOfWeek(Carbon::SUNDAY);
        $cursorWeek = $baseWeek->copy();

        $scanWeek = $scanStart->copy()->startOfWeek(Carbon::SUNDAY);
        if ($scanWeek->gt($baseWeek)) {
            $weeksApart = (int) round($baseWeek->diffInDays($scanWeek) / 7);
            $cursorWeek->addWeeks((int) floor($weeksApart / $step) * $step);
        }

        $dates = [];
        while ($cursorWeek->lte($rangeEnd) && count($dates) < self::MAX_OCCURRENCES) {
            foreach ($days as $dow) {
                $date = $cursorWeek->copy()->addDays($dow);
                if ($date->gte($seriesStart) && $date->gte($scanStart) && $date->lte($rangeEnd)) {
                    $dates[] = $date->format('Y-m-d');
                }
            }
            $cursorWeek->addWeeks($step);
        }

        return $dates;
    }

    /** @return string[] */
    private function monthlyDates(Carbon $seriesStart, Carbon $scanStart, Carbon $rangeEnd): array
    {
        $step = $this->repeat_every;
        $dom = $this->repeat_day_of_month ?? $seriesStart->day;

        $baseMonth = $seriesStart->copy()->startOfMonth();
        $cursorMonth = $baseMonth->copy();

        $scanMonth = $scanStart->copy()->startOfMonth();
        if ($scanMonth->gt($baseMonth)) {
            $monthsApart = (int) $baseMonth->diffInMonths($scanMonth);
            $cursorMonth->addMonthsNoOverflow((int) floor($monthsApart / $step) * $step);
        }

        $dates = [];
        while ($cursorMonth->lte($rangeEnd) && count($dates) < self::MAX_OCCURRENCES) {
            $date = null;
            if ($dom === -1) {
                $date = $cursorMonth->copy()->endOfMonth()->startOfDay();
            } elseif ($dom >= 1 && $dom <= $cursorMonth->daysInMonth) {
                // Bulan yang tanggalnya tidak ada (mis. 31 di Februari) DILEWATI,
                // bukan digeser, supaya tanggalnya konsisten tiap bulan.
                $date = $cursorMonth->copy()->day($dom);
            }

            if ($date && $date->gte($seriesStart) && $date->gte($scanStart) && $date->lte($rangeEnd)) {
                $dates[] = $date->format('Y-m-d');
            }

            $cursorMonth->addMonthsNoOverflow($step);
        }

        return $dates;
    }

    /** @return string[] */
    private function yearlyDates(Carbon $seriesStart, Carbon $scanStart, Carbon $rangeEnd): array
    {
        $step = $this->repeat_every;
        $month = $seriesStart->month;
        $day = $seriesStart->day;

        $year = $seriesStart->year;
        if ($scanStart->year > $year) {
            $year += (int) floor(($scanStart->year - $year) / $step) * $step;
        }

        $dates = [];
        while ($year <= $rangeEnd->year && count($dates) < self::MAX_OCCURRENCES) {
            // 29 Februari di tahun bukan kabisat: dilewati.
            if (checkdate($month, $day, $year)) {
                $date = Carbon::create($year, $month, $day)->startOfDay();
                if ($date->gte($seriesStart) && $date->gte($scanStart) && $date->lte($rangeEnd)) {
                    $dates[] = $date->format('Y-m-d');
                }
            }
            $year += $step;
        }

        return $dates;
    }

    // ── Serialisasi ─────────────────────────────────────────────────────────
    /**
     * Bentuk objek event untuk FullCalendar.
     *
     * @param  string|null  $occurrenceDate  tanggal mulai kemunculan (default: start_date)
     *
     * Catatan: untuk acara "seharian", FullCalendar memakai `end` EKSKLUSIF,
     * jadi tanggal akhir perlu ditambah 1 hari agar sel terakhir ikut terwarnai.
     */
    public function toCalendarPayload(?string $occurrenceDate = null): array
    {
        $start = $occurrenceDate ?: $this->start_date->format('Y-m-d');
        $end = Carbon::parse($start)->addDays($this->duration_days)->format('Y-m-d');
        $isDone = $this->isDoneOn($start);

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

        $color = $isDone ? '#9CA3AF' : $this->display_color;

        $classNames = $isDone ? ['fc-event-done'] : [];
        if ($this->is_recurring) {
            $classNames[] = 'fc-event-repeat';
        }

        return array_merge($payload, [
            // Kemunculan berulang butuh id unik per tanggal, kalau tidak
            // FullCalendar menganggapnya satu event yang sama.
            'id' => $this->is_recurring ? 'own-' . $this->id . '-' . $start : 'own-' . $this->id,
            'title' => $this->title,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'textColor' => '#ffffff',
            // Rangkaian berulang tidak bisa digeser lewat drag & drop: yang tergeser
            // harusnya aturannya, bukan satu tanggal. Ubah lewat form.
            'editable' => ! $this->is_recurring,
            'classNames' => $classNames,
            'extendedProps' => array_merge($this->formProps(), [
                'source' => 'own',
                'recordId' => $this->id,
                'isDone' => $isDone,
                'occurrenceDate' => $start,
                'timeLabel' => $this->time_label,
            ]),
        ]);
    }

    /**
     * Field yang dibutuhkan form di halaman kalender (dipakai payload & panel todo).
     */
    public function formProps(): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'color' => $this->display_color,
            'startDate' => $this->start_date->format('Y-m-d'),
            'endDate' => $this->end_date?->format('Y-m-d'),
            'startTime' => $this->start_time ? substr((string) $this->start_time, 0, 5) : null,
            'endTime' => $this->end_time ? substr((string) $this->end_time, 0, 5) : null,
            'allDay' => (bool) $this->all_day,
            'isRecurring' => $this->is_recurring,
            'repeatType' => $this->repeat_type,
            'repeatInterval' => $this->repeat_every,
            'repeatDays' => $this->repeat_day_numbers,
            'repeatDayOfMonth' => $this->repeat_day_of_month,
            'repeatUntil' => $this->repeat_until?->format('Y-m-d'),
            'repeatLabel' => $this->repeat_label,
        ];
    }

    /**
     * Bentuk ringkas untuk kalender di dashboard (dipakai JS lama).
     */
    public function toDashboardArray(?string $occurrenceDate = null): array
    {
        $date = $occurrenceDate ?: $this->start_date->format('Y-m-d');

        return [
            'id' => $this->id,
            'date' => $date,
            'title' => $this->title,
            'content' => $this->description,
            'type' => $this->type,
            'is_done' => $this->isDoneOn($date),
            'is_recurring' => $this->is_recurring,
            'repeat_label' => $this->repeat_label,
            'color' => $this->display_color,
            'time_label' => $this->time_label,
        ];
    }

    // ── Query siap pakai ────────────────────────────────────────────────────
    /**
     * Muat rangkaian dalam rentang lalu bentangkan jadi daftar kemunculan.
     * Centang selesai untuk data berulang diambil sekaligus (bukan per tanggal).
     *
     * $type membatasi ke satu tipe saja (EVENT / TODO); null = keduanya.
     *
     * @return array<int, array{event: self, date: string}> urut tanggal lalu jam
     */
    public static function expandRange(string $userId, string $from, string $to, ?string $type = null): array
    {
        /** @var EloquentCollection<int, self> $events */
        $events = self::forUser($userId)
            ->inRange($from, $to)
            ->when($type !== null, fn (Builder $q) => $q->where('type', $type))
            ->orderBy('start_date')
            ->orderByRaw('start_time IS NULL, start_time')
            ->get();

        self::loadCompletionsFor($events, $from, $to);

        $rows = [];
        foreach ($events as $event) {
            foreach ($event->occurrencesBetween($from, $to) as $date) {
                $rows[] = ['event' => $event, 'date' => $date];
            }
        }

        usort($rows, function (array $a, array $b) {
            return [$a['date'], (string) $a['event']->start_time, $a['event']->id]
                <=> [$b['date'], (string) $b['event']->start_time, $b['event']->id];
        });

        return $rows;
    }

    /**
     * Muat centang selesai (satu query) untuk seluruh rangkaian berulang di koleksi.
     * Rentang dilonggarkan supaya kemunculan multi-hari ikut terbawa.
     */
    public static function loadCompletionsFor(EloquentCollection $events, string $from, string $to): void
    {
        $recurring = $events->filter(fn (self $e) => $e->is_recurring);

        if ($recurring->isEmpty()) {
            return;
        }

        $completions = CalendarEventCompletion::whereIn('event_id', $recurring->pluck('id'))
            ->whereBetween('occurrence_date', [
                Carbon::parse($from)->subYear()->format('Y-m-d'),
                Carbon::parse($to)->addYear()->format('Y-m-d'),
            ])
            ->get()
            ->groupBy('event_id');

        foreach ($recurring as $event) {
            $event->setRelation('completions', $completions->get($event->id) ?: new EloquentCollection());
        }
    }

    /**
     * Agenda satu bulan untuk kalender dashboard (array polos).
     * Acara multi-hari & berulang ikut terbentang jadi kemunculan per tanggal.
     *
     * TODO SENGAJA TIDAK IKUT: todo (terutama yang rutin) tempatnya di panel
     * Todo halaman Kalender, bukan memenuhi kotak tanggal.
     */
    public static function getEventsForMonth(string $userId, int $year, int $month): array
    {
        $from = Carbon::create($year, $month, 1)->startOfMonth();
        $to = $from->copy()->endOfMonth();

        return array_map(
            fn (array $row) => $row['event']->toDashboardArray($row['date']),
            self::expandRange($userId, $from->format('Y-m-d'), $to->format('Y-m-d'), self::TYPE_EVENT)
        );
    }
}
