<?php
// app/Models/BankBalance.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'balance',
        'balance_date',
        'bank_name',
        'notes',
        'is_verified',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'balance_date' => 'date',
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get latest bank balance for specific bank
     */
    public static function getLatestBalance($bankName = 'Bank Octo'): float
    {
        $latest = static::where('bank_name', $bankName)
            ->latest('balance_date')
            ->first();

        return $latest ? $latest->balance : 0;
    }

    /**
     * Create new bank balance record
     */
    public static function recordBalance(float $balance, string $bankName = 'Bank Octo', string $notes = '')
    {
        return static::create([
            'balance' => $balance,
            'balance_date' => now()->toDateString(),
            'bank_name' => $bankName,
            'notes' => $notes,
            'is_verified' => true,
        ]);
    }

    /**
     * Format currency
     */
    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->balance ?? 0, 0, ',', '.');
    }

    /**
     * Get available banks
     */
    public static function getAvailableBanks(): array
    {
        return [
            'Bank Octo' => 'Bank Octo',
            'BCA' => 'BCA',
            'Mandiri' => 'Mandiri',
            'Other' => 'Other'
        ];
    }

    /**
     * Scope: For specific bank
     */
    public function scopeForBank($query, $bankName)
    {
        return $query->where('bank_name', $bankName);
    }

    /**
     * Scope: Recent balances
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('balance_date', '>=', now()->subDays($days));
    }
}
