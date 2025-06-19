<?php
// app/Models/Payment.php - Updated boot method

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'amount',
        'payment_type',
        'payment_date',
        'notes',
        'payment_method',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Payment belongs to project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship: Payment has one saving record (10%)
     */
    public function savingRecord(): HasOne
    {
        return $this->hasOne(Saving::class);
    }

    /**
     * Get 10% amount for saving
     */
    public function getSavingAmountAttribute(): float
    {
        return $this->amount * 0.10;
    }

    /**
     * Format currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    public function getFormattedSavingAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->saving_amount ?? 0, 0, ',', '.');
    }

    /**
     * Get payment type color for UI
     */
    public function getTypeColorAttribute(): string
    {
        return match ($this->payment_type) {
            'DP' => 'blue',
            'INSTALLMENT' => 'yellow',
            'FULL' => 'green',
            'FINAL' => 'green',
            default => 'gray'
        };
    }

    /**
     * Get payment type icon for UI
     */
    public function getTypeIconAttribute(): string
    {
        return match ($this->payment_type) {
            'DP' => 'dollar-sign',
            'INSTALLMENT' => 'credit-card',
            'FULL' => 'check-circle',
            'FINAL' => 'check-circle-2',
            default => 'help-circle'
        };
    }

    /**
     * Scope: Search payments
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('payment_type', 'like', "%{$search}%")
                ->orWhere('payment_method', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%")
                ->orWhereHas('project', function ($projectQuery) use ($search) {
                    $projectQuery->where('title', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('name', 'like', "%{$search}%");
                        });
                });
        });
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Create PENDING saving record when payment is created
        static::created(function ($payment) {
            Saving::create([
                'payment_id' => $payment->id,
                'amount' => $payment->saving_amount,
                'transaction_date' => $payment->payment_date,
                'status' => 'PENDING', // Not transferred yet
                'notes' => "10% tabungan dari pembayaran: {$payment->project->title}"
            ]);
        });

        // Update project's paid_amount when payment is saved
        static::saved(function ($payment) {
            $project = $payment->project;
            $project->paid_amount = $project->payments()->sum('amount');
            $project->saveQuietly();
        });

        // Update project's paid_amount when payment is deleted
        static::deleted(function ($payment) {
            // Delete associated saving record
            $payment->savingRecord?->delete();

            // Update project paid amount
            $project = $payment->project;
            $project->paid_amount = $project->payments()->sum('amount');
            $project->saveQuietly();
        });
    }
}
