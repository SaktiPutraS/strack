<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBalance extends Model
{
    use HasFactory;

    protected $table = 'cash_balance';

    protected $fillable = [
        'initial_balance',
        'current_balance',
        'last_updated',
    ];

    protected $casts = [
        'initial_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'last_updated' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessors
    public function getFormattedCurrentBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->current_balance ?? 0, 0, ',', '.');
    }

    public function getFormattedInitialBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->initial_balance ?? 0, 0, ',', '.');
    }

    // Static methods
    public static function getCurrentBalance(): float
    {
        $record = self::first();

        if (!$record) {
            $record = self::create([
                'initial_balance' => 0,
                'current_balance' => 0,
                'last_updated' => now()->toDateString(),
            ]);
        }

        return $record->current_balance ?? 0;
    }

    public static function updateBalance(): void
    {
        $record = self::first();

        if (!$record) {
            $record = self::create([
                'initial_balance' => 0,
                'current_balance' => 0,
                'last_updated' => now()->toDateString(),
            ]);
        }

        // Calculate cash balance - import classes at top
        $totalWithdrawals = \App\Models\CashWithdrawal::sum('amount');
        $totalCashExpenses = \App\Models\Expense::where('source', 'CASH')->sum('amount');

        $newBalance = $record->initial_balance + $totalWithdrawals - $totalCashExpenses;

        $record->update([
            'current_balance' => $newBalance,
            'last_updated' => now()->toDateString(),
        ]);
    }
}
