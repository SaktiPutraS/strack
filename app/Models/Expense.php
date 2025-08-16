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
        'AI'            => 'AI',
        'ADMIN_BANK'    => 'Admin Bank',
        'BENSIN'        => 'Bensin',
        'BUKU'          => 'Buku',
        'DOMAIN_HOSTING' => 'Domain/Hosting',
        'ENTERTAIN'     => 'Entertain/Jajan',
        'GAJI_BONUS'    => 'Gaji/Bonus',
        'INVENTARIS'    => 'Inventaris',
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
