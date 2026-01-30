<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use App\Services\BudgetExcelService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        // Menentukan 3 bulan: bulan lalu, sekarang, dan bulan depan
        $currentMonth = (int) date('n');
        $currentYear = (int) date('Y');

        // Hitung bulan lalu
        $lastMonth = $currentMonth - 1;
        $lastMonthYear = $currentYear;
        if ($lastMonth < 1) {
            $lastMonth = 12;
            $lastMonthYear = $currentYear - 1;
        }

        // Hitung bulan depan
        $nextMonth = $currentMonth + 1;
        $nextMonthYear = $currentYear;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextMonthYear = $currentYear + 1;
        }

        // Array untuk filter 3 bulan
        $targetMonths = [
            ['month' => $lastMonth, 'year' => $lastMonthYear],
            ['month' => $currentMonth, 'year' => $currentYear],
            ['month' => $nextMonth, 'year' => $nextMonthYear],
        ];

        // Query budgets untuk 3 bulan tersebut
        $budgets = Budget::with('items')
            ->where(function ($query) use ($targetMonths) {
                foreach ($targetMonths as $target) {
                    $query->orWhere(function ($q) use ($target) {
                        $q->where('month', $target['month'])
                          ->where('year', $target['year']);
                    });
                }
            })
            ->get();

        // Urutkan: bulan termuda dengan status belum selesai dulu
        $budgets = $budgets->sortBy(function ($budget) use ($currentMonth, $currentYear) {
            // Hitung prioritas berdasarkan status (belum selesai = 0, selesai = 1)
            $statusPriority = $budget->is_fully_completed ? 1 : 0;

            // Hitung jarak bulan dari bulan sekarang (bulan sekarang = 0)
            $monthsFromNow = (($budget->year - $currentYear) * 12) + ($budget->month - $currentMonth);

            // Format: status_priority * 1000 + abs(monthsFromNow)
            // Ini akan mengurutkan: belum selesai dulu, lalu berdasarkan kedekatan dengan bulan sekarang
            return $statusPriority * 1000 + abs($monthsFromNow);
        })->values();

        // Hitung tahun yang dipilih untuk stats (gunakan tahun sekarang)
        $selectedYear = $currentYear;

        // Dapatkan budget bulan ini
        $currentBudget = Budget::where('month', $currentMonth)
            ->where('year', $currentYear)
            ->with('items')
            ->first();

        // Stats untuk tahun ini dan bulan ini
        $stats = [
            'total_year' => Budget::byYear($selectedYear)->sum('total_budget'),
            'total_budgets' => Budget::byYear($selectedYear)->count(),
            'completed_budgets' => Budget::byYear($selectedYear)
                ->get()
                ->filter(fn($b) => $b->is_fully_completed)
                ->count(),
            'avg_budget' => Budget::byYear($selectedYear)->count() > 0
                ? Budget::byYear($selectedYear)->avg('total_budget')
                : 0,
            // Stats bulan ini
            'current_month_name' => $currentBudget ? $currentBudget->month_name : date('F Y'),
            'current_month_budget' => $currentBudget ? $currentBudget->total_budget : 0,
            'current_month_progress' => $currentBudget ? $currentBudget->progress_percentage : 0,
            'current_month_total_items' => $currentBudget ? $currentBudget->total_items_count : 0,
            'current_month_completed_items' => $currentBudget ? $currentBudget->completed_items_count : 0,
        ];

        // Available years untuk link ke laporan
        $availableYears = Budget::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        return view('budgets.index', compact('budgets', 'stats', 'selectedYear', 'availableYears'));
    }

    public function create(): View
    {
        // Get months that already have budget for current year
        $currentYear = date('Y');
        $usedMonths = Budget::where('year', $currentYear)->pluck('month')->toArray();

        return view('budgets.create', compact('usedMonths', 'currentYear'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.estimated_amount' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
            'items.*.category' => 'nullable|string|max:255',
        ], [
            'items.required' => 'Minimal harus ada 1 item pengeluaran',
            'items.min' => 'Minimal harus ada 1 item pengeluaran',
        ]);

        // Check if budget already exists
        $exists = Budget::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'month' => 'Budget untuk bulan dan tahun ini sudah ada!'
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Create budget
            $budget = Budget::create([
                'month' => $validated['month'],
                'year' => $validated['year'],
                'notes' => $validated['notes'] ?? null,
                'total_budget' => 0,
            ]);

            // Create budget items
            foreach ($validated['items'] as $itemData) {
                BudgetItem::create([
                    'budget_id' => $budget->id,
                    'category' => $itemData['category'] ?? null,
                    'item_name' => $itemData['item_name'],
                    'estimated_amount' => $itemData['estimated_amount'],
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }

            // Update total budget
            $budget->updateTotalBudget();

            DB::commit();

            return redirect()->route('budgets.show', $budget)
                ->with('success', 'Budget berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan budget'])
                ->withInput();
        }
    }

    public function show(Budget $budget): View
    {
        $budget->load(['items' => function ($query) {
            $query->orderBy('is_completed', 'asc')
                ->orderBy('id', 'asc');
        }]);

        // Dapatkan budget bulan sebelumnya
        $prevMonth = $budget->month - 1;
        $prevYear = $budget->year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear = $budget->year - 1;
        }
        $prevBudget = Budget::where('month', $prevMonth)
            ->where('year', $prevYear)
            ->first();

        // Dapatkan budget bulan berikutnya
        $nextMonth = $budget->month + 1;
        $nextYear = $budget->year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear = $budget->year + 1;
        }
        $nextBudget = Budget::where('month', $nextMonth)
            ->where('year', $nextYear)
            ->first();

        // Dapatkan semua kategori dari semua budget untuk suggestion
        $allCategories = BudgetItem::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('budgets.show', compact('budget', 'prevBudget', 'nextBudget', 'allCategories'));
    }

    public function edit(Budget $budget): View
    {
        $budget->load('items');

        // Get used months except current budget month
        $usedMonths = Budget::where('year', $budget->year)
            ->where('id', '!=', $budget->id)
            ->pluck('month')
            ->toArray();

        return view('budgets.edit', compact('budget', 'usedMonths'));
    }

    public function update(Request $request, Budget $budget): RedirectResponse
    {
        $validated = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.id' => 'nullable|exists:budget_items,id',
            'items.*.category' => 'nullable|string|max:255',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.estimated_amount' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:500',
        ]);

        // Check if budget already exists (except current)
        $exists = Budget::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->where('id', '!=', $budget->id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'month' => 'Budget untuk bulan dan tahun ini sudah ada!'
            ])->withInput();
        }

        DB::beginTransaction();
        try {
            // Update budget
            $budget->update([
                'month' => $validated['month'],
                'year' => $validated['year'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Track existing item IDs
            $existingItemIds = collect($validated['items'])
                ->pluck('id')
                ->filter()
                ->toArray();

            // Delete items that are not in the request
            $budget->items()
                ->whereNotIn('id', $existingItemIds)
                ->delete();

            // Update or create items
            foreach ($validated['items'] as $itemData) {
                if (isset($itemData['id'])) {
                    // Update existing item
                    $item = BudgetItem::find($itemData['id']);
                    if ($item && $item->budget_id == $budget->id) {
                        $item->update([
                            'category' => $itemData['category'] ?? null,
                            'item_name' => $itemData['item_name'],
                            'estimated_amount' => $itemData['estimated_amount'],
                            'notes' => $itemData['notes'] ?? null,
                        ]);
                    }
                } else {
                    // Create new item
                    BudgetItem::create([
                        'budget_id' => $budget->id,
                        'category' => $itemData['category'] ?? null,
                        'item_name' => $itemData['item_name'],
                        'estimated_amount' => $itemData['estimated_amount'],
                        'notes' => $itemData['notes'] ?? null,
                    ]);
                }
            }

            // Update total budget
            $budget->updateTotalBudget();

            DB::commit();

            return redirect()->route('budgets.show', $budget)
                ->with('success', 'Budget berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memperbarui budget'])
                ->withInput();
        }
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        $budget->delete();

        return redirect()->route('budgets.index')
            ->with('success', 'Budget berhasil dihapus!');
    }

    // Toggle item completion
    public function toggleItemComplete(BudgetItem $budgetItem): JsonResponse
    {
        try {
            $budgetItem->toggleComplete();

            return response()->json([
                'success' => true,
                'is_completed' => $budgetItem->is_completed,
                'completed_at' => $budgetItem->completed_date,
                'message' => $budgetItem->is_completed
                    ? 'Item ditandai selesai!'
                    : 'Item ditandai belum selesai!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status item',
            ], 500);
        }
    }

    // Bulk toggle completion
    public function bulkToggleComplete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_ids' => 'required|array|min:1',
                'item_ids.*' => 'exists:budget_items,id',
                'mark_complete' => 'required|boolean',
            ]);

            $count = 0;
            foreach ($validated['item_ids'] as $itemId) {
                $item = BudgetItem::find($itemId);
                if ($item) {
                    $item->is_completed = $validated['mark_complete'];
                    $item->completed_at = $validated['mark_complete'] ? now() : null;
                    $item->save();
                    $count++;
                }
            }

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $validated['mark_complete']
                    ? "{$count} item ditandai selesai!"
                    : "{$count} item ditandai belum selesai!",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengubah status item',
            ], 500);
        }
    }

    // Update single item
    public function updateItem(Request $request, BudgetItem $budgetItem): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_name' => 'required|string|max:255',
                'estimated_amount' => 'required|numeric|min:0',
                'notes' => 'nullable|string|max:500',
                'category' => 'nullable|string|max:255',
            ]);

            $budgetItem->update($validated);

            return response()->json([
                'success' => true,
                'item' => [
                    'id' => $budgetItem->id,
                    'item_name' => $budgetItem->item_name,
                    'estimated_amount' => $budgetItem->estimated_amount,
                    'formatted_amount' => $budgetItem->formatted_amount,
                    'notes' => $budgetItem->notes,
                    'category' => $budgetItem->category,
                ],
                'message' => 'Item berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memperbarui item',
            ], 500);
        }
    }

    // Toggle category completion
    public function toggleCategoryComplete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'budget_id' => 'required|exists:budgets,id',
                'category' => 'required|string',
                'mark_complete' => 'required|boolean',
            ]);

            $category = $validated['category'] === 'Tanpa Kategori' ? null : $validated['category'];

            $query = BudgetItem::where('budget_id', $validated['budget_id']);

            if ($category === null) {
                $query->whereNull('category');
            } else {
                $query->where('category', $category);
            }

            $count = $query->update([
                'is_completed' => $validated['mark_complete'],
                'completed_at' => $validated['mark_complete'] ? now() : null,
            ]);

            // Update budget total
            $budget = Budget::find($validated['budget_id']);
            if ($budget) {
                $budget->updateTotalBudget();
            }

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => $validated['mark_complete']
                    ? "Kategori ditandai selesai! ({$count} item)"
                    : "Kategori dibatalkan! ({$count} item)",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function report(Request $request, $year = null): View
    {
        $selectedYear = $year ?? Budget::max('year') ?? date('Y');

        // Get all budgets for the year
        $budgets = Budget::with('items')
            ->byYear($selectedYear)
            ->orderBy('month')
            ->get();

        // Overall Statistics
        $stats = [
            'total_budgets' => $budgets->count(),
            'total_budget_year' => $budgets->sum('total_budget'),
            'avg_budget_month' => $budgets->avg('total_budget'),
            'highest_budget' => $budgets->max('total_budget'),
            'lowest_budget' => $budgets->min('total_budget'),
            'total_items' => $budgets->sum(fn($b) => $b->items->count()),
            'completed_items' => $budgets->sum(fn($b) => $b->completed_items_count),
            'completion_rate' => $budgets->sum(fn($b) => $b->items->count()) > 0
                ? round(($budgets->sum(fn($b) => $b->completed_items_count) / $budgets->sum(fn($b) => $b->items->count())) * 100, 1)
                : 0,
        ];

        // Monthly data for chart
        $monthlyData = $budgets->map(function ($budget) {
            return [
                'month' => $budget->month,
                'month_name' => $budget->month_name,
                'total' => $budget->total_budget,
                'items_count' => $budget->items->count(),
                'completed_count' => $budget->completed_items_count,
                'completion_rate' => $budget->progress_percentage,
            ];
        });

        // All items untuk analisis
        $allItems = $budgets->flatMap(fn($b) => $b->items);

        // Group by item name (same items across months)
        $itemGroups = $allItems->groupBy('item_name')->map(function ($items, $name) {
            return [
                'name' => $name,
                'count' => $items->count(),
                'total' => $items->sum('estimated_amount'),
                'avg' => $items->avg('estimated_amount'),
                'completed' => $items->where('is_completed', true)->count(),
            ];
        })->sortByDesc('total')->take(15);

        // Monthly comparison - menampilkan total tiap bulan
        $monthlyComparison = [];
        foreach ($budgets as $budget) {
            $monthlyComparison[] = [
                'month_name' => $budget->month_name,
                'total' => $budget->total_budget,
                'items_count' => $budget->items->count(),
                'completed_count' => $budget->completed_items_count,
                'progress' => $budget->progress_percentage,
            ];
        }

        // Kategorisasi Items berdasarkan field kategori di database
        $categorizedData = $allItems->groupBy(function ($item) {
            return $item->category ?? 'Tanpa Kategori';
        })->map(function ($items, $categoryName) {
            return [
                'total' => $items->sum('estimated_amount'),
                'count' => $items->count(),
                'completed' => $items->where('is_completed', true)->count(),
            ];
        })->sortByDesc('total');

        // Available years
        $availableYears = Budget::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('budgets.report', compact(
            'budgets',
            'stats',
            'monthlyData',
            'itemGroups',
            'monthlyComparison',
            'categorizedData',
            'selectedYear',
            'availableYears'
        ));
    }

    /**
     * Export budget items to Excel
     */
    public function exportExcel(Budget $budget): BinaryFileResponse
    {
        $service = new BudgetExcelService();
        $filepath = $service->export($budget);

        $filename = 'budget_' . $budget->period . '.xlsx';

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import budget items from Excel
     */
    public function importExcel(Request $request, Budget $budget): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:5120', // Max 5MB
        ], [
            'excel_file.required' => 'File Excel harus diupload',
            'excel_file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'excel_file.max' => 'Ukuran file maksimal 5MB',
        ]);

        $file = $request->file('excel_file');

        // Use the uploaded file's temp path directly
        $tempPath = $file->getRealPath();

        // If getRealPath fails, try to move the file manually
        if (!$tempPath || !file_exists($tempPath)) {
            // Create temp directory if not exists
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Move uploaded file to our temp directory
            $filename = 'import_' . time() . '_' . $file->getClientOriginalName();
            $tempPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

            if (!$file->move($tempDir, $filename)) {
                return redirect()->route('budgets.show', $budget)
                    ->with('error', 'Gagal menyimpan file. Silakan coba lagi.');
            }
        }

        // Verify file exists
        if (!file_exists($tempPath)) {
            return redirect()->route('budgets.show', $budget)
                ->with('error', 'File tidak ditemukan. Silakan coba lagi.');
        }

        $service = new BudgetExcelService();
        $result = $service->import($budget, $tempPath);

        // Delete temp file if we created it in storage
        if (strpos($tempPath, storage_path()) !== false && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        if ($result['success']) {
            $message = $result['message'];
            if (!empty($result['errors'])) {
                $message .= ' Peringatan: ' . implode(', ', array_slice($result['errors'], 0, 3));
                if (count($result['errors']) > 3) {
                    $message .= ' dan ' . (count($result['errors']) - 3) . ' error lainnya.';
                }
            }
            return redirect()->route('budgets.show', $budget)
                ->with('success', $message);
        } else {
            return redirect()->route('budgets.show', $budget)
                ->with('error', $result['message']);
        }
    }

    /**
     * Export all budgets to Excel
     */
    public function exportAllExcel(): BinaryFileResponse
    {
        $service = new BudgetExcelService();
        $filepath = $service->exportAll();

        $filename = 'all_budgets_' . date('Y-m-d') . '.xlsx';

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Import all budgets from Excel
     */
    public function importAllExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240', // Max 10MB
        ], [
            'excel_file.required' => 'File Excel harus diupload',
            'excel_file.mimes' => 'File harus berformat Excel (.xlsx atau .xls)',
            'excel_file.max' => 'Ukuran file maksimal 10MB',
        ]);

        $file = $request->file('excel_file');

        // Use the uploaded file's temp path directly
        $tempPath = $file->getRealPath();

        // If getRealPath fails, try to move the file manually
        if (!$tempPath || !file_exists($tempPath)) {
            // Create temp directory if not exists
            $tempDir = storage_path('app' . DIRECTORY_SEPARATOR . 'temp');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Move uploaded file to our temp directory
            $filename = 'import_all_' . time() . '_' . $file->getClientOriginalName();
            $tempPath = $tempDir . DIRECTORY_SEPARATOR . $filename;

            if (!$file->move($tempDir, $filename)) {
                return redirect()->route('budgets.index')
                    ->with('error', 'Gagal menyimpan file. Silakan coba lagi.');
            }
        }

        // Verify file exists
        if (!file_exists($tempPath)) {
            return redirect()->route('budgets.index')
                ->with('error', 'File tidak ditemukan. Silakan coba lagi.');
        }

        $service = new BudgetExcelService();
        $result = $service->importAll($tempPath);

        // Delete temp file if we created it in storage
        if (strpos($tempPath, storage_path()) !== false && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        if ($result['success']) {
            $message = $result['message'];
            if (!empty($result['errors'])) {
                $message .= ' Peringatan: ' . implode(', ', array_slice($result['errors'], 0, 3));
                if (count($result['errors']) > 3) {
                    $message .= ' dan ' . (count($result['errors']) - 3) . ' error lainnya.';
                }
            }
            return redirect()->route('budgets.index')
                ->with('success', $message);
        } else {
            return redirect()->route('budgets.index')
                ->with('error', $result['message']);
        }
    }
}
