<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'budget_id',
        'item_name',
        'estimated_amount',
        'notes',
        'is_completed',
        'completed_at',
    ];

    protected $casts = [
        'estimated_amount' => 'decimal:2',
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship
    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->estimated_amount, 0, ',', '.');
    }

    public function getCompletedDateAttribute(): ?string
    {
        return $this->completed_at ? $this->completed_at->format('d M Y') : null;
    }

    // Methods
    public function toggleComplete(): bool
    {
        $this->is_completed = !$this->is_completed;
        $this->completed_at = $this->is_completed ? now() : null;
        return $this->save();
    }

    // Boot
    protected static function boot()
    {
        parent::boot();

        // Update budget total after item changes
        static::saved(function ($item) {
            $item->budget->updateTotalBudget();
        });

        static::deleted(function ($item) {
            $item->budget->updateTotalBudget();
        });
    }
}
