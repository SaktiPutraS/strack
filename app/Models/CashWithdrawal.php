<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'withdrawal_date',
        'amount',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'withdrawal_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    // Scopes
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('withdrawal_date', [$startDate, $endDate]);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('reference_number', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Auto update balances when cash withdrawal created/deleted
        static::saved(function ($withdrawal) {
            CashBalance::updateBalance();
            BankBalance::updateBalance();
        });

        static::deleted(function ($withdrawal) {
            CashBalance::updateBalance();
            BankBalance::updateBalance();
        });
    }
}
