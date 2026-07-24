<?php

namespace App\Http\Controllers;

use App\Models\DebtRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DebtRecordController extends Controller
{
    public function index(Request $request): View
    {
        $query = DebtRecord::query();

        if (in_array($request->type, ['HUTANG', 'PIUTANG'], true)) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Yang belum lunas di atas, lalu jatuh tempo terdekat, lalu terbaru.
        $records = $query
            ->orderByRaw("status = 'PAID'")
            ->orderByRaw('due_date IS NULL')
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $totalPiutang = (float) DebtRecord::piutang()->sum(DB::raw('principal_amount - paid_amount'));
        $totalHutang = (float) DebtRecord::hutang()->sum(DB::raw('principal_amount - paid_amount'));
        $selisih = $totalPiutang - $totalHutang;

        return view('debts.index', compact('records', 'totalPiutang', 'totalHutang', 'selisih'));
    }

    public function create(): View
    {
        return view('debts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:HUTANG,PIUTANG',
            'party_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'principal_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $validated['paid_amount'] = 0;

        DebtRecord::create($validated);

        return redirect()->route('debts.index')
            ->with('success', 'Catatan hutang/piutang berhasil dibuat!');
    }

    public function show(DebtRecord $debt): View
    {
        $debt->load(['payments' => fn ($q) => $q->orderByDesc('payment_date')->orderByDesc('id')]);

        return view('debts.show', compact('debt'));
    }

    public function edit(DebtRecord $debt): View
    {
        return view('debts.edit', compact('debt'));
    }

    public function update(Request $request, DebtRecord $debt): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:HUTANG,PIUTANG',
            'party_name' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'principal_amount' => 'required|numeric|min:0',
            'due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        // Cegah nilai total lebih kecil dari yang sudah dibayar (jadi minus/tak konsisten).
        if ((float) $validated['principal_amount'] < (float) $debt->paid_amount) {
            return back()
                ->withErrors(['principal_amount' => 'Nilai total tidak boleh lebih kecil dari yang sudah dibayar (' . $debt->formatted_paid . ').'])
                ->withInput();
        }

        $debt->update($validated);

        return redirect()->route('debts.show', $debt)
            ->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroy(DebtRecord $debt): RedirectResponse
    {
        $debt->delete();

        return redirect()->route('debts.index')
            ->with('success', 'Catatan hutang/piutang berhasil dihapus.');
    }
}
