<?php

namespace App\Http\Controllers;

use App\Models\GoldTransaction;
use App\Models\BankBalance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GoldTransactionController extends Controller
{
    public function index(): View
    {
        $transactions = GoldTransaction::orderBy('transaction_date', 'desc')->paginate(15);

        // Gold portfolio statistics
        $totalBoughtGrams = GoldTransaction::buy()->sum('grams');
        $totalSoldGrams = GoldTransaction::sell()->sum('grams');
        $currentGrams = $totalBoughtGrams - $totalSoldGrams;

        $totalInvestment = GoldTransaction::buy()->sum('total_price');
        $totalSales = GoldTransaction::sell()->sum('total_price');

        $averageBuyPrice = $totalBoughtGrams > 0 ? $totalInvestment / $totalBoughtGrams : 0;

        $currentValue = $currentGrams * $averageBuyPrice;

        return view('gold.index', compact(
            'transactions',
            'currentGrams',
            'totalInvestment',
            'averageBuyPrice',
            'currentValue'
        ));
    }

    public function create(Request $request): View
    {
        $type = $request->get('type', 'BUY');
        $currentBalance = BankBalance::getCurrentBalance();

        return view('gold.create', compact('type', 'currentBalance'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'transaction_date' => 'required|date|before_or_equal:today',
            'type' => 'required|in:BUY,SELL',
            'grams' => 'required|numeric|min:0.001|max:999.999',
            'total_price' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        // Additional validations
        if ($validated['type'] === 'BUY') {
            $currentBalance = BankBalance::getCurrentBalance();
            if ($validated['total_price'] > $currentBalance) {
                return back()->withErrors(['total_price' => 'Saldo Bank Octo tidak mencukupi!'])->withInput();
            }
        }

        if ($validated['type'] === 'SELL') {
            $currentGrams = GoldTransaction::buy()->sum('grams') - GoldTransaction::sell()->sum('grams');
            if ($validated['grams'] > $currentGrams) {
                return back()->withErrors(['grams' => 'Stok emas tidak mencukupi!'])->withInput();
            }
        }

        GoldTransaction::create($validated);

        // Update bank balance
        BankBalance::updateBalance();

        $action = $validated['type'] === 'BUY' ? 'pembelian' : 'penjualan';
        return redirect()->route('gold.index')
            ->with('success', "Transaksi {$action} emas berhasil dicatat!");
    }

    public function destroy(GoldTransaction $goldTransaction): RedirectResponse
    {
        $goldTransaction->delete();

        // Update bank balance
        BankBalance::updateBalance();

        return redirect()->route('gold.index')
            ->with('success', 'Transaksi emas berhasil dihapus!');
    }

    public function getPortfolio(): JsonResponse
    {
        $totalBoughtGrams = GoldTransaction::buy()->sum('grams');
        $totalSoldGrams = GoldTransaction::sell()->sum('grams');
        $currentGrams = $totalBoughtGrams - $totalSoldGrams;

        $totalInvestment = GoldTransaction::buy()->sum('total_price');
        $averageBuyPrice = $totalBoughtGrams > 0 ? $totalInvestment / $totalBoughtGrams : 0;

        return response()->json([
            'current_grams' => $currentGrams,
            'average_buy_price' => $averageBuyPrice,
            'total_investment' => $totalInvestment,
            'current_value' => $currentGrams * $averageBuyPrice,
            'formatted_current_grams' => number_format($currentGrams, 3),
            'formatted_average_buy_price' => 'Rp ' . number_format($averageBuyPrice, 0, ',', '.'),
            'formatted_total_investment' => 'Rp ' . number_format($totalInvestment, 0, ',', '.'),
            'formatted_current_value' => 'Rp ' . number_format($currentGrams * $averageBuyPrice, 0, ',', '.'),
        ]);
    }
}
