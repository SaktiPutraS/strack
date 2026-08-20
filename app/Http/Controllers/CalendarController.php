<?php

namespace App\Http\Controllers;

use App\Models\CalendarEvent;
use App\Models\CalendarEventCompletion;
use App\Models\DebtRecord;
use App\Models\Domain;
use App\Models\MaintenanceTask;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Menu Kalender: agenda & todo pribadi, digabung dengan tanggal penting dari
 * modul lain (deadline proyek, kedaluwarsa domain, jadwal maintenance,
 * jatuh tempo hutang piutang).
 */
class CalendarController extends Controller
{
    /** Sumber data yang bisa ditampilkan di kalender. */
    public const SOURCES = ['own', 'projects', 'domains', 'maintenance', 'debts'];

    /**
     * Jendela pencarian kemunculan todo BERULANG untuk panel samping.
     * Ke belakang secukupnya buat menagih yang tertunggak, ke depan cukup jauh
     * supaya pola bulanan/tahunan tetap kebagian satu baris.
     */
    private const TODO_LOOKBACK_DAYS = 92;
    private const TODO_LOOKAHEAD_DAYS = 400;

    /** Warna per sumber eksternal. */
    private const SOURCE_COLORS = [
        'projects' => '#3B82F6',
        'domains' => '#0EA5E9',
        'maintenance' => '#F59E0B',
        'debts' => '#EF4444',
    ];

    private function userId(): string
    {
        return (string) session('role', 'admin');
    }

    // ── Halaman ─────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $initialDate = $request->query('date');
        if ($initialDate && ! $this->isValidDate($initialDate)) {
            $initialDate = null;
        }

        return view('calendar.index', [
            'initialDate' => $initialDate,
            'initialView' => $request->query('view', 'dayGridMonth'),
            'colors' => CalendarEvent::COLORS,
            'dayNames' => CalendarEvent::DAY_NAMES_SHORT,
        ]);
    }

    // ── Feed FullCalendar ───────────────────────────────────────────────────
    /**
     * Kembalikan semua event dalam rentang yang diminta FullCalendar.
     * Param: start, end (ISO), sources (dipisah koma; default semua).
     */
    public function feed(Request $request): JsonResponse
    {
        $start = $this->parseDate($request->query('start'), Carbon::now()->startOfMonth());
        $end = $this->parseDate($request->query('end'), Carbon::now()->endOfMonth());

        $from = $start->format('Y-m-d');
        $to = $end->format('Y-m-d');

        $requested = $request->query('sources');
        $sources = $requested === null || $requested === ''
            ? self::SOURCES
            : array_values(array_intersect(explode(',', $requested), self::SOURCES));

        $events = [];

        if (in_array('own', $sources, true)) {
            $events = array_merge($events, $this->ownEvents($from, $to));
        }
        if (in_array('projects', $sources, true)) {
            $events = array_merge($events, $this->projectEvents($from, $to));
        }
        if (in_array('domains', $sources, true)) {
            $events = array_merge($events, $this->domainEvents($from, $to));
        }
        if (in_array('maintenance', $sources, true)) {
            $events = array_merge($events, $this->maintenanceEvents($start, $end));
        }
        if (in_array('debts', $sources, true)) {
            $events = array_merge($events, $this->debtEvents($from, $to));
        }

        return response()->json($events);
    }

    /**
     * Daftar todo untuk panel samping: yang belum selesai (termasuk terlewat)
     * plus yang baru saja diselesaikan.
     *
     * Todo BERULANG diwakili SATU baris saja: kemunculan yang sedang perlu
     * dikerjakan (lihat CalendarEvent::activeOccurrence). Begitu dicentang,
     * barisnya lompat ke kemunculan berikutnya, jadi panel tidak dibanjiri
     * todo harian.
     */
    public function todos(Request $request): JsonResponse
    {
        $userId = $this->userId();
        $today = Carbon::today();

        $from = $today->copy()->subDays(self::TODO_LOOKBACK_DAYS)->format('Y-m-d');
        $to = $today->copy()->addDays(self::TODO_LOOKAHEAD_DAYS)->format('Y-m-d');

        // Rangkaian berulang selalu ikut diambil (kolom is_done tidak dipakai
        // untuk data berulang - centangnya per tanggal di tabel completions).
        $candidates = CalendarEvent::forUser($userId)
            ->todo()
            ->where(function ($q) {
                $q->whereNotNull('repeat_type')->orWhere('is_done', false);
            })
            ->orderBy('start_date')
            ->orderByRaw('start_time IS NULL, start_time')
            ->limit(200)
            ->get();

        CalendarEvent::loadCompletionsFor($candidates, $from, $to);

        $pending = [];
        foreach ($candidates as $todo) {
            if ($todo->is_recurring) {
                $date = $todo->activeOccurrence($from, $to);
                if ($date) {
                    $pending[] = $this->todoRow($todo, $date, $today);
                }
                continue;
            }

            $pending[] = $this->todoRow($todo, $todo->start_date->format('Y-m-d'), $today);
        }

        usort($pending, fn (array $a, array $b) => [$a['occurrenceDate'], (string) $a['startTime']]
            <=> [$b['occurrenceDate'], (string) $b['startTime']]);

        return response()->json([
            'success' => true,
            'pending' => array_values($pending),
            'done' => $this->recentlyDoneTodos($userId, $today),
        ]);
    }

    /**
     * Todo yang baru saja diselesaikan: gabungan todo sekali jalan (kolom
     * is_done) dan centang per tanggal dari rangkaian berulang.
     */
    private function recentlyDoneTodos(string $userId, Carbon $today): array
    {
        $rows = [];

        $once = CalendarEvent::forUser($userId)
            ->todo()
            ->whereNull('repeat_type')
            ->where('is_done', true)
            ->orderByDesc('completed_at')
            ->limit(20)
            ->get();

        foreach ($once as $todo) {
            $rows[] = $this->todoRow($todo, $todo->start_date->format('Y-m-d'), $today, true, $todo->completed_at);
        }

        $recurringIds = CalendarEvent::forUser($userId)->todo()->recurring()->pluck('id');

        if ($recurringIds->isNotEmpty()) {
            $completions = CalendarEventCompletion::with('event')
                ->whereIn('event_id', $recurringIds)
                ->orderByDesc('completed_at')
                ->limit(20)
                ->get();

            foreach ($completions as $completion) {
                if (! $completion->event) {
                    continue;
                }
                $rows[] = $this->todoRow(
                    $completion->event,
                    $completion->occurrence_date->format('Y-m-d'),
                    $today,
                    true,
                    $completion->completed_at,
                );
            }
        }

        usort($rows, fn (array $a, array $b) => ($b['completedAt'] ?? '') <=> ($a['completedAt'] ?? ''));

        return array_slice($rows, 0, 20);
    }

    /**
     * Satu baris todo di panel samping.
     *
     * Field form (startDate dkk) sengaja diambil dari RANGKAIAN, bukan dari
     * tanggal kemunculan, supaya klik baris langsung membuka form edit yang
     * benar. Tanggal kemunculan dibawa terpisah lewat `occurrenceDate`.
     */
    private function todoRow(
        CalendarEvent $todo,
        string $date,
        Carbon $today,
        bool $isDone = false,
        ?Carbon $completedAt = null,
    ): array {
        $occurrence = Carbon::parse($date);

        return array_merge($todo->formProps(), [
            'id' => $todo->id,
            'occurrenceDate' => $date,
            'dateLabel' => $occurrence->translatedFormat('j M Y'),
            'timeLabel' => $todo->time_label,
            'isDone' => $isDone,
            'isOverdue' => ! $isDone && $occurrence->lt($today),
            'isToday' => $occurrence->isSameDay($today),
            'completedAt' => $completedAt?->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Agenda & todo satu bulan, dipakai kalender di dashboard.
     */
    public function monthEvents(int $year, int $month): JsonResponse
    {
        try {
            if ($month < 1 || $month > 12) {
                return response()->json(['success' => false, 'message' => 'Bulan tidak valid'], 422);
            }

            return response()->json([
                'success' => true,
                'notes' => CalendarEvent::getEventsForMonth($this->userId(), $year, $month),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data kalender',
            ], 500);
        }
    }

    // ── CRUD agenda / todo ──────────────────────────────────────────────────
    public function store(Request $request): JsonResponse
    {
        $data = $this->validatePayload($request);

        $event = CalendarEvent::create($data + ['user_id' => $this->userId()]);

        return response()->json([
            'success' => true,
            'message' => $event->is_todo ? 'Todo berhasil disimpan' : 'Agenda berhasil disimpan',
            'event' => $event->toCalendarPayload(),
        ]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $event = $this->findOwned($id);
        if (! $event) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Isi field yang tidak dikirim dengan nilai yang sudah tersimpan, supaya
        // form ringkas (mis. catatan di dashboard) tidak menghapus jam/warna/tipe.
        $request->merge(array_merge([
            'type' => $event->type,
            'start_date' => $event->start_date->format('Y-m-d'),
            'end_date' => $event->end_date?->format('Y-m-d'),
            'start_time' => $event->start_time ? substr((string) $event->start_time, 0, 5) : null,
            'end_time' => $event->end_time ? substr((string) $event->end_time, 0, 5) : null,
            'all_day' => $event->all_day,
            'color' => $event->display_color,
            'is_done' => $event->is_done,
            'description' => $event->description,
            'repeat_type' => $event->repeat_type,
            'repeat_interval' => $event->repeat_every,
            'repeat_days' => $event->repeat_day_numbers,
            'repeat_day_of_month' => $event->repeat_day_of_month,
            'repeat_until' => $event->repeat_until?->format('Y-m-d'),
        ], $request->all()));

        $data = $this->validatePayload($request);
        $ruleChanged = $this->repeatRuleChanged($event, $data);

        $event->update($data);

        // Aturan pengulangan berubah: centang per tanggal yang lama sudah tidak
        // cocok lagi dengan kemunculan yang baru, jadi dibersihkan.
        if ($ruleChanged) {
            $event->completions()->delete();
            $event->load('completions');
        }

        return response()->json([
            'success' => true,
            'message' => 'Perubahan berhasil disimpan',
            'event' => $event->fresh()->toCalendarPayload(),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $event = $this->findOwned($id);
        if (! $event) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // FK sudah cascade, tapi dihapus eksplisit supaya tidak bergantung pada
        // dukungan foreign key di tiap koneksi.
        $event->completions()->delete();
        $event->delete();

        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
    }

    /**
     * Centang / batalkan centang todo.
     *
     * Data BERULANG dicentang per tanggal (param `date`), supaya todo harian
     * yang beres hari ini tetap muncul lagi besok. Data sekali jalan tetap
     * memakai kolom is_done seperti sebelumnya.
     */
    public function toggleDone(Request $request, int $id): JsonResponse
    {
        $event = $this->findOwned($id);
        if (! $event) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        if (! $event->is_recurring) {
            $done = ! $event->is_done;
            $event->update([
                'is_done' => $done,
                'completed_at' => $done ? Carbon::now() : null,
            ]);

            return response()->json([
                'success' => true,
                'message' => $done ? 'Ditandai selesai' : 'Tanda selesai dibatalkan',
                'isDone' => $done,
                'event' => $event->fresh()->toCalendarPayload(),
            ]);
        }

        $date = (string) $request->input('date', '');
        if (! $date || ! $this->isValidDate($date)) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal kemunculan tidak valid',
            ], 422);
        }

        $existing = $event->completions()->whereDate('occurrence_date', $date)->first();

        if ($existing) {
            $existing->delete();
            $done = false;
        } else {
            $event->completions()->create([
                'occurrence_date' => $date,
                'completed_at' => Carbon::now(),
            ]);
            $done = true;
        }

        $event->load('completions');
        $label = Carbon::parse($date)->translatedFormat('j M Y');

        return response()->json([
            'success' => true,
            'message' => $done ? "Selesai untuk {$label}" : "Tanda selesai {$label} dibatalkan",
            'isDone' => $done,
            'event' => $event->toCalendarPayload($date),
        ]);
    }

    /**
     * Geser / ubah durasi lewat drag & drop di kalender.
     */
    public function move(Request $request, int $id): JsonResponse
    {
        $event = $this->findOwned($id);
        if (! $event) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Menggeser satu kemunculan tidak punya arti untuk rangkaian berulang:
        // yang harus berubah aturannya, lewat form.
        if ($event->is_recurring) {
            return response()->json([
                'success' => false,
                'message' => 'Agenda berulang tidak bisa digeser. Ubah pola pengulangannya lewat form.',
            ], 422);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'all_day' => 'nullable|boolean',
        ]);

        $allDay = $request->boolean('all_day', $event->all_day);

        $event->update([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $allDay ? null : $this->normalizeTime($validated['start_time'] ?? null),
            'end_time' => $allDay ? null : $this->normalizeTime($validated['end_time'] ?? null),
            'all_day' => $allDay,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jadwal diperbarui',
            'event' => $event->fresh()->toCalendarPayload(),
        ]);
    }

    // ── Sumber event ────────────────────────────────────────────────────────
    /**
     * Agenda pribadi saja. TODO SENGAJA TIDAK DIMASUKKAN ke kotak tanggal:
     * todo rutin akan membanjiri tampilan bulanan. Tempatnya di panel Todo
     * sebelah kanan (lihat todos()).
     */
    private function ownEvents(string $from, string $to): array
    {
        return array_map(
            fn (array $row) => $row['event']->toCalendarPayload($row['date']),
            CalendarEvent::expandRange($this->userId(), $from, $to, CalendarEvent::TYPE_EVENT)
        );
    }

    private function projectEvents(string $from, string $to): array
    {
        $today = Carbon::today();

        return Project::with('client')
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [$from, $to])
            ->whereIn('status', ['WAITING', 'PROGRESS'])
            ->get()
            ->map(function (Project $project) use ($today) {
                $deadline = $project->deadline;
                $daysLeft = (int) $today->diffInDays($deadline, false);

                // Merah = terlewat, kuning = <= 7 hari, biru = masih lama.
                $color = $daysLeft < 0 ? '#EF4444' : ($daysLeft <= 7 ? '#F59E0B' : self::SOURCE_COLORS['projects']);

                return $this->externalEvent(
                    id: 'project-' . $project->id,
                    title: ($project->client->name ?? 'Tanpa klien') . ' - ' . $project->title,
                    date: $deadline->format('Y-m-d'),
                    color: $color,
                    source: 'projects',
                    icon: 'bi-list-task',
                    sourceLabel: 'Deadline Proyek',
                    url: route('projects.show', $project),
                    details: array_filter([
                        'Klien' => $project->client->name ?? '-',
                        'Status' => $project->status,
                        'Nilai' => 'Rp ' . number_format((float) $project->total_value, 0, ',', '.'),
                        'Sisa tagihan' => 'Rp ' . number_format((float) $project->remaining_amount, 0, ',', '.'),
                    ]),
                );
            })
            ->all();
    }

    private function domainEvents(string $from, string $to): array
    {
        return Domain::with('client')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [$from, $to])
            ->get()
            ->map(function (Domain $domain) {
                $days = $domain->days_until_expiry;
                $color = $days !== null && $days < 0
                    ? '#EF4444'
                    : ($days !== null && $days <= Domain::EXPIRING_DAYS ? '#F59E0B' : self::SOURCE_COLORS['domains']);

                return $this->externalEvent(
                    id: 'domain-' . $domain->id,
                    title: $domain->name,
                    date: $domain->expires_at->format('Y-m-d'),
                    color: $color,
                    source: 'domains',
                    icon: 'bi-globe2',
                    sourceLabel: 'Domain kedaluwarsa',
                    url: route('domains.index', ['search' => $domain->name]),
                    details: array_filter([
                        'Klien' => $domain->client->name ?? '-',
                        'Provider' => $domain->provider ?: '-',
                        'Biaya perpanjangan' => $domain->renewal_cost
                            ? 'Rp ' . number_format((float) $domain->renewal_cost, 0, ',', '.')
                            : null,
                    ]),
                );
            })
            ->all();
    }

    /**
     * Jadwal maintenance. Tipe DATE punya tanggal pasti; MONTH berulang tiap
     * bulan tertentu (dipasang di tanggal 1); YEAR dipasang 1 Januari tahun itu.
     * Tipe TEXT & ODOMETER tidak punya tanggal, jadi dilewati.
     */
    private function maintenanceEvents(Carbon $start, Carbon $end): array
    {
        $tasks = MaintenanceTask::whereIn('schedule_type', ['DATE', 'MONTH', 'YEAR'])->get();
        $events = [];

        foreach ($tasks as $task) {
            foreach ($this->maintenanceDates($task, $start, $end) as $date) {
                $events[] = $this->externalEvent(
                    id: 'maintenance-' . $task->id . '-' . $date,
                    title: $task->name,
                    date: $date,
                    color: $task->status === 'DUE' ? '#EF4444' : self::SOURCE_COLORS['maintenance'],
                    source: 'maintenance',
                    icon: 'bi-tools',
                    sourceLabel: 'Maintenance',
                    url: route('maintenance.index'),
                    details: array_filter([
                        'Jadwal' => $task->schedule_label,
                        'Status' => $task->status_label,
                        'Terakhir dikerjakan' => $task->last_done_at?->translatedFormat('j M Y'),
                    ]),
                );
            }
        }

        return $events;
    }

    /** @return string[] daftar tanggal (Y-m-d) kemunculan tugas dalam rentang. */
    private function maintenanceDates(MaintenanceTask $task, Carbon $start, Carbon $end): array
    {
        try {
            switch ($task->schedule_type) {
                case 'DATE':
                    if (! $task->schedule_value) {
                        return [];
                    }
                    $date = Carbon::parse($task->schedule_value);

                    return $date->between($start, $end) ? [$date->format('Y-m-d')] : [];

                case 'YEAR':
                    $year = (int) $task->schedule_value;
                    if ($year < 1970) {
                        return [];
                    }
                    $date = Carbon::create($year, 1, 1);

                    return $date->between($start, $end) ? [$date->format('Y-m-d')] : [];

                case 'MONTH':
                    $months = $task->monthNumbers();
                    if (empty($months)) {
                        return [];
                    }
                    $dates = [];
                    $cursor = $start->copy()->startOfMonth();
                    while ($cursor->lte($end)) {
                        if (in_array($cursor->month, $months, true) && $cursor->between($start, $end)) {
                            $dates[] = $cursor->format('Y-m-d');
                        }
                        $cursor->addMonth();
                    }

                    return $dates;

                default:
                    return [];
            }
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function debtEvents(string $from, string $to): array
    {
        return DebtRecord::whereNotNull('due_date')
            ->whereBetween('due_date', [$from, $to])
            ->where('status', '!=', 'PAID')
            ->get()
            ->map(function (DebtRecord $debt) {
                return $this->externalEvent(
                    id: 'debt-' . $debt->id,
                    title: $debt->type_label . ' - ' . $debt->party_name,
                    date: $debt->due_date->format('Y-m-d'),
                    color: $debt->is_overdue ? '#EF4444' : self::SOURCE_COLORS['debts'],
                    source: 'debts',
                    icon: 'bi-cash-stack',
                    sourceLabel: 'Jatuh tempo ' . strtolower($debt->type_label),
                    url: route('debts.show', $debt),
                    details: array_filter([
                        'Keterangan' => $debt->title ?: '-',
                        'Nilai' => $debt->formatted_principal,
                        'Sisa' => $debt->formatted_remaining,
                        'Status' => $debt->status_label,
                    ]),
                );
            })
            ->all();
    }

    /** Bentuk event read-only dari modul lain. */
    private function externalEvent(
        string $id,
        string $title,
        string $date,
        string $color,
        string $source,
        string $icon,
        string $sourceLabel,
        string $url,
        array $details = [],
    ): array {
        return [
            'id' => $id,
            'title' => $title,
            'start' => $date,
            'allDay' => true,
            'backgroundColor' => $color,
            'borderColor' => $color,
            'textColor' => '#ffffff',
            'editable' => false,
            'extendedProps' => [
                'source' => $source,
                'sourceLabel' => $sourceLabel,
                'icon' => $icon,
                'url' => $url,
                'details' => $details,
                'startDate' => $date,
            ],
        ];
    }

    // ── Util ────────────────────────────────────────────────────────────────
    private function findOwned(int $id): ?CalendarEvent
    {
        return CalendarEvent::forUser($this->userId())->find($id);
    }

    private function validatePayload(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:500',
            'description' => 'nullable|string',
            'type' => 'required|in:EVENT,TODO',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i',
            'all_day' => 'nullable|boolean',
            'color' => 'nullable|string|max:20',
            'is_done' => 'nullable|boolean',
            'repeat_type' => 'nullable|in:' . implode(',', CalendarEvent::REPEAT_TYPES),
            'repeat_interval' => 'nullable|integer|min:1|max:365',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'integer|min:0|max:6',
            'repeat_day_of_month' => 'nullable|integer|min:-1|max:31',
            'repeat_until' => 'nullable|date|after_or_equal:start_date',
        ], [], [
            'title' => 'judul',
            'start_date' => 'tanggal mulai',
            'end_date' => 'tanggal selesai',
            'repeat_until' => 'batas akhir pengulangan',
            'repeat_interval' => 'interval pengulangan',
        ]);

        $allDay = $request->boolean('all_day', true);
        $startTime = $allDay ? null : ($validated['start_time'] ?? null);
        $endTime = $allDay ? null : ($validated['end_time'] ?? null);

        // Tanpa jam mulai, jam selesai tidak punya arti.
        if (! $startTime) {
            $endTime = null;
            $allDay = true;
        }

        // Jam selesai sebelum jam mulai di hari yang sama: abaikan saja.
        if ($startTime && $endTime && ($validated['end_date'] ?? $validated['start_date']) === $validated['start_date'] && $endTime <= $startTime) {
            $endTime = null;
        }

        $repeat = $this->normalizeRepeat($validated);

        // Data berulang tidak punya status selesai tunggal: centangnya per
        // tanggal, disimpan di calendar_event_completions.
        $isDone = $repeat['repeat_type'] ? false : $request->boolean('is_done');

        return array_merge([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'] ?? null,
            'start_time' => $this->normalizeTime($startTime),
            'end_time' => $this->normalizeTime($endTime),
            'all_day' => $allDay,
            'color' => $validated['color'] ?? CalendarEvent::DEFAULT_COLOR,
            'is_done' => $isDone,
            'completed_at' => $isDone ? Carbon::now() : null,
        ], $repeat);
    }

    /**
     * Rapikan aturan pengulangan: buang field yang tidak relevan untuk pola
     * terpilih, dan isi bagian yang dikosongkan user dari tanggal mulai.
     *
     * @return array{repeat_type: ?string, repeat_interval: int, repeat_days: ?string, repeat_day_of_month: ?int, repeat_until: ?string}
     */
    private function normalizeRepeat(array $validated): array
    {
        $type = $validated['repeat_type'] ?? null;

        if (! $type) {
            return [
                'repeat_type' => null,
                'repeat_interval' => 1,
                'repeat_days' => null,
                'repeat_day_of_month' => null,
                'repeat_until' => null,
            ];
        }

        $start = Carbon::parse($validated['start_date']);
        $interval = max(1, (int) ($validated['repeat_interval'] ?? 1));
        $days = null;
        $dayOfMonth = null;

        if ($type === CalendarEvent::REPEAT_WEEKDAY) {
            // Sen-Jum sudah menentukan harinya sendiri, interval tidak dipakai.
            $interval = 1;
        }

        if ($type === CalendarEvent::REPEAT_WEEKLY) {
            $picked = array_map('intval', $validated['repeat_days'] ?? []);
            $picked = array_values(array_unique(array_filter($picked, fn ($d) => $d >= 0 && $d <= 6)));
            sort($picked);
            // Tidak ada hari yang dicentang: pakai hari dari tanggal mulai.
            $days = implode(',', $picked ?: [$start->dayOfWeek]);
        }

        if ($type === CalendarEvent::REPEAT_MONTHLY) {
            $dom = $validated['repeat_day_of_month'] ?? null;
            // 0 tidak punya arti; -1 dipakai untuk "hari terakhir bulan".
            $dayOfMonth = ($dom === null || (int) $dom === 0) ? $start->day : (int) $dom;
        }

        return [
            'repeat_type' => $type,
            'repeat_interval' => $interval,
            'repeat_days' => $days,
            'repeat_day_of_month' => $dayOfMonth,
            'repeat_until' => $validated['repeat_until'] ?? null,
        ];
    }

    /** Apakah aturan pengulangan (atau tanggal mulainya) berubah. */
    private function repeatRuleChanged(CalendarEvent $event, array $data): bool
    {
        $before = [
            $event->repeat_type,
            $event->repeat_every,
            (string) $event->repeat_days,
            $event->repeat_day_of_month,
            $event->start_date->format('Y-m-d'),
        ];

        $after = [
            $data['repeat_type'],
            (int) $data['repeat_interval'],
            (string) $data['repeat_days'],
            $data['repeat_day_of_month'],
            Carbon::parse($data['start_date'])->format('Y-m-d'),
        ];

        return $before !== $after;
    }

    /** Samakan jam ke format H:i:s supaya nilainya konsisten di DB. */
    private function normalizeTime(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        return strlen($value) === 5 ? $value . ':00' : $value;
    }

    private function parseDate(?string $value, Carbon $fallback): Carbon
    {
        if (! $value) {
            return $fallback;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return $fallback;
        }
    }

    private function isValidDate(string $value): bool
    {
        try {
            Carbon::createFromFormat('Y-m-d', $value);

            return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $value);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
