<?php

namespace App\Http\Controllers;

use App\Models\CashWithdrawal;
use App\Models\BankBalance;
use App\Models\CashBalance;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CashWithdrawalController extends Controller
{
    public function index(Request $request): View
    {
        $query = CashWithdrawal::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('withdrawal_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('withdrawal_date', '<=', $request->date_to);
        }

        $withdrawals = $query->orderBy('withdrawal_date', 'desc')->paginate(15)->withQueryString();

        // Statistics
        $totalWithdrawals = CashWithdrawal::sum('amount');
        $monthlyWithdrawals = CashWithdrawal::whereYear('withdrawal_date', now()->year)
            ->whereMonth('withdrawal_date', now()->month)
            ->sum('amount');
        $withdrawalCount = CashWithdrawal::count();

        // Current balances
        $currentBankBalance = BankBalance::getCurrentBalance();
        $currentCashBalance = CashBalance::getCurrentBalance();
        $formattedBankBalance = 'Rp ' . number_format($currentBankBalance, 0, ',', '.');
        $formattedCashBalance = 'Rp ' . number_format($currentCashBalance, 0, ',', '.');

        return view('cash-withdrawals.index', compact(
            'withdrawals',
            'totalWithdrawals',
            'monthlyWithdrawals',
            'withdrawalCount',
            'currentBankBalance',
            'currentCashBalance',
            'formattedBankBalance',
            'formattedCashBalance'
        ));
    }

    public function create(): View
    {
        $currentBankBalance = BankBalance::getCurrentBalance();
        $currentCashBalance = CashBalance::getCurrentBalance();

        return view('cash-withdrawals.create', compact(
            'currentBankBalance',
            'currentCashBalance'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'withdrawal_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check bank balance
        $bankBalance = BankBalance::getCurrentBalance();
        if ($validated['amount'] > $bankBalance) {
            return back()->withErrors([
                'amount' => 'Saldo Bank Octo tidak mencukupi untuk penarikan ini.'
            ])->withInput();
        }

        CashWithdrawal::create($validated);

        return redirect()->route('cash-withdrawals.index')
            ->with('success', 'Penarikan cash berhasil dicatat!');
    }

    public function show(CashWithdrawal $cashWithdrawal): View
    {
        return view('cash-withdrawals.show', compact('cashWithdrawal'));
    }

    public function edit(CashWithdrawal $cashWithdrawal): View
    {
        $currentBankBalance = BankBalance::getCurrentBalance();
        $currentCashBalance = CashBalance::getCurrentBalance();

        return view('cash-withdrawals.edit', compact(
            'cashWithdrawal',
            'currentBankBalance',
            'currentCashBalance'
        ));
    }

    public function update(Request $request, CashWithdrawal $cashWithdrawal): RedirectResponse
    {
        $validated = $request->validate([
            'withdrawal_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check bank balance (exclude current withdrawal amount)
        $bankBalance = BankBalance::getCurrentBalance() + $cashWithdrawal->amount;
        if ($validated['amount'] > $bankBalance) {
            return back()->withErrors([
                'amount' => 'Saldo Bank Octo tidak mencukupi untuk jumlah penarikan ini.'
            ])->withInput();
        }

        $cashWithdrawal->update($validated);

        return redirect()->route('cash-withdrawals.show', $cashWithdrawal)
            ->with('success', 'Penarikan cash berhasil diperbarui!');
    }

    public function destroy(CashWithdrawal $cashWithdrawal): RedirectResponse
    {
        $cashWithdrawal->delete();

        return redirect()->route('cash-withdrawals.index')
            ->with('success', 'Penarikan cash berhasil dihapus!');
    }
}
