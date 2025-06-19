<?php
// app/Models/Saving.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Saving extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'amount',
        'transaction_date',
        'status',
        'transfer_date',
        'transfer_method',
        'transfer_reference',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
        'transfer_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Saving belongs to payment
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    /**
     * Get total savings amount (all records)
     */
    public static function getTotalSavings(): float
    {
        return static::sum('amount');
    }

    /**
     * Get total pending savings (not transferred yet)
     */
    public static function getPendingSavings(): float
    {
        return static::where('status', 'PENDING')->sum('amount');
    }

    /**
     * Get total transferred savings
     */
    public static function getTransferredSavings(): float
    {
        return static::where('status', 'TRANSFERRED')->sum('amount');
    }

    /**
     * Get current bank balance from latest bank_balances record
     */
    public static function getCurrentBankBalance(): float
    {
        $latestBalance = \App\Models\BankBalance::latest('balance_date')->first();
        return $latestBalance ? $latestBalance->balance : 0;
    }

    /**
     * Check if savings are balanced with bank
     */
    public static function isSavingsBalanced(): bool
    {
        $transferredSavings = static::getTransferredSavings();
        $bankBalance = static::getCurrentBankBalance();

        // Allow small difference (1000 rupiah) due to rounding or bank fees
        return abs($transferredSavings - $bankBalance) <= 1000;
    }

    /**
     * Get difference between transferred savings and bank balance
     */
    public static function getSavingsDifference(): float
    {
        return static::getTransferredSavings() - static::getCurrentBankBalance();
    }

    /**
     * Format currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'PENDING' => 'warning',
            'TRANSFERRED' => 'success',
            default => 'secondary'
        };
    }

    /**
     * Get status icon for UI
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'PENDING' => 'clock',
            'TRANSFERRED' => 'check-circle',
            default => 'help-circle'
        };
    }

    /**
     * Scope: Pending savings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope: Transferred savings
     */
    public function scopeTransferred($query)
    {
        return $query->where('status', 'TRANSFERRED');
    }

    /**
     * Scope: Recent savings (last 30 days)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('transaction_date', '>=', now()->subDays($days));
    }

    /**
     * Scope: Ready for transfer (pending and amount > threshold)
     */
    public function scopeReadyForTransfer($query, $minAmount = 0)
    {
        return $query->where('status', 'PENDING')
            ->where('amount', '>=', $minAmount);
    }
}
