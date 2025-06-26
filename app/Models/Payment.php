<?php
// app/Models/Payment.php - UPDATED VERSION with Transfer Status

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
        'is_transferred', // NEW FIELD
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'is_transferred' => 'boolean', // NEW CAST
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
     * Relationship: Payment has one bank transfer
     */
    public function bankTransfer(): HasOne
    {
        return $this->hasOne(BankTransfer::class);
    }

    /**
     * Format currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
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
     * Get transfer status badge
     */
    public function getTransferStatusBadgeAttribute(): string
    {
        if ($this->is_transferred) {
            return '<span class="badge bg-success">SUDAH TRANSFER</span>';
        }
        return '<span class="badge bg-warning">BELUM TRANSFER</span>';
    }

    /**
     * Get transfer status color
     */
    public function getTransferStatusColorAttribute(): string
    {
        return $this->is_transferred ? 'success' : 'warning';
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
     * Scope: Transferred payments
     */
    public function scopeTransferred($query)
    {
        return $query->where('is_transferred', true);
    }

    /**
     * Scope: Untransferred payments
     */
    public function scopeUntransferred($query)
    {
        return $query->where('is_transferred', false);
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update project's paid_amount when payment is saved
        static::saved(function ($payment) {
            $project = $payment->project;
            $project->paid_amount = $project->payments()->sum('amount');
            $project->saveQuietly();
        });

        // Update project's paid_amount when payment is deleted
        static::deleted(function ($payment) {
            // Update project paid amount
            $project = $payment->project;
            $project->paid_amount = $project->payments()->sum('amount');
            $project->saveQuietly();
        });
    }
}
