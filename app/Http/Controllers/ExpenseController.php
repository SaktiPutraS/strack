<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\BankBalance;
use App\Models\CashBalance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Expense::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->bySource($request->source);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();

        // Data untuk grafik - Bulan ini by Category
        $monthlyExpensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                $categoryLabel = isset(Expense::CATEGORIES[$item->category])
                    ? Expense::CATEGORIES[$item->category]
                    : $item->category;
                return [$categoryLabel => $item->total];
            });

        // Data untuk grafik - Bulan ini by Source
        $monthlyExpensesBySource = Expense::selectRaw('source, SUM(amount) as total')
            ->whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->groupBy('source')
            ->get()
            ->mapWithKeys(function ($item) {
                $sourceLabel = isset(Expense::SOURCES[$item->source])
                    ? Expense::SOURCES[$item->source]
                    : $item->source;
                return [$sourceLabel => $item->total];
            });

        // Data untuk grafik - Tahun ini by Category
        $yearlyExpensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('expense_date', Carbon::now()->year)
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                // Cek apakah category ada di CATEGORIES konstanta
                $categoryLabel = isset(Expense::CATEGORIES[$item->category])
                    ? Expense::CATEGORIES[$item->category]
                    : $item->category;
                return [$categoryLabel => $item->total];
            });

        // Current balances
        $currentBankBalance = BankBalance::getCurrentBalance();
        $currentCashBalance = CashBalance::getCurrentBalance();
        $formattedBankBalance = 'Rp ' . number_format($currentBankBalance, 0, ',', '.');
        $formattedCashBalance = 'Rp ' . number_format($currentCashBalance, 0, ',', '.');

        return view('expenses.index', compact(
            'expenses',
            'monthlyExpensesByCategory',
            'yearlyExpensesByCategory',
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

        return view('expenses.create', compact(
            'currentBankBalance',
            'currentCashBalance'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'source' => 'required|in:' . implode(',', array_keys(Expense::SOURCES)),
            'description' => 'required|string|max:500',
        ]);

        // Check balance based on source
        if ($validated['source'] === Expense::SOURCE_BANK) {
            $balance = BankBalance::getCurrentBalance();
            $balanceType = 'Bank Octo';
        } else {
            $balance = CashBalance::getCurrentBalance();
            $balanceType = 'Cash';
        }

        if ($validated['amount'] > $balance) {
            return back()->withErrors([
                'amount' => "Saldo {$balanceType} tidak mencukupi untuk pengeluaran ini."
            ])->withInput();
        }

        Expense::create($validated);

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function show(Expense $expense): View
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        $currentBankBalance = BankBalance::getCurrentBalance();
        $currentCashBalance = CashBalance::getCurrentBalance();

        return view('expenses.edit', compact(
            'expense',
            'currentBankBalance',
            'currentCashBalance'
        ));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'source' => 'required|in:' . implode(',', array_keys(Expense::SOURCES)),
            'description' => 'required|string|max:500',
        ]);

        // Check balance based on new source (add back current expense amount)
        if ($validated['source'] === Expense::SOURCE_BANK) {
            $balance = BankBalance::getCurrentBalance();
            if ($expense->source === Expense::SOURCE_BANK) {
                $balance += $expense->amount; // Add back current amount if same source
            }
            $balanceType = 'Bank Octo';
        } else {
            $balance = CashBalance::getCurrentBalance();
            if ($expense->source === Expense::SOURCE_CASH) {
                $balance += $expense->amount; // Add back current amount if same source
            }
            $balanceType = 'Cash';
        }

        if ($validated['amount'] > $balance) {
            return back()->withErrors([
                'amount' => "Saldo {$balanceType} tidak mencukupi untuk pengeluaran ini."
            ])->withInput();
        }

        $expense->update($validated);

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus!');
    }

    public function getBalances(): JsonResponse
    {
        return response()->json([
            'bank_balance' => BankBalance::getCurrentBalance(),
            'cash_balance' => CashBalance::getCurrentBalance(),
            'formatted_bank_balance' => 'Rp ' . number_format(BankBalance::getCurrentBalance(), 0, ',', '.'),
            'formatted_cash_balance' => 'Rp ' . number_format(CashBalance::getCurrentBalance(), 0, ',', '.'),
        ]);
    }
}
