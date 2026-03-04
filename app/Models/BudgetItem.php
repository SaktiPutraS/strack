<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'category',
        'item_name',
        'estimated_amount',
        'notes',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'month'            => 'integer',
        'year'             => 'integer',
        'estimated_amount' => 'decimal:2',
        'is_completed'     => 'boolean',
        'completed_at'     => 'datetime',
        'created_at'       => 'datetime',
        'updated_at'       => 'datetime',
    ];

    // ─── Accessors ────────────────────────────────────────────────────────────

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->estimated_amount, 0, ',', '.');
    }

    public function getCompletedDateAttribute(): ?string
    {
        return $this->completed_at ? $this->completed_at->format('d M Y') : null;
    }

    // ─── Methods ──────────────────────────────────────────────────────────────

    public function toggleComplete(): bool
    {
        $this->is_completed = !$this->is_completed;
        $this->completed_at = $this->is_completed ? now() : null;
        return $this->save();
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    public function scopeByYear($query, int $year)
    {
        return $query->where('year', $year);
    }
}
