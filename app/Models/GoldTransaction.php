<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoldTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'type',
        'grams',
        'total_price',
        'price_per_gram',
        'notes',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'grams' => 'decimal:3',
        'total_price' => 'decimal:2',
        'price_per_gram' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Constants
    const TYPE_BUY = 'BUY';
    const TYPE_SELL = 'SELL';

    // Accessors
    public function getFormattedTotalPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->total_price ?? 0, 0, ',', '.');
    }

    public function getFormattedPricePerGramAttribute(): string
    {
        return 'Rp ' . number_format($this->price_per_gram ?? 0, 0, ',', '.');
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === self::TYPE_BUY ? 'success' : 'warning';
    }

    public function getTypeLabelAttribute(): string
    {
        return $this->type === self::TYPE_BUY ? 'BELI' : 'JUAL';
    }

    // Scopes
    public function scopeBuy($query)
    {
        return $query->where('type', self::TYPE_BUY);
    }

    public function scopeSell($query)
    {
        return $query->where('type', self::TYPE_SELL);
    }

    public function scopeInPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Auto calculate price per gram
        static::saving(function ($transaction) {
            if ($transaction->grams > 0) {
                $transaction->price_per_gram = $transaction->total_price / $transaction->grams;
            }
        });
    }
}
