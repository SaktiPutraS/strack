<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\BankBalance;
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

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15)->withQueryString();

        // Data untuk grafik - Bulan ini
        $monthlyExpensesByCategory = Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('expense_date', Carbon::now()->year)
            ->whereMonth('expense_date', Carbon::now()->month)
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                // Cek apakah category ada di CATEGORIES konstanta
                $categoryLabel = isset(Expense::CATEGORIES[$item->category])
                    ? Expense::CATEGORIES[$item->category]
                    : $item->category;
                return [$categoryLabel => $item->total];
            });

        // Data untuk grafik - Tahun ini
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

        return view('expenses.index', compact(
            'expenses',
            'monthlyExpensesByCategory',
            'yearlyExpensesByCategory'
        ));
    }

    public function create(): View
    {
        return view('expenses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'description' => 'required|string|max:500',
        ]);

        Expense::create($validated);

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan!');
    }

    public function show(Expense $expense): View
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense): View
    {
        return view('expenses.edit', compact('expense'));
    }

    public function update(Request $request, Expense $expense): RedirectResponse
    {
        $validated = $request->validate([
            'expense_date' => 'required|date|before_or_equal:today',
            'amount' => 'required|numeric|min:1',
            'category' => 'required|in:' . implode(',', array_keys(Expense::CATEGORIES)),
            'description' => 'required|string|max:500',
        ]);

        $expense->update($validated);

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('expenses.show', $expense)
            ->with('success', 'Pengeluaran berhasil diperbarui!');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus!');
    }
}
