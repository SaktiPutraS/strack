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
        'description',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Updated Category constants
    const CATEGORIES = [
        // urutkan sesuai dengan yang ada di database
        'AI' => 'AI',
        'ADMIN_BANK' => 'Admin Bank',
        'BUKU' => 'Buku',
        'DOMAIN_HOSTING' => 'Domain/Hosting',
        'ENTERTAIN' => 'Entertain/Jajan/Traktir',
        'GAJI_BONUS' => 'Gaji/Bonus',
        'KOPI_SUSU' => 'Kopi/Susu',
        'INVENTARIS' => 'Inventaris',
        'BENSIN' => 'Bensin',
        'SALDO_AWAL' => 'Saldo Awal',
        'LAINNYA' => 'Lainnya',
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

    // Scopes
    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', "%{$search}%");
    }
}
