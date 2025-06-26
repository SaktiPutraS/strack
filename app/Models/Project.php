<?php
// app/Models/Project.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'title',
        'description',
        'type',
        'total_value',
        'dp_amount',
        'paid_amount',
        'status',
        'deadline',
        'notes',
    ];

    protected $casts = [
        'total_value' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'deadline' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Project belongs to client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship: Project has many payments
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get remaining amount (computed attribute)
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->total_value - $this->paid_amount;
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_value == 0) return 0;
        return round(($this->paid_amount / $this->total_value) * 100, 1);
    }

    /**
     * Get days until deadline
     */
    public function getDaysUntilDeadlineAttribute(): int
    {
        return Carbon::now()->diffInDays($this->deadline, false);
    }

    /**
     * Check if project is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return Carbon::now()->gt($this->deadline) && $this->status !== 'FINISHED';
    }

    /**
     * Check if deadline is near (within 7 days)
     */
    public function getIsDeadlineNearAttribute(): bool
    {
        $daysUntil = $this->days_until_deadline;
        return $daysUntil >= 0 && $daysUntil <= 7 && $this->status !== 'FINISHED';
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'WAITING' => 'yellow',
            'PROGRESS' => 'blue',
            'FINISHED' => 'green',
            'CANCELLED' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get status icon for UI
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'WAITING' => 'clock',
            'PROGRESS' => 'play',
            'FINISHED' => 'check-circle',
            'CANCELLED' => 'x-circle',
            default => 'help-circle'
        };
    }

    /**
     * Format currency
     */
    public function getFormattedTotalValueAttribute(): string
    {
        return 'Rp ' . number_format($this->total_value ?? 0, 0, ',', '.');
    }

    public function getFormattedPaidAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->paid_amount ?? 0, 0, ',', '.');
    }

    public function getFormattedRemainingAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount ?? 0, 0, ',', '.');
    }

    public function getFormattedDpAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->dp_amount ?? 0, 0, ',', '.');
    }

    /**
     * Scope: Active projects
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['WAITING', 'PROGRESS']);
    }

    /**
     * Scope: Finished projects
     */
    public function scopeFinished($query)
    {
        return $query->where('status', 'FINISHED');
    }

    /**
     * Scope: Overdue projects
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', Carbon::now())
            ->whereNotIn('status', ['FINISHED', 'CANCELLED']);
    }

    /**
     * Scope: Upcoming deadlines
     */
    public function scopeUpcomingDeadlines($query, $days = 7)
    {
        return $query->where('deadline', '>=', Carbon::now())
            ->where('deadline', '<=', Carbon::now()->addDays($days))
            ->whereNotIn('status', ['FINISHED', 'CANCELLED'])
            ->orderBy('deadline');
    }

    /**
     * Scope: Search projects
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('name', 'like', "%{$search}%");
                });
        });
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update paid_amount when payments are added
        static::saved(function ($project) {
            $project->paid_amount = $project->payments()->sum('amount');
            if ($project->isDirty('paid_amount')) {
                $project->saveQuietly(); // Prevent infinite loop
            }
        });
    }

    // Add this relationship method
    public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'type', 'name');
    }

    // Add this accessor for type info
    public function getTypeInfoAttribute(): ?ProjectType
    {
        return ProjectType::where('name', $this->type)->first();
    }

    // Add this accessor for type color
    public function getTypeColorAttribute(): string
    {
        $typeInfo = $this->type_info;
        return $typeInfo ? $typeInfo->color : '#6c757d';
    }

    // Add this accessor for type icon
    public function getTypeIconAttribute(): string
    {
        $typeInfo = $this->type_info;
        return $typeInfo ? $typeInfo->icon : 'bi-folder';
    }
}
