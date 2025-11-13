<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\BudgetItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{
    public function index(Request $request): View
    {
        $query = Budget::with('items');

        // Auto-detect latest year with data, fallback to current year
        $latestYear = Budget::max('year') ?? date('Y');
        $selectedYear = $request->get('year', $latestYear); // UBAH BARIS INI

        $query->byYear($selectedYear);

        // Filter by status
        if ($request->filled('status')) {
            match ($request->status) {
                'completed' => $query->whereHas('items')->having(
                    DB::raw('COUNT(*)'),
                    '=',
                    DB::raw('SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END)')
                ),
                'progress' => $query->whereHas('items', function ($q) {
                    $q->where('is_completed', true);
                })->whereHas('items', function ($q) {
                    $q->where('is_completed', false);
                }),
                'new' => $query->whereDoesntHave('items', function ($q) {
                    $q->where('is_completed', true);
                }),
                default => null
            };
        }

        $budgets = $query->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(12);

        // Stats
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
        ];

        // Available years
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

        return view('budgets.show', compact('budget'));
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
                            'item_name' => $itemData['item_name'],
                            'estimated_amount' => $itemData['estimated_amount'],
                            'notes' => $itemData['notes'] ?? null,
                        ]);
                    }
                } else {
                    // Create new item
                    BudgetItem::create([
                        'budget_id' => $budget->id,
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
    public function toggleItemComplete(BudgetItem $item): JsonResponse
    {
        try {
            $item->toggleComplete();

            return response()->json([
                'success' => true,
                'is_completed' => $item->is_completed,
                'completed_at' => $item->completed_date,
                'message' => $item->is_completed
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

        // Top 10 Highest Items
        $allItems = $budgets->flatMap(fn($b) => $b->items);
        $topItems = $allItems->sortByDesc('estimated_amount')->take(10);

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

        // Monthly comparison
        $monthlyComparison = [];
        for ($i = 0; $i < $budgets->count() - 1; $i++) {
            $current = $budgets[$i];
            $next = $budgets[$i + 1];
            $diff = $next->total_budget - $current->total_budget;
            $diffPercent = $current->total_budget > 0
                ? round(($diff / $current->total_budget) * 100, 1)
                : 0;

            $monthlyComparison[] = [
                'from' => $current->month_name,
                'to' => $next->month_name,
                'diff' => $diff,
                'diff_percent' => $diffPercent,
                'type' => $diff >= 0 ? 'increase' : 'decrease',
            ];
        }

        // Kategorisasi Items (berdasarkan keyword)
        $categories = [
            'Cicilan & Hutang' => ['KPR', 'Hutang', 'CC', 'Angsuran'],
            'Keluarga' => ['Mama', 'Aung', 'Soskar'],
            'Komunikasi' => ['Pulsa', 'Internet'],
            'Utilitas' => ['Listrik', 'Token', 'PAM', 'IPL'],
            'Lainnya' => [],
        ];

        $categorizedData = [];
        foreach ($categories as $categoryName => $keywords) {
            $categoryItems = $allItems->filter(function ($item) use ($keywords) {
                foreach ($keywords as $keyword) {
                    if (stripos($item->item_name, $keyword) !== false) {
                        return true;
                    }
                }
                return empty($keywords); // Lainnya
            });

            if ($categoryItems->count() > 0) {
                $categorizedData[$categoryName] = [
                    'total' => $categoryItems->sum('estimated_amount'),
                    'count' => $categoryItems->count(),
                    'items' => $categoryItems->take(5),
                ];
            }
        }

        // Sort categories by total
        $categorizedData = collect($categorizedData)->sortByDesc('total');

        // Available years
        $availableYears = Budget::selectRaw('DISTINCT year')
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('budgets.report', compact(
            'budgets',
            'stats',
            'monthlyData',
            'topItems',
            'itemGroups',
            'monthlyComparison',
            'categorizedData',
            'selectedYear',
            'availableYears'
        ));
    }
}
