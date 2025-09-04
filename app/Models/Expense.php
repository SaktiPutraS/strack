<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_date',
        'amount',
        'category',
        'source',
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
        'HPP'          => 'HPP/Potongan Penjualan/Retur',
        'INVENTARIS'    => 'Inventaris',
        'KOPI_SUSU'     => 'Kopi/Susu',
        'LAINNYA'       => 'Lainnya',
        'PERLENGKAPAN'  => 'Perlengkapan/Habis Pakai',
        'PERALATAN'     => 'Peralatan',
        'OBAT'          => 'Obat/Multivitamin',
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

    /**
     * NEW: Get month name in Indonesian for export
     */
    public function getMonthNameAttribute(): string
    {
        return $this->expense_date->format('F Y');
    }

    /**
     * NEW: Get day name in Indonesian for export
     */
    public function getDayNameAttribute(): string
    {
        return $this->expense_date->format('l');
    }

    /**
     * NEW: Get quarter info for export
     */
    public function getQuarterAttribute(): string
    {
        $month = $this->expense_date->month;
        $quarter = ceil($month / 3);
        return "Q{$quarter} " . $this->expense_date->year;
    }

    /**
     * NEW: Get export data array
     */
    public function getExportDataAttribute(): array
    {
        return [
            'id' => $this->id,
            'expense_date' => $this->expense_date->format('d/m/Y'),
            'expense_date_iso' => $this->expense_date->format('Y-m-d'),
            'source' => $this->source,
            'source_label' => $this->source_label,
            'category' => $this->category,
            'category_label' => $this->category_label,
            'description' => $this->description,
            'amount' => $this->amount,
            'formatted_amount' => $this->formatted_amount,
            'month_name' => $this->month_name,
            'day_name' => $this->day_name,
            'quarter' => $this->quarter,
            'year' => $this->expense_date->year,
            'month' => $this->expense_date->month,
            'day' => $this->expense_date->day,
            'week_number' => $this->expense_date->weekOfYear,
            'created_at' => $this->created_at->format('d/m/Y H:i'),
        ];
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

    /**
     * NEW: Scope for current month
     */
    public function scopeCurrentMonth($query)
    {
        return $query->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year);
    }

    /**
     * NEW: Scope for current year
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereYear('expense_date', Carbon::now()->year);
    }

    /**
     * NEW: Scope for date range with better performance
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        if ($endDate) {
            return $query->whereBetween('expense_date', [$startDate, $endDate]);
        }
        return $query->whereDate('expense_date', $startDate);
    }

    /**
     * NEW: Scope for export with optimized query
     */
    public function scopeForExport($query)
    {
        return $query->select([
            'id',
            'expense_date',
            'amount',
            'category',
            'source',
            'description',
            'created_at'
        ]);
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
