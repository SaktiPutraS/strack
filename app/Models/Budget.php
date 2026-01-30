<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'total_budget',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'total_budget' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship
    public function items()
    {
        return $this->hasMany(BudgetItem::class);
    }

    // Accessors
    public function getMonthNameAttribute(): string
    {
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        return $months[$this->month] ?? '';
    }

    public function getFormattedBudgetAttribute(): string
    {
        return 'Rp ' . number_format($this->total_budget, 0, ',', '.');
    }

    public function getPeriodAttribute(): string
    {
        return $this->month_name . ' ' . $this->year;
    }

    // Stats
    public function getCompletedItemsCountAttribute(): int
    {
        return $this->items()->where('is_completed', true)->count();
    }

    public function getTotalItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_items_count == 0) return 0;
        return round(($this->completed_items_count / $this->total_items_count) * 100, 1);
    }

    public function getIsFullyCompletedAttribute(): bool
    {
        return $this->total_items_count > 0 &&
            $this->completed_items_count == $this->total_items_count;
    }

    public function getStatusAttribute(): string
    {
        if ($this->completed_items_count == 0) return 'new';
        if ($this->is_fully_completed) return 'completed';
        return 'progress';
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'new' => 'secondary',
            'progress' => 'warning',
            'completed' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'new' => 'Baru',
            'progress' => 'Progress',
            'completed' => 'Selesai',
            default => 'Baru'
        };
    }

    public function getCompletedAmountAttribute(): float
    {
        return $this->items()->where('is_completed', true)->sum('estimated_amount');
    }

    public function getRemainingAmountAttribute(): float
    {
        return $this->items()->where('is_completed', false)->sum('estimated_amount');
    }

    // Scopes
    public function scopeByYear($query, $year)
    {
        return $query->where('year', $year);
    }

    public function scopeByMonth($query, $month)
    {
        return $query->where('month', $month);
    }

    public function scopeCompleted($query)
    {
        return $query->whereHas('items', function ($q) {
            $q->where('is_completed', true);
        }, '=', function ($q) {
            return $q->from('budget_items')->selectRaw('count(*)');
        });
    }

    // Methods
    public function updateTotalBudget(): void
    {
        $this->total_budget = $this->items()->sum('estimated_amount');
        $this->save();
    }

    // Get items grouped by category
    public function getItemsGroupedByCategoryAttribute(): array
    {
        $items = $this->items()
            ->orderBy('category')
            ->orderBy('is_completed')
            ->orderBy('id')
            ->get();

        $grouped = [];

        // Group items by category
        foreach ($items as $item) {
            $category = $item->category ?: 'Tanpa Kategori';
            if (!isset($grouped[$category])) {
                $grouped[$category] = [
                    'name' => $category,
                    'items' => [],
                    'total_amount' => 0,
                    'completed_amount' => 0,
                    'total_count' => 0,
                    'completed_count' => 0,
                ];
            }
            $grouped[$category]['items'][] = $item;
            $grouped[$category]['total_amount'] += $item->estimated_amount;
            $grouped[$category]['total_count']++;
            if ($item->is_completed) {
                $grouped[$category]['completed_amount'] += $item->estimated_amount;
                $grouped[$category]['completed_count']++;
            }
        }

        // Calculate progress for each category
        foreach ($grouped as $key => $category) {
            $grouped[$key]['progress'] = $category['total_count'] > 0
                ? round(($category['completed_count'] / $category['total_count']) * 100, 1)
                : 0;
            $grouped[$key]['is_completed'] = $category['completed_count'] == $category['total_count'] && $category['total_count'] > 0;
        }

        return $grouped;
    }

    // Get all unique categories for this budget
    public function getCategoriesAttribute(): array
    {
        return $this->items()
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->toArray();
    }
}
