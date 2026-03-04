<?php

namespace App\Http\Controllers;

use App\Models\BudgetItem;
use App\Services\BudgetExcelService;
use App\Support\BudgetPeriod;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BudgetController extends Controller
{
    /* ══════════════════════════════════════════════════════════════════════════
     *  INDEX — Matrix 12 bulan
     * ══════════════════════════════════════════════════════════════════════════ */
    public function index(Request $request): View
    {
        $year = (int) $request->input('year', date('Y'));

        // Load semua items tahun ini sekaligus
        $allItems = BudgetItem::where('year', $year)
            ->orderBy('month')
            ->orderBy('category')
            ->orderBy('id')
            ->get();

        // Buat koleksi BudgetPeriod keyed by month
        $existingMonths = $allItems->pluck('month')->unique()->sort()->values();
        $budgets = collect();
        foreach ($existingMonths as $m) {
            $budgets->put($m, new BudgetPeriod($m, $year, $allItems->where('month', $m)->values()));
        }

        // Bangun matrix: $matrix[$cat][$item_name][$month] = BudgetItem|null
        $categoryOrder = [];
        $matrix        = [];

        for ($m = 1; $m <= 12; $m++) {
            $bp = $budgets->get($m);
            if (!$bp) continue;
            foreach ($bp->items as $item) {
                $cat = $item->category ?: 'Tanpa Kategori';
                if (!in_array($cat, $categoryOrder)) $categoryOrder[] = $cat;
                if (!isset($matrix[$cat][$item->item_name])) {
                    $matrix[$cat][$item->item_name] = array_fill(1, 12, null);
                }
                $matrix[$cat][$item->item_name][$m] = $item;
            }
        }

        // Sort kategori A–Z, "Tanpa Kategori" selalu di akhir
        sort($categoryOrder);
        if (($idx = array_search('Tanpa Kategori', $categoryOrder)) !== false) {
            unset($categoryOrder[$idx]);
            $categoryOrder   = array_values($categoryOrder);
            $categoryOrder[] = 'Tanpa Kategori';
        }

        // Sort item dalam setiap kategori secara alfabetis
        foreach ($categoryOrder as $cat) {
            if (isset($matrix[$cat])) {
                ksort($matrix[$cat]);
            }
        }

        $availableYears = BudgetItem::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year');

        if ($availableYears->isEmpty()) {
            $availableYears = collect([$year]);
        }

        return view('budgets.index', compact('budgets', 'matrix', 'categoryOrder', 'year', 'availableYears'));
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  CREATE
     * ══════════════════════════════════════════════════════════════════════════ */
    public function create(Request $request): View
    {
        $currentYear = (int) $request->input('year', date('Y'));
        $preMonth    = (int) $request->input('month', 0);

        $usedMonths = BudgetItem::where('year', $currentYear)
            ->distinct()
            ->pluck('month')
            ->toArray();

        $allCategories = BudgetItem::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('budgets.create', compact('usedMonths', 'currentYear', 'preMonth', 'allCategories'));
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  STORE
     * ══════════════════════════════════════════════════════════════════════════ */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'month'                    => 'required|integer|min:1|max:12',
            'year'                     => 'required|integer|min:2020|max:2100',
            'items'                    => 'required|array|min:1',
            'items.*.item_name'        => 'required|string|max:255',
            'items.*.estimated_amount' => 'required|numeric|min:0',
            'items.*.notes'            => 'nullable|string|max:500',
            'items.*.category'         => 'nullable|string|max:255',
        ], ['items.required' => 'Minimal harus ada 1 item pengeluaran']);

        // Cek duplikat bulan
        $exists = BudgetItem::where('month', $validated['month'])
            ->where('year', $validated['year'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['month' => 'Budget untuk bulan dan tahun ini sudah ada!'])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            foreach ($validated['items'] as $itemData) {
                BudgetItem::create([
                    'month'            => $validated['month'],
                    'year'             => $validated['year'],
                    'category'         => $itemData['category'] ?? null,
                    'item_name'        => $itemData['item_name'],
                    'estimated_amount' => $itemData['estimated_amount'],
                    'notes'            => $itemData['notes'] ?? null,
                ]);
            }
            DB::commit();

            return redirect()
                ->route('budgets.show', [$validated['year'], $validated['month']])
                ->with('success', 'Budget berhasil dibuat!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  SHOW
     * ══════════════════════════════════════════════════════════════════════════ */
    public function show(int $year, int $month): View
    {
        $items = BudgetItem::where('year', $year)
            ->where('month', $month)
            ->orderBy('category')
            ->orderBy('id')
            ->get();

        abort_if($items->isEmpty(), 404);

        $budget = new BudgetPeriod($month, $year, $items);

        // Bulan sebelumnya (kolom kiri)
        $prevM = $month - 1; $prevY = $year;
        if ($prevM < 1) { $prevM = 12; $prevY = $year - 1; }
        $prevItems  = BudgetItem::where('year', $prevY)->where('month', $prevM)
            ->orderBy('category')->orderBy('id')->get();
        $prevBudget = $prevItems->isNotEmpty() ? new BudgetPeriod($prevM, $prevY, $prevItems) : null;

        // Bulan berikutnya (navigasi)
        $nextM = $month + 1; $nextY = $year;
        if ($nextM > 12) { $nextM = 1; $nextY = $year + 1; }
        $nextExists = BudgetItem::where('year', $nextY)->where('month', $nextM)->exists();
        $nextBudget = $nextExists ? new BudgetPeriod($nextM, $nextY) : null;

        $allCategories = BudgetItem::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('budgets.show', compact('budget', 'prevBudget', 'nextBudget', 'allCategories'));
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  STORE ITEM (AJAX — tambah item ke bulan yg sudah ada)
     * ══════════════════════════════════════════════════════════════════════════ */
    public function storeItem(Request $request, int $year, int $month): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_name'        => 'required|string|max:255',
                'estimated_amount' => 'required|numeric|min:0',
                'category'         => 'nullable|string|max:255',
                'notes'            => 'nullable|string|max:500',
            ]);

            $item = BudgetItem::create([
                'month'            => $month,
                'year'             => $year,
                'category'         => $validated['category'] ?: null,
                'item_name'        => $validated['item_name'],
                'estimated_amount' => $validated['estimated_amount'],
                'notes'            => $validated['notes'] ?? null,
                'is_completed'     => false,
            ]);

            $newTotal = BudgetItem::where('month', $month)->where('year', $year)
                ->sum('estimated_amount');

            return response()->json([
                'success'   => true,
                'item'      => [
                    'id'               => $item->id,
                    'item_name'        => $item->item_name,
                    'estimated_amount' => $item->estimated_amount,
                    'formatted_amount' => $item->formatted_amount,
                    'notes'            => $item->notes,
                    'category'         => $item->category,
                    'is_completed'     => false,
                ],
                'new_total' => $newTotal,
                'message'   => 'Item berhasil ditambahkan!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  DESTROY ITEM (AJAX)
     * ══════════════════════════════════════════════════════════════════════════ */
    public function destroyItem(BudgetItem $item): JsonResponse
    {
        try {
            $month = $item->month;
            $year  = $item->year;
            $item->delete();

            $newTotal = BudgetItem::where('month', $month)->where('year', $year)
                ->sum('estimated_amount');

            return response()->json([
                'success'   => true,
                'new_total' => $newTotal,
                'message'   => 'Item berhasil dihapus!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  EDIT
     * ══════════════════════════════════════════════════════════════════════════ */
    public function edit(int $year, int $month): View
    {
        $items = BudgetItem::where('year', $year)
            ->where('month', $month)
            ->orderBy('category')
            ->orderBy('id')
            ->get();

        abort_if($items->isEmpty(), 404);

        $budget = new BudgetPeriod($month, $year, $items);

        $usedMonths = BudgetItem::where('year', $year)
            ->where('month', '!=', $month)
            ->distinct()
            ->pluck('month')
            ->toArray();

        return view('budgets.edit', compact('budget', 'usedMonths'));
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  UPDATE
     * ══════════════════════════════════════════════════════════════════════════ */
    public function update(Request $request, int $year, int $month): RedirectResponse
    {
        $validated = $request->validate([
            'month'                    => 'required|integer|min:1|max:12',
            'year'                     => 'required|integer|min:2020|max:2100',
            'items'                    => 'required|array|min:1',
            'items.*.id'               => 'nullable|exists:budget_items,id',
            'items.*.category'         => 'nullable|string|max:255',
            'items.*.item_name'        => 'required|string|max:255',
            'items.*.estimated_amount' => 'required|numeric|min:0',
            'items.*.notes'            => 'nullable|string|max:500',
        ]);

        $newMonth = (int) $validated['month'];
        $newYear  = (int) $validated['year'];

        // Cek konflik jika bulan/tahun berubah
        if ($newMonth !== $month || $newYear !== $year) {
            $conflict = BudgetItem::where('month', $newMonth)
                ->where('year', $newYear)
                ->exists();

            if ($conflict) {
                return back()
                    ->withErrors(['month' => 'Budget untuk bulan dan tahun tersebut sudah ada!'])
                    ->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // IDs yang ada di form (untuk keep)
            $formIds = collect($validated['items'])
                ->pluck('id')
                ->filter()
                ->values()
                ->toArray();

            // Hapus item yang tidak ada di form
            $deleteQuery = BudgetItem::where('month', $month)->where('year', $year);
            if (!empty($formIds)) {
                $deleteQuery->whereNotIn('id', $formIds);
            }
            $deleteQuery->delete();

            // Update atau buat item
            foreach ($validated['items'] as $itemData) {
                $payload = [
                    'month'            => $newMonth,
                    'year'             => $newYear,
                    'category'         => $itemData['category'] ?? null,
                    'item_name'        => $itemData['item_name'],
                    'estimated_amount' => $itemData['estimated_amount'],
                    'notes'            => $itemData['notes'] ?? null,
                ];

                if (!empty($itemData['id'])) {
                    BudgetItem::where('id', $itemData['id'])
                        ->where('month', $month)
                        ->where('year', $year)
                        ->update($payload);
                } else {
                    BudgetItem::create($payload);
                }
            }

            DB::commit();

            return redirect()
                ->route('budgets.show', [$newYear, $newMonth])
                ->with('success', 'Budget berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  DESTROY (hapus semua item satu bulan)
     * ══════════════════════════════════════════════════════════════════════════ */
    public function destroy(int $year, int $month): RedirectResponse
    {
        BudgetItem::where('month', $month)->where('year', $year)->delete();

        return redirect()
            ->route('budgets.index')
            ->with('success', 'Budget berhasil dihapus!');
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  TOGGLE ITEM COMPLETE
     * ══════════════════════════════════════════════════════════════════════════ */
    public function toggleItemComplete(BudgetItem $budgetItem): JsonResponse
    {
        try {
            $budgetItem->toggleComplete();

            return response()->json([
                'success'      => true,
                'is_completed' => $budgetItem->is_completed,
                'completed_at' => $budgetItem->completed_date,
                'message'      => $budgetItem->is_completed
                    ? 'Item ditandai selesai!'
                    : 'Item ditandai belum selesai!',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  BULK TOGGLE COMPLETE
     * ══════════════════════════════════════════════════════════════════════════ */
    public function bulkToggleComplete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_ids'      => 'required|array|min:1',
                'item_ids.*'    => 'exists:budget_items,id',
                'mark_complete' => 'required|boolean',
            ]);

            $count = BudgetItem::whereIn('id', $validated['item_ids'])->update([
                'is_completed' => $validated['mark_complete'],
                'completed_at' => $validated['mark_complete'] ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'count'   => $count,
                'message' => $validated['mark_complete']
                    ? "{$count} item ditandai selesai!"
                    : "{$count} item ditandai belum selesai!",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  UPDATE ITEM (AJAX)
     * ══════════════════════════════════════════════════════════════════════════ */
    public function updateItem(Request $request, BudgetItem $budgetItem): JsonResponse
    {
        try {
            $validated = $request->validate([
                'item_name'        => 'required|string|max:255',
                'estimated_amount' => 'required|numeric|min:0',
                'notes'            => 'nullable|string|max:500',
                'category'         => 'nullable|string|max:255',
            ]);

            $budgetItem->update($validated);

            return response()->json([
                'success' => true,
                'item'    => [
                    'id'               => $budgetItem->id,
                    'item_name'        => $budgetItem->item_name,
                    'estimated_amount' => $budgetItem->estimated_amount,
                    'formatted_amount' => $budgetItem->formatted_amount,
                    'notes'            => $budgetItem->notes,
                    'category'         => $budgetItem->category,
                ],
                'message' => 'Item berhasil diperbarui!',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan'], 500);
        }
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  TOGGLE CATEGORY COMPLETE
     * ══════════════════════════════════════════════════════════════════════════ */
    public function toggleCategoryComplete(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'month'         => 'required|integer|min:1|max:12',
                'year'          => 'required|integer',
                'category'      => 'required|string',
                'mark_complete' => 'required|boolean',
            ]);

            $cat   = $validated['category'] === 'Tanpa Kategori' ? null : $validated['category'];
            $query = BudgetItem::where('month', $validated['month'])
                ->where('year', $validated['year']);

            if ($cat === null) {
                $query->whereNull('category');
            } else {
                $query->where('category', $cat);
            }

            $count = $query->update([
                'is_completed' => $validated['mark_complete'],
                'completed_at' => $validated['mark_complete'] ? now() : null,
            ]);

            return response()->json([
                'success' => true,
                'count'   => $count,
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

    /* ══════════════════════════════════════════════════════════════════════════
     *  REPORT
     * ══════════════════════════════════════════════════════════════════════════ */
    public function report(Request $request, $year = null): View
    {
        $selectedYear = (int) ($year ?? BudgetItem::max('year') ?? date('Y'));

        // Load semua items tahun ini (single query)
        $allItems = BudgetItem::where('year', $selectedYear)->get();

        // Buat BudgetPeriod per bulan
        $months  = $allItems->pluck('month')->unique()->sort()->values();
        $budgets = $months->map(
            fn($m) => new BudgetPeriod($m, $selectedYear, $allItems->where('month', $m)->values())
        );

        $totalItems     = $allItems->count();
        $completedItems = $allItems->where('is_completed', true)->count();

        $stats = [
            'total_budgets'      => $budgets->count(),
            'total_budget_year'  => $budgets->sum(fn($b) => $b->total_budget),
            'avg_budget_month'   => $budgets->count() > 0
                                     ? $budgets->sum(fn($b) => $b->total_budget) / $budgets->count()
                                     : 0,
            'highest_budget'     => $budgets->max(fn($b) => $b->total_budget) ?? 0,
            'lowest_budget'      => $budgets->min(fn($b) => $b->total_budget) ?? 0,
            'total_items'        => $totalItems,
            'completed_items'    => $completedItems,
            'completion_rate'    => $totalItems > 0
                                     ? round(($completedItems / $totalItems) * 100, 1)
                                     : 0,
        ];

        $monthlyData = $budgets->map(fn($b) => [
            'month'           => $b->month,
            'month_name'      => $b->month_name,
            'total'           => $b->total_budget,
            'items_count'     => $b->total_items_count,
            'completed_count' => $b->completed_items_count,
            'completion_rate' => $b->progress_percentage,
        ]);

        $itemGroups = $allItems->groupBy('item_name')->map(fn($items, $name) => [
            'name'      => $name,
            'count'     => $items->count(),
            'total'     => $items->sum('estimated_amount'),
            'avg'       => $items->avg('estimated_amount'),
            'completed' => $items->where('is_completed', true)->count(),
        ])->sortByDesc('total')->take(15);

        $monthlyComparison = $budgets->map(fn($b) => [
            'month_name'      => $b->period,
            'total'           => $b->total_budget,
            'items_count'     => $b->total_items_count,
            'completed_count' => $b->completed_items_count,
            'progress'        => $b->progress_percentage,
        ])->values()->toArray();

        $categorizedData = $allItems
            ->groupBy(fn($item) => $item->category ?? 'Tanpa Kategori')
            ->map(fn($items) => [
                'total'     => $items->sum('estimated_amount'),
                'count'     => $items->count(),
                'completed' => $items->where('is_completed', true)->count(),
            ])
            ->sortByDesc('total');

        $availableYears = BudgetItem::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year');

        return view('budgets.report', compact(
            'budgets', 'stats', 'monthlyData', 'itemGroups',
            'monthlyComparison', 'categorizedData', 'selectedYear', 'availableYears'
        ));
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  EXPORT EXCEL
     * ══════════════════════════════════════════════════════════════════════════ */
    public function exportExcel(int $year, int $month): BinaryFileResponse
    {
        $items = BudgetItem::where('year', $year)->where('month', $month)
            ->orderBy('category')->orderBy('id')->get();

        abort_if($items->isEmpty(), 404);

        $budget   = new BudgetPeriod($month, $year, $items);
        $service  = new BudgetExcelService();
        $filepath = $service->export($budget);
        $filename = 'budget_' . str_replace(' ', '_', $budget->period) . '.xlsx';

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  IMPORT EXCEL
     * ══════════════════════════════════════════════════════════════════════════ */
    public function importExcel(Request $request, int $year, int $month): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:5120',
        ]);

        $file     = $request->file('excel_file');
        $tempPath = $file->getRealPath();

        if (!$tempPath || !file_exists($tempPath)) {
            $tempDir  = storage_path('app/temp');
            if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
            $fname    = 'import_' . time() . '_' . $file->getClientOriginalName();
            $tempPath = $tempDir . '/' . $fname;
            if (!$file->move($tempDir, $fname)) {
                return redirect()->route('budgets.show', [$year, $month])
                    ->with('error', 'Gagal menyimpan file.');
            }
        }

        $service = new BudgetExcelService();
        $result  = $service->import($month, $year, $tempPath);

        if (str_starts_with($tempPath, storage_path()) && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        $redirect = redirect()->route('budgets.show', [$year, $month]);

        if ($result['success']) {
            $msg = $result['message'];
            if (!empty($result['errors'])) {
                $msg .= ' Peringatan: ' . implode(', ', array_slice($result['errors'], 0, 3));
                if (count($result['errors']) > 3) {
                    $msg .= ' dan ' . (count($result['errors']) - 3) . ' error lainnya.';
                }
            }
            return $redirect->with('success', $msg);
        }

        return $redirect->with('error', $result['message']);
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  EXPORT ALL EXCEL
     * ══════════════════════════════════════════════════════════════════════════ */
    public function exportAllExcel(): BinaryFileResponse
    {
        $service  = new BudgetExcelService();
        $filepath = $service->exportAll();

        return response()
            ->download($filepath, 'all_budgets_' . date('Y-m-d') . '.xlsx')
            ->deleteFileAfterSend(true);
    }

    /* ══════════════════════════════════════════════════════════════════════════
     *  IMPORT ALL EXCEL
     * ══════════════════════════════════════════════════════════════════════════ */
    public function importAllExcel(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        $file     = $request->file('excel_file');
        $tempPath = $file->getRealPath();

        if (!$tempPath || !file_exists($tempPath)) {
            $tempDir  = storage_path('app/temp');
            if (!file_exists($tempDir)) mkdir($tempDir, 0755, true);
            $fname    = 'import_all_' . time() . '_' . $file->getClientOriginalName();
            $tempPath = $tempDir . '/' . $fname;
            if (!$file->move($tempDir, $fname)) {
                return redirect()->route('budgets.index')
                    ->with('error', 'Gagal menyimpan file.');
            }
        }

        $service = new BudgetExcelService();
        $result  = $service->importAll($tempPath);

        if (str_starts_with($tempPath, storage_path()) && file_exists($tempPath)) {
            @unlink($tempPath);
        }

        $redirect = redirect()->route('budgets.index');

        if ($result['success']) {
            $msg = $result['message'];
            if (!empty($result['errors'])) {
                $msg .= ' Peringatan: ' . implode(', ', array_slice($result['errors'], 0, 3));
                if (count($result['errors']) > 3) {
                    $msg .= ' dan ' . (count($result['errors']) - 3) . ' error lainnya.';
                }
            }
            return $redirect->with('success', $msg);
        }

        return $redirect->with('error', $result['message']);
    }
}
