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
        'subcategory',
        'description',
    ];

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Category constants
    const CATEGORIES = [
        'OPERASIONAL' => 'Operasional',
        'MARKETING' => 'Marketing',
        'PENGEMBANGAN' => 'Pengembangan',
        'GAJI_FREELANCE' => 'Gaji & Freelance',
        'ENTERTAINMENT' => 'Entertainment',
        'LAIN_LAIN' => 'Lain-lain',
    ];

    // Subcategory mapping
    const SUBCATEGORIES = [
        'OPERASIONAL' => [
            'hosting_domain' => 'Hosting & Domain',
            'software_tools' => 'Software & Tools',
            'internet_komunikasi' => 'Internet & Komunikasi',
            'listrik_utilitas' => 'Listrik & Utilitas',
        ],
        'MARKETING' => [
            'iklan_online' => 'Iklan Online',
            'promosi_campaign' => 'Promosi & Campaign',
            'content_tools' => 'Content Creation Tools',
        ],
        'PENGEMBANGAN' => [
            'training_course' => 'Training & Course',
            'hardware_equipment' => 'Hardware & Equipment',
            'third_party_services' => 'Third-party Services',
        ],
        'GAJI_FREELANCE' => [
            'gaji_freelancer' => 'Gaji Freelancer',
            'fee_project' => 'Fee Project',
            'bonus_insentif' => 'Bonus & Insentif',
        ],
        'ENTERTAINMENT' => [
            'kopi_makanan' => 'Kopi & Makanan',
            'makan_kerja' => 'Makan Kerja',
            'snack_minuman' => 'Snack & Minuman',
            'entertainment_pribadi' => 'Entertainment Pribadi',
        ],
        'LAIN_LAIN' => [
            'transportasi' => 'Transportasi',
            'pajak_admin' => 'Pajak & Administrasi',
            'misc' => 'Misc Expenses',
        ],
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

    public function getSubcategoryLabelAttribute(): string
    {
        if (!$this->subcategory) {
            return '-';
        }

        $subcategories = self::SUBCATEGORIES[$this->category] ?? [];
        return $subcategories[$this->subcategory] ?? $this->subcategory;
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
        return $query->where(function ($q) use ($search) {
            $q->where('description', 'like', "%{$search}%")
                ->orWhere('subcategory', 'like', "%{$search}%");
        });
    }
}
