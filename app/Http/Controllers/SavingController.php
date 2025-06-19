<?php

namespace App\Http\Controllers;

use App\Models\Saving;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class SavingController extends Controller
{
    public function index(Request $request): View
    {
        $query = Saving::with(['payment.project.client']);

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('is_verified')) {
            $query->where('is_verified', $request->is_verified);
        }

        $sortBy = $request->get('sort', 'transaction_date');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $savings = $query->paginate(15)->withQueryString();

        $totalSavings = Saving::getTotalSavings();
        $currentBankBalance = Saving::getCurrentBankBalance();
        $isBalanced = Saving::isSavingsBalanced();
        $difference = Saving::getSavingsDifference();

        $monthlySavings = Saving::whereYear('transaction_date', Carbon::now()->year)
            ->whereMonth('transaction_date', Carbon::now()->month)
            ->sum('amount');

        return view('savings.index', compact(
            'savings',
            'totalSavings',
            'currentBankBalance',
            'isBalanced',
            'difference',
            'monthlySavings'
        ));
    }

    public function create(): View
    {
        return view('savings.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:payments,id',
            'amount' => 'required|numeric|min:0',
            'bank_balance' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'is_verified' => 'boolean',
        ]);

        $validated['is_verified'] = $request->has('is_verified');

        Saving::create($validated);

        return redirect()->route('savings.index')
            ->with('success', 'Data tabungan berhasil ditambahkan!');
    }

    public function show(Saving $saving): View
    {
        $saving->load(['payment.project.client']);
        return view('savings.show', compact('saving'));
    }

    public function edit(Saving $saving): View
    {
        return view('savings.edit', compact('saving'));
    }

    public function update(Request $request, Saving $saving): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'bank_balance' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'notes' => 'nullable|string',
            'is_verified' => 'boolean',
        ]);

        $validated['is_verified'] = $request->has('is_verified');

        $saving->update($validated);

        return redirect()->route('savings.show', $saving)
            ->with('success', 'Data tabungan berhasil diperbarui!');
    }

    public function destroy(Saving $saving): RedirectResponse
    {
        $saving->delete();

        return redirect()->route('savings.index')
            ->with('success', 'Data tabungan berhasil dihapus!');
    }

    public function verify(Saving $saving): JsonResponse
    {
        $saving->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Tabungan berhasil diverifikasi!',
            'saving' => $saving->load(['payment.project.client'])
        ]);
    }

    public function bulkVerify(): JsonResponse
    {
        $unverifiedSavings = Saving::where('is_verified', false)->get();
        $count = $unverifiedSavings->count();

        Saving::where('is_verified', false)->update(['is_verified' => true]);

        return response()->json([
            'success' => true,
            'message' => "Berhasil memverifikasi {$count} tabungan!",
            'verified_count' => $count
        ]);
    }

    public function updateBankBalance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bank_balance' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $latestSaving = Saving::latest('transaction_date')->first();

        if ($latestSaving) {
            Saving::create([
                'payment_id' => $latestSaving->payment_id,
                'amount' => 0,
                'bank_balance' => $validated['bank_balance'],
                'transaction_date' => Carbon::now(),
                'notes' => $validated['notes'] ?? 'Update saldo bank manual',
                'is_verified' => true
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Saldo bank berhasil diperbarui!',
            'new_balance' => $validated['bank_balance']
        ]);
    }

    public function checkBalance(): JsonResponse
    {
        $totalSavings = Saving::getTotalSavings();
        $currentBankBalance = Saving::getCurrentBankBalance();
        $isBalanced = Saving::isSavingsBalanced();
        $difference = Saving::getSavingsDifference();

        return response()->json([
            'total_savings' => $totalSavings,
            'current_bank_balance' => $currentBankBalance,
            'is_balanced' => $isBalanced,
            'difference' => $difference,
            'formatted_total_savings' => 'Rp ' . number_format($totalSavings, 0, ',', '.'),
            'formatted_bank_balance' => 'Rp ' . number_format($currentBankBalance, 0, ',', '.'),
            'formatted_difference' => 'Rp ' . number_format(abs($difference), 0, ',', '.')
        ]);
    }

    public function getSummary(): JsonResponse
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $monthlySavings = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $total = Saving::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $monthlySavings[] = [
                'month' => $date->format('M Y'),
                'total' => $total,
                'formatted_total' => 'Rp ' . number_format($total, 0, ',', '.')
            ];
        }

        $yearlySavings = Saving::whereYear('transaction_date', $currentYear)->sum('amount');
        $averageMonthlySavings = $yearlySavings / $currentMonth;

        $verifiedCount = Saving::where('is_verified', true)->count();
        $unverifiedCount = Saving::where('is_verified', false)->count();

        return response()->json([
            'monthly_savings' => $monthlySavings,
            'yearly_savings' => $yearlySavings,
            'average_monthly_savings' => $averageMonthlySavings,
            'verified_count' => $verifiedCount,
            'unverified_count' => $unverifiedCount,
            'formatted_yearly_savings' => 'Rp ' . number_format($yearlySavings, 0, ',', '.'),
            'formatted_average_monthly' => 'Rp ' . number_format($averageMonthlySavings, 0, ',', '.')
        ]);
    }
}
