<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use App\Models\SupplyUsage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplyController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supply::query();

        // Search
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by stock status
        if ($request->filled('status')) {
            match ($request->status) {
                'low' => $query->lowStock(),
                'out' => $query->outOfStock(),
                default => null
            };
        }

        $supplies = $query->orderBy('name')->paginate(15)->withQueryString();

        // Statistics
        $stats = [
            'total' => Supply::count(),
            'low_stock' => Supply::lowStock()->count(),
            'out_of_stock' => Supply::outOfStock()->count(),
            'normal' => Supply::whereColumn('qty', '>=', 'minimum_stock')->where('qty', '>', 0)->count(),
        ];

        return view('supplies.index', compact('supplies', 'stats'));
    }

    public function create(): View
    {
        return view('supplies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'order_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        Supply::create($validated);

        return redirect()->route('supplies.index')
            ->with('success', 'Perlengkapan berhasil ditambahkan!');
    }

    public function show(Supply $supply): View
    {
        $supply->load(['usages' => function ($query) {
            $query->orderBy('usage_date', 'desc')->orderBy('id', 'desc');
        }]);

        return view('supplies.show', compact('supply'));
    }

    public function edit(Supply $supply): View
    {
        return view('supplies.edit', compact('supply'));
    }

    public function update(Request $request, Supply $supply): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'order_link' => 'nullable|url|max:500',
            'notes' => 'nullable|string|max:1000',
        ]);

        $supply->update($validated);

        return redirect()->route('supplies.show', $supply)
            ->with('success', 'Perlengkapan berhasil diperbarui!');
    }

    public function destroy(Supply $supply): RedirectResponse
    {
        $supply->delete();

        return redirect()->route('supplies.index')
            ->with('success', 'Perlengkapan berhasil dihapus!');
    }

    // Usage Management
    public function showUseForm(Supply $supply): View
    {
        return view('supplies.use', compact('supply'));
    }

    public function recordUsage(Request $request, Supply $supply): RedirectResponse
    {
        $validated = $request->validate([
            'qty_used' => 'required|integer|min:1|max:' . $supply->qty,
            'usage_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:500',
        ], [
            'qty_used.max' => 'Jumlah penggunaan tidak boleh melebihi stok yang tersedia (' . $supply->qty . ')',
        ]);

        SupplyUsage::create([
            'supply_id' => $supply->id,
            'qty_used' => $validated['qty_used'],
            'usage_date' => $validated['usage_date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('supplies.show', $supply)
            ->with('success', 'Penggunaan perlengkapan berhasil dicatat!');
    }

    public function deleteUsage(SupplyUsage $usage): RedirectResponse
    {
        $supplyId = $usage->supply_id;
        $usage->delete();

        return redirect()->route('supplies.show', $supplyId)
            ->with('success', 'Riwayat penggunaan berhasil dihapus!');
    }

    // Add stock
    public function showAddStockForm(Supply $supply): View
    {
        return view('supplies.add-stock', compact('supply'));
    }

    public function addStock(Request $request, Supply $supply): RedirectResponse
    {
        $validated = $request->validate([
            'qty_added' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $supply->increment('qty', $validated['qty_added']);

        return redirect()->route('supplies.show', $supply)
            ->with('success', 'Stok berhasil ditambahkan!');
    }
}
