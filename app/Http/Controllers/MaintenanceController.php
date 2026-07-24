<?php

namespace App\Http\Controllers;

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

        if (in_array($request->type, ['TEXT', 'DATE', 'MONTH', 'YEAR'], true)) {
            $query->where('schedule_type', $request->type);
        }

        $tasks = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('maintenance.index', compact('tasks'));
    }

    public function create(): View
    {
        return view('maintenance.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        MaintenanceTask::create($data);

        return redirect()->route('maintenance.index')
            ->with('success', 'Catatan maintenance berhasil dibuat!');
    }

    public function edit(MaintenanceTask $maintenance): View
    {
        return view('maintenance.edit', ['task' => $maintenance]);
    }

    public function update(Request $request, MaintenanceTask $maintenance): RedirectResponse
    {
        $data = $this->validateData($request);

        $maintenance->update($data);

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
     * Validasi + normalisasi jadwal sesuai tipe yang dipilih.
     */
    private function validateData(Request $request): array
    {
        $base = $request->validate([
            'name' => 'required|string|max:255',
            'schedule_type' => 'required|in:TEXT,DATE,MONTH,YEAR',
            'notes' => 'nullable|string',
        ]);

        $base['schedule_value'] = match ($base['schedule_type']) {
            'TEXT' => $request->validate(['schedule_text' => 'required|string|max:255'])['schedule_text'],
            'DATE' => Carbon::parse($request->validate(['schedule_date' => 'required|date'])['schedule_date'])->format('Y-m-d'),
            'MONTH' => $request->validate(['schedule_month' => ['required', 'regex:/^\d{4}-\d{2}$/']])['schedule_month'],
            'YEAR' => (string) $request->validate(['schedule_year' => 'required|integer|min:1900|max:2200'])['schedule_year'],
        };

        return $base;
    }
}
