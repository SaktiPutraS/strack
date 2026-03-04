<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Value object pengganti Eloquent Budget model.
 * Membungkus month + year + items, dengan interface yang sama persis
 * seperti Budget model lama (accessor, computed properties) sehingga
 * view tidak perlu banyak diubah.
 */
class BudgetPeriod
{
    public int        $month;
    public int        $year;
    public Collection $items;

    private static array $monthNames = [
        1  => 'Januari',  2  => 'Februari', 3  => 'Maret',
        4  => 'April',    5  => 'Mei',       6  => 'Juni',
        7  => 'Juli',     8  => 'Agustus',   9  => 'September',
        10 => 'Oktober',  11 => 'November',  12 => 'Desember',
    ];

    public function __construct(int $month, int $year, $items = null)
    {
        $this->month = $month;
        $this->year  = $year;
        $this->items = $items instanceof Collection
            ? $items
            : collect($items ?? []);
    }

    /** Memungkinkan $budget->period, $budget->total_budget, dst. dari Blade */
    public function __get(string $key): mixed
    {
        return match ($key) {
            'month_name'            => self::$monthNames[$this->month] ?? '',
            'period'                => (self::$monthNames[$this->month] ?? '') . ' ' . $this->year,
            'total_budget'          => (float) $this->items->sum('estimated_amount'),
            'formatted_budget'      => 'Rp ' . number_format($this->items->sum('estimated_amount'), 0, ',', '.'),
            'completed_items_count' => $this->items->where('is_completed', true)->count(),
            'total_items_count'     => $this->items->count(),
            'progress_percentage'   => $this->items->count() > 0
                                        ? round(($this->items->where('is_completed', true)->count() / $this->items->count()) * 100, 1)
                                        : 0.0,
            'is_fully_completed'    => $this->items->count() > 0
                                        && $this->items->where('is_completed', true)->count() === $this->items->count(),
            'completed_amount'      => (float) $this->items->where('is_completed', true)->sum('estimated_amount'),
            'remaining_amount'      => (float) $this->items->where('is_completed', false)->sum('estimated_amount'),
            'status'                => $this->computeStatus(),
            'items_grouped_by_category' => $this->getItemsGroupedByCategory(),
            'categories'            => $this->items->whereNotNull('category')
                                        ->pluck('category')->unique()->sort()->values()->toArray(),
            default                 => null,
        };
    }

    /** Diperlukan agar data_get() / Collection::sum('key') bisa memanggil __get */
    public function __isset(string $key): bool
    {
        return in_array($key, [
            'month_name', 'period', 'total_budget', 'formatted_budget',
            'completed_items_count', 'total_items_count', 'progress_percentage',
            'is_fully_completed', 'completed_amount', 'remaining_amount',
            'status', 'items_grouped_by_category', 'categories',
        ]);
    }

    private function computeStatus(): string
    {
        $completed = $this->items->where('is_completed', true)->count();
        $total     = $this->items->count();
        if ($total === 0 || $completed === 0) return 'new';
        if ($completed === $total)            return 'completed';
        return 'progress';
    }

    private function getItemsGroupedByCategory(): array
    {
        $grouped = [];

        foreach ($this->items->sortBy([['category', 'asc'], ['id', 'asc']]) as $item) {
            $cat = $item->category ?: 'Tanpa Kategori';
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [
                    'name'             => $cat,
                    'items'            => [],
                    'total_amount'     => 0,
                    'completed_amount' => 0,
                    'total_count'      => 0,
                    'completed_count'  => 0,
                    'progress'         => 0,
                    'is_completed'     => false,
                ];
            }
            $grouped[$cat]['items'][]      = $item;
            $grouped[$cat]['total_amount'] += $item->estimated_amount;
            $grouped[$cat]['total_count']++;
            if ($item->is_completed) {
                $grouped[$cat]['completed_amount'] += $item->estimated_amount;
                $grouped[$cat]['completed_count']++;
            }
        }

        foreach ($grouped as $key => $cat) {
            $grouped[$key]['progress']     = $cat['total_count'] > 0
                ? round(($cat['completed_count'] / $cat['total_count']) * 100, 1)
                : 0;
            $grouped[$key]['is_completed'] = $cat['completed_count'] === $cat['total_count']
                && $cat['total_count'] > 0;
        }

        return $grouped;
    }
}
