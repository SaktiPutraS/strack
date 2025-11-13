<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'qty',
        'minimum_stock',
        'order_link',
        'notes',
    ];

    protected $casts = [
        'qty' => 'integer',
        'minimum_stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship
    public function usages()
    {
        return $this->hasMany(SupplyUsage::class);
    }

    // Accessors
    public function getIsLowStockAttribute(): bool
    {
        return $this->qty < $this->minimum_stock;
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->qty == 0) {
            return 'habis';
        } elseif ($this->is_low_stock) {
            return 'rendah';
        }
        return 'normal';
    }

    public function getStockStatusColorAttribute(): string
    {
        return match($this->stock_status) {
            'habis' => 'danger',
            'rendah' => 'warning',
            default => 'success'
        };
    }

    public function getStockStatusTextAttribute(): string
    {
        return match($this->stock_status) {
            'habis' => 'Habis',
            'rendah' => 'Stok Rendah',
            default => 'Normal'
        };
    }

    // Scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('qty', '<', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('qty', 0);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('notes', 'like', "%{$search}%");
    }
}
