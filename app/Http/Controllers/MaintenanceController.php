<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\MaintenanceTask;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceController extends Controller
{
    public function index(Request $request): View
    {
        $query = MaintenanceTask::query();

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        $all = $query->orderBy('name')->get();

        // Hitung jumlah per status (status dihitung di PHP karena bergantung tanggal/km).
        $counts = [
            'all' => $all->count(),
            'due' => $all->where('status', 'DUE')->count(),
            'scheduled' => $all->where('status', 'SCHEDULED')->count(),
            'done' => $all->where('status', 'DONE')->count(),
        ];

        $filter = $request->get('status');
        $tasks = match ($filter) {
            'perlu' => $all->where('status', 'DUE'),
            'terjadwal' => $all->where('status', 'SCHEDULED'),
            'selesai' => $all->where('status', 'DONE'),
            default => $all,
        };

        // Urutkan: perlu dikerjakan di atas, lalu terjadwal, lalu selesai; kemudian nama.
        $tasks = $tasks->sortBy([
            fn ($a, $b) => $a->status_sort <=> $b->status_sort,
            fn ($a, $b) => strcasecmp($a->name, $b->name),
        ])->values();

        return view('maintenance.index', compact('tasks', 'counts', 'filter'));
    }

    public function create(): View
    {
        return view('maintenance.create');
    }

    public function store(Request $request): RedirectResponse
    {
        MaintenanceTask::create($this->validateData($request));

        return redirect()->route('maintenance.index')
            ->with('success', 'Catatan maintenance berhasil dibuat!');
    }

    public function show(MaintenanceTask $maintenance): View
    {
        $maintenance->load('logs');

        return view('maintenance.show', ['task' => $maintenance]);
    }

    public function edit(MaintenanceTask $maintenance): View
    {
        return view('maintenance.edit', ['task' => $maintenance]);
    }

    public function update(Request $request, MaintenanceTask $maintenance): RedirectResponse
    {
        $maintenance->update($this->validateData($request));

        return redirect()->route('maintenance.index')
            ->with('success', 'Catatan maintenance berhasil diperbarui.');
    }

    public function destroy(MaintenanceTask $maintenance): RedirectResponse
    {
        $maintenance->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'Catatan maintenance berhasil dihapus.');
    }

    /**
     * Tandai tugas selesai: catat ke riwayat + perbarui data tugas.
     */
    public function complete(Request $request, MaintenanceTask $maintenance): RedirectResponse
    {
        $data = $request->validate([
            'done_at' => 'nullable|date',
            'odometer' => 'nullable|integer|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $doneAt = !empty($data['done_at']) ? Carbon::parse($data['done_at']) : Carbon::today();

        $maintenance->logs()->create([
            'done_at' => $doneAt->format('Y-m-d'),
            'odometer' => $data['odometer'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $maintenance->last_done_at = $doneAt->format('Y-m-d');
        if ($maintenance->schedule_type === 'ODOMETER' && isset($data['odometer'])) {
            $maintenance->last_km = $data['odometer'];
        }
        $maintenance->save();

        return redirect()->back()->with('success', "\"{$maintenance->name}\" ditandai selesai.");
    }

    /**
     * Hapus satu entri riwayat + sinkronkan data terakhir tugas.
     */
    public function destroyLog(MaintenanceLog $maintenanceLog): RedirectResponse
    {
        $task = $maintenanceLog->task;
        $maintenanceLog->delete();

        if ($task) {
            $latest = $task->logs()->first();
            $task->last_done_at = $latest?->done_at?->format('Y-m-d');
            if ($task->schedule_type === 'ODOMETER') {
                $task->last_km = $latest?->odometer;
            }
            $task->save();
        }

        return redirect()->back()->with('success', 'Satu entri riwayat dihapus.');
    }

    /**
     * Validasi + normalisasi data tugas sesuai tipe jadwal.
     */
    private function validateData(Request $request): array
    {
        $base = $request->validate([
            'name' => 'required|string|max:255',
            'schedule_type' => 'required|in:TEXT,DATE,MONTH,YEAR,ODOMETER',
            'notes' => 'nullable|string',
        ]);

        // Reset kolom yang tidak dipakai tipe ini.
        $base['schedule_value'] = null;
        $base['interval_km'] = null;
        $base['last_km'] = null;

        switch ($base['schedule_type']) {
            case 'TEXT':
                $base['schedule_value'] = $request->validate(['schedule_text' => 'required|string|max:255'])['schedule_text'];
                break;
            case 'DATE':
                $base['schedule_value'] = Carbon::parse($request->validate(['schedule_date' => 'required|date'])['schedule_date'])->format('Y-m-d');
                break;
            case 'MONTH':
                $base['schedule_value'] = collect($request->validate([
                    'schedule_months' => 'required|array|min:1',
                    'schedule_months.*' => 'integer|min:1|max:12',
                ], [
                    'schedule_months.required' => 'Pilih minimal satu bulan.',
                ])['schedule_months'])->map(fn ($m) => (int) $m)->unique()->sort()->values()->implode(',');
                break;
            case 'YEAR':
                $base['schedule_value'] = (string) $request->validate(['schedule_year' => 'required|integer|min:1900|max:2200'])['schedule_year'];
                break;
            case 'ODOMETER':
                $km = $request->validate([
                    'interval_km' => 'required|integer|min:1',
                    'last_km' => 'nullable|integer|min:0',
                ]);
                $base['interval_km'] = $km['interval_km'];
                $base['last_km'] = $km['last_km'] ?? null;
                break;
        }

        return $base;
    }
}
