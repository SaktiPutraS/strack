<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UrfavShopeeProduct extends Model
{
    protected $table = 'urfav_shopee_products';

    protected $fillable = [
        'jakmall_sku',
        'jakmall_harga',
        'jakmall_stock',
        'shopee_urut',
        'shopee_id',
        'shopee_sku',
        'shopee_harga',
        'shopee_margin',
        'shopee_stock'
    ];

    protected $casts = [
        'jakmall_harga' => 'decimal:2',
        'shopee_harga' => 'decimal:2',
        'shopee_margin' => 'decimal:2',
        'shopee_stock' => 'integer',
        'shopee_urut' => 'integer'
    ];

    // Scope untuk ordering berdasarkan shopee_urut
    public function scopeOrderedByShopee($query)
    {
        return $query->orderBy('shopee_urut', 'asc');
    }

    // Method untuk hitung shopee_harga
    public function calculateShopeePrice()
    {
        if ($this->shopee_margin && $this->jakmall_harga) {
            return ($this->shopee_margin / 100 * $this->jakmall_harga) + $this->jakmall_harga;
        }
        return 0;
    }

    // Method untuk hitung shopee_stock
    public function calculateShopeeStock()
    {
        return $this->jakmall_stock === 'tersedia' ? 10 : 0;
    }
}
