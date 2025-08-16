<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'amount',
        'category',
        'source', // NEW FIELD
        'description',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Source constants
    const SOURCE_BANK = 'BANK';
    const SOURCE_CASH = 'CASH';

    const SOURCES = [
        self::SOURCE_BANK => 'Bank Octo',
        self::SOURCE_CASH => 'Cash',
    ];

    // Updated Category constants
    const CATEGORIES = [
        'AI'            => 'AI',
        'ADMIN_BANK'    => 'Admin Bank',
        'BENSIN'        => 'Bensin',
        'BUKU'          => 'Buku',
        'DOMAIN_HOSTING' => 'Domain/Hosting',
        'ENTERTAIN'     => 'Entertain/Jajan',
        'GAJI_BONUS'    => 'Gaji/Bonus',
        'INVENTARIS'    => 'Inventaris',
        'KOPI_SUSU'     => 'Kopi/Susu',
        'LAINNYA'       => 'Lainnya',
        'PERLENGKAPAN'  => 'Perlengkapan/Habis Pakai',
        'PERALATAN'     => 'Peralatan',
        'SALDO_AWAL'    => 'Saldo Awal',
        'SEMBAKO'       => 'Sembako',
        'SIERRA'        => 'Sierra',
        'SKINCARE'      => 'Skincare/Sabun/Shampo',
        'TRAKTIR'       => 'Traktir/Kado/Kondangan',
        'UTILITAS'      => 'Internet/Air Satelit/Iuran',
    ];

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getSourceLabelAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    public function getSourceColorAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_BANK => 'primary',
            self::SOURCE_CASH => 'success',
            default => 'secondary'
        };
    }

    public function getSourceIconAttribute(): string
    {
        return match ($this->source) {
            self::SOURCE_BANK => 'bank',
            self::SOURCE_CASH => 'cash-coin',
            default => 'circle'
        };
    }

    // Scopes
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', "%{$search}%");
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Auto update balances when expense created/updated/deleted
        static::saved(function ($expense) {
            if ($expense->source === self::SOURCE_BANK) {
                BankBalance::updateBalance();
            } else {
                CashBalance::updateBalance();
            }
        });

        static::deleted(function ($expense) {
            if ($expense->source === self::SOURCE_BANK) {
                BankBalance::updateBalance();
            } else {
                CashBalance::updateBalance();
            }
        });
    }
}
