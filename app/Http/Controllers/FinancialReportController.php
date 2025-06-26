<?php

namespace App\Http\Controllers;

use App\Models\BankTransfer;
use App\Models\Expense;
use App\Models\GoldTransaction;
use App\Models\BankBalance;
use App\Models\Payment;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        // Profit & Loss Report
        $profitLoss = $this->generateProfitLossReport($startDate, $endDate);

        // Balance Sheet
        $balanceSheet = $this->generateBalanceSheet();

        // Gold Portfolio
        $goldPortfolio = $this->generateGoldPortfolio();

        return view('financial-reports.index', compact(
            'profitLoss',
            'balanceSheet',
            'goldPortfolio',
            'startDate',
            'endDate'
        ));
    }

    private function generateProfitLossReport(string $startDate, string $endDate): array
    {
        // PENDAPATAN (yang sudah masuk ke Bank Octo)
        $transferIncome = BankTransfer::whereBetween('transfer_date', [$startDate, $endDate])
            ->sum('transfer_amount');

        $goldSales = GoldTransaction::sell()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_price');

        $totalIncome = $transferIncome + $goldSales;

        // PENGELUARAN by Category
        $expenses = Expense::whereBetween('expense_date', [$startDate, $endDate])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->category => $item->total];
            });

        $totalExpenses = $expenses->sum();

        // INVESTASI
        $goldInvestment = GoldTransaction::buy()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_price');

        $totalExpensesAndInvestment = $totalExpenses + $goldInvestment;

        // LABA/RUGI
        $operationalProfit = $totalIncome - $totalExpensesAndInvestment;

        // Bank Octo Balance at end of period
        $bankBalance = BankBalance::getCurrentBalance();

        return [
            'income' => [
                'transfer_from_payments' => $transferIncome,
                'gold_sales' => $goldSales,
                'other_income' => 0, // Placeholder for future
                'total_income' => $totalIncome,
            ],
            'expenses' => [
                'operasional' => $expenses->get('OPERASIONAL', 0),
                'marketing' => $expenses->get('MARKETING', 0),
                'pengembangan' => $expenses->get('PENGEMBANGAN', 0),
                'gaji_freelance' => $expenses->get('GAJI_FREELANCE', 0),
                'entertainment' => $expenses->get('ENTERTAINMENT', 0),
                'lain_lain' => $expenses->get('LAIN_LAIN', 0),
                'total_expenses' => $totalExpenses,
            ],
            'investment' => [
                'gold_purchase' => $goldInvestment,
                'total_expenses_and_investment' => $totalExpensesAndInvestment,
            ],
            'result' => [
                'operational_profit' => $operationalProfit,
                'bank_balance_end_period' => $bankBalance,
            ]
        ];
    }

    private function generateBalanceSheet(): array
    {
        // ASET
        $bankBalance = BankBalance::getCurrentBalance();

        // Gold Portfolio
        $totalBoughtGrams = GoldTransaction::buy()->sum('grams');
        $totalSoldGrams = GoldTransaction::sell()->sum('grams');
        $currentGrams = $totalBoughtGrams - $totalSoldGrams;
        $totalGoldInvestment = GoldTransaction::buy()->sum('total_price');
        $averageGoldPrice = $totalBoughtGrams > 0 ? $totalGoldInvestment / $totalBoughtGrams : 0;
        $goldValue = $currentGrams * $averageGoldPrice;

        $totalAssets = $bankBalance + $goldValue;

        // PIUTANG
        $untransferredPayments = Payment::where('is_transferred', false)->sum('amount');
        $remainingProjectValues = Project::whereIn('status', ['WAITING', 'PROGRESS'])
            ->sum(DB::raw('total_value - paid_amount'));

        $totalReceivables = $untransferredPayments + $remainingProjectValues;

        // NET WORTH
        $netWorth = $totalAssets + $totalReceivables;

        return [
            'assets' => [
                'bank_octo_balance' => $bankBalance,
                'gold_investment' => [
                    'grams' => $currentGrams,
                    'average_price' => $averageGoldPrice,
                    'total_value' => $goldValue,
                ],
                'total_assets' => $totalAssets,
            ],
            'receivables' => [
                'untransferred_payments' => $untransferredPayments,
                'remaining_project_values' => $remainingProjectValues,
                'total_receivables' => $totalReceivables,
            ],
            'net_worth' => $netWorth,
        ];
    }

    private function generateGoldPortfolio(): array
    {
        $transactions = GoldTransaction::orderBy('transaction_date', 'desc')->get();

        $totalBoughtGrams = GoldTransaction::buy()->sum('grams');
        $totalSoldGrams = GoldTransaction::sell()->sum('grams');
        $currentGrams = $totalBoughtGrams - $totalSoldGrams;

        $totalInvestment = GoldTransaction::buy()->sum('total_price');
        $averageBuyPrice = $totalBoughtGrams > 0 ? $totalInvestment / $totalBoughtGrams : 0;

        // Calculate running balance for each transaction
        $runningGrams = 0;
        $transactionHistory = $transactions->reverse()->map(function ($transaction) use (&$runningGrams) {
            if ($transaction->type === 'BUY') {
                $runningGrams += $transaction->grams;
            } else {
                $runningGrams -= $transaction->grams;
            }

            return [
                'date' => $transaction->transaction_date->format('d M Y'),
                'type' => $transaction->type_label,
                'grams' => $transaction->grams,
                'total_price' => $transaction->total_price,
                'formatted_total_price' => $transaction->formatted_total_price,
                'running_grams' => $runningGrams,
                'notes' => $transaction->notes,
            ];
        })->reverse();

        return [
            'summary' => [
                'total_grams' => $currentGrams,
                'average_buy_price' => $averageBuyPrice,
                'total_investment' => $totalInvestment,
                'current_value' => $currentGrams * $averageBuyPrice,
            ],
            'transactions' => $transactionHistory,
        ];
    }

    public function exportProfitLoss(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->toDateString());

        $profitLoss = $this->generateProfitLossReport($startDate, $endDate);

        // Return JSON for now, can be extended to PDF/Excel
        return response()->json([
            'period' => "$startDate to $endDate",
            'report' => $profitLoss,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    public function exportBalanceSheet()
    {
        $balanceSheet = $this->generateBalanceSheet();

        // Return JSON for now, can be extended to PDF/Excel
        return response()->json([
            'as_of_date' => now()->format('Y-m-d'),
            'report' => $balanceSheet,
            'generated_at' => now()->format('Y-m-d H:i:s')
        ]);
    }

    public function dashboard(): View
    {
        // Enhanced dashboard with financial overview
        $currentMonth = Carbon::now();
        $startOfMonth = $currentMonth->copy()->startOfMonth()->toDateString();
        $endOfMonth = $currentMonth->copy()->endOfMonth()->toDateString();

        // Current balances
        $bankBalance = BankBalance::getCurrentBalance();
        $goldPortfolio = $this->generateGoldPortfolio();
        $netWorth = $bankBalance + $goldPortfolio['summary']['current_value'];

        // Monthly performance
        $monthlyProfit = $this->generateProfitLossReport($startOfMonth, $endOfMonth);

        // Quick stats
        $untransferredAmount = Payment::where('is_transferred', false)->sum('amount');
        $monthlyExpensesByCategory = Expense::whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get()
            ->mapWithKeys(function ($item) {
                return [Expense::CATEGORIES[$item->category] => $item->total];
            });

        return view('financial-reports.dashboard', compact(
            'bankBalance',
            'goldPortfolio',
            'netWorth',
            'monthlyProfit',
            'untransferredAmount',
            'monthlyExpensesByCategory'
        ));
    }
}
