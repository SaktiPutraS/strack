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
        'bank_balance',
        'transaction_date',
        'notes',
        'is_verified',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'bank_balance' => 'decimal:2',
        'transaction_date' => 'date',
        'is_verified' => 'boolean',
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
     * Get total accumulated savings
     */
    public static function getTotalSavings(): float
    {
        return static::sum('amount');
    }

    /**
     * Get current bank balance (latest record)
     */
    public static function getCurrentBankBalance(): float
    {
        $latest = static::latest('transaction_date')->first();
        return $latest ? $latest->bank_balance : 0;
    }

    /**
     * Check if total savings matches bank balance
     */
    public static function isSavingsBalanced(): bool
    {
        $totalSavings = static::getTotalSavings();
        $bankBalance = static::getCurrentBankBalance();

        // Allow small difference (1 rupiah) due to rounding
        return abs($totalSavings - $bankBalance) <= 1;
    }

    /**
     * Get difference between total savings and bank balance
     */
    public static function getSavingsDifference(): float
    {
        return static::getTotalSavings() - static::getCurrentBankBalance();
    }

    /**
     * Format currency
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    public function getFormattedBankBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->bank_balance ?? 0, 0, ',', '.');
    }

    /**
     * Get verification status color for UI
     */
    public function getVerificationColorAttribute(): string
    {
        return $this->is_verified ? 'green' : 'yellow';
    }

    /**
     * Get verification status icon for UI
     */
    public function getVerificationIconAttribute(): string
    {
        return $this->is_verified ? 'check-circle' : 'clock';
    }

    /**
     * Scope: Verified savings
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope: Unverified savings
     */
    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    /**
     * Scope: Recent savings (last 30 days)
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('transaction_date', '>=', now()->subDays($days));
    }
}
