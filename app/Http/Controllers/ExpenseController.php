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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        $expenses = $query->orderBy('expense_date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

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

    /**
     * Export expenses to Excel
     */
    public function exportExcel(Request $request): StreamedResponse
    {
        // Get filtered data based on request parameters
        $query = Expense::query();

        // Apply same filters as index method
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        if ($request->filled('source')) {
            $query->bySource($request->source);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('expense_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('expense_date', '<=', $request->date_to);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->get();

        // Create spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pengeluaran');

        // Set headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Tanggal',
            'C1' => 'Sumber',
            'D1' => 'Kategori',
            'E1' => 'Deskripsi',
            'F1' => 'Jumlah',
            'G1' => 'Bulan',
            'H1' => 'Tahun',
            'I1' => 'Hari'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'EF4444']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        // Auto-size columns
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Set row height for header
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Add data
        $row = 2;
        foreach ($expenses as $index => $expense) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $expense->expense_date->format('d/m/Y'));
            $sheet->setCellValue('C' . $row, $expense->source_label);
            $sheet->setCellValue('D' . $row, $expense->category_label);
            $sheet->setCellValue('E' . $row, $expense->description);
            $sheet->setCellValue('F' . $row, $expense->amount);
            $sheet->setCellValue('G' . $row, $expense->expense_date->format('F')); // Full month name
            $sheet->setCellValue('H' . $row, $expense->expense_date->format('Y'));
            $sheet->setCellValue('I' . $row, $expense->expense_date->format('l')); // Full day name

            // Format currency column
            $sheet->getStyle('F' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Color code source
            $sourceColor = match ($expense->source) {
                Expense::SOURCE_BANK => 'CCE5FF',
                Expense::SOURCE_CASH => 'D4EDDA',
                default => 'FFFFFF'
            };

            $sheet->getStyle('C' . $row)->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB($sourceColor);

            // Apply borders to data rows
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()
                ->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            $row++;
        }

        // Add summary at the bottom
        $summaryRow = $row + 2;
        $sheet->setCellValue('A' . $summaryRow, 'RINGKASAN:');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Transaksi:');
        $sheet->setCellValue('B' . $summaryRow, $expenses->count());

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Total Pengeluaran:');
        $sheet->setCellValue('B' . $summaryRow, $expenses->sum('amount'));
        $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

        // Summary by source
        $bankExpenses = $expenses->where('source', Expense::SOURCE_BANK)->sum('amount');
        $cashExpenses = $expenses->where('source', Expense::SOURCE_CASH)->sum('amount');

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Pengeluaran Bank:');
        $sheet->setCellValue('B' . $summaryRow, $bankExpenses);
        $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

        $summaryRow++;
        $sheet->setCellValue('A' . $summaryRow, 'Pengeluaran Cash:');
        $sheet->setCellValue('B' . $summaryRow, $cashExpenses);
        $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');

        // Summary by category (top 5)
        $topCategories = $expenses->groupBy('category')
            ->map(function ($group) {
                return [
                    'label' => $group->first()->category_label,
                    'total' => $group->sum('amount')
                ];
            })
            ->sortByDesc('total')
            ->take(5);

        $summaryRow += 2;
        $sheet->setCellValue('A' . $summaryRow, 'TOP 5 KATEGORI:');
        $sheet->getStyle('A' . $summaryRow)->getFont()->setBold(true);

        foreach ($topCategories as $category) {
            $summaryRow++;
            $sheet->setCellValue('A' . $summaryRow, $category['label'] . ':');
            $sheet->setCellValue('B' . $summaryRow, $category['total']);
            $sheet->getStyle('B' . $summaryRow)->getNumberFormat()->setFormatCode('#,##0');
        }

        // Generate filename with current date and filters
        $filename = 'Data_Pengeluaran_' . date('Y-m-d');
        if ($request->filled('source')) {
            $filename .= '_' . $request->source;
        }
        if ($request->filled('category')) {
            $filename .= '_' . substr($request->category, 0, 15);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $filename .= '_' . $request->date_from . '_to_' . $request->date_to;
        }
        $filename .= '.xlsx';

        // Return as download
        return new StreamedResponse(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ]);
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

    public function analysis(Request $request): View
    {
        $year = $request->get('year', Carbon::now()->year);
        $compareYear = $year - 1;
        $selectedCategory = $request->get('category', null);
        $periodStart = $request->get('period_start', null);
        $periodEnd = $request->get('period_end', null);

        // Data bulanan tahun ini (EXCLUDE SALDO_AWAL)
        $monthlyExpenses = [];

        for ($month = 1; $month <= 12; $month++) {
            $query = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->where('category', '!=', 'SALDO_AWAL');

            if ($selectedCategory) {
                $query->where('category', $selectedCategory);
            }

            $total = $query->sum('amount');

            $monthlyExpenses[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month)->format('M'),
                'total' => $total
            ];
        }

        // Perbandingan dengan tahun lalu
        $comparisonData = [];
        for ($month = 1; $month <= 12; $month++) {
            $thisYearQuery = Expense::whereYear('expense_date', $year)
                ->whereMonth('expense_date', $month)
                ->where('category', '!=', 'SALDO_AWAL');

            $lastYearQuery = Expense::whereYear('expense_date', $compareYear)
                ->whereMonth('expense_date', $month)
                ->where('category', '!=', 'SALDO_AWAL');

            if ($selectedCategory) {
                $thisYearQuery->where('category', $selectedCategory);
                $lastYearQuery->where('category', $selectedCategory);
            }

            $thisYear = $thisYearQuery->sum('amount');
            $lastYear = $lastYearQuery->sum('amount');

            $comparisonData[] = [
                'month' => $month,
                'month_name' => Carbon::create($year, $month)->format('M'),
                'this_year' => $thisYear,
                'last_year' => $lastYear,
                'difference' => $thisYear - $lastYear,
                'percentage' => $lastYear > 0 ? (($thisYear - $lastYear) / $lastYear) * 100 : 0
            ];
        }

        // Top 10 Kategori tahun ini
        $topCategories = Expense::selectRaw('category, SUM(amount) as total')
            ->whereYear('expense_date', $year)
            ->where('category', '!=', 'SALDO_AWAL')
            ->groupBy('category')
            ->orderByDesc('total')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'label' => Expense::CATEGORIES[$item->category] ?? $item->category,
                    'total' => $item->total
                ];
            });

        // Detail transaksi berdasarkan filter periode dan kategori
        $detailTransactions = null;
        if ($selectedCategory && $periodStart && $periodEnd) {
            $detailTransactions = Expense::where('category', $selectedCategory)
                ->whereBetween('expense_date', [$periodStart, $periodEnd])
                ->orderByDesc('amount')
                ->get();
        }

        // Statistik summary
        $statsQuery = Expense::whereYear('expense_date', $year)->where('category', '!=', 'SALDO_AWAL');
        if ($selectedCategory) {
            $statsQuery->where('category', $selectedCategory);
        }

        $stats = [
            'total_year' => $statsQuery->sum('amount'),
            'total_last_year' => Expense::whereYear('expense_date', $compareYear)
                ->where('category', '!=', 'SALDO_AWAL')
                ->when($selectedCategory, fn($q) => $q->where('category', $selectedCategory))
                ->sum('amount'),
            'avg_monthly' => $statsQuery->sum('amount') / 12,
            'highest_month' => collect($monthlyExpenses)->sortByDesc('total')->first(),
            'lowest_month' => collect($monthlyExpenses)->where('total', '>', 0)->sortBy('total')->first(),
            'total_transactions' => $statsQuery->count(),
        ];

        // Daftar tahun yang tersedia
        $availableYears = Expense::selectRaw('YEAR(expense_date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        // List kategori untuk filter
        $categories = Expense::CATEGORIES;

        return view('expenses.analysis', compact(
            'year',
            'compareYear',
            'monthlyExpenses',
            'comparisonData',
            'topCategories',
            'stats',
            'availableYears',
            'categories',
            'selectedCategory',
            'periodStart',
            'periodEnd',
            'detailTransactions'
        ));
    }
}
