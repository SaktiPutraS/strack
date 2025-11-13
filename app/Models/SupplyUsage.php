<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'supply_id',
        'qty_used',
        'usage_date',
        'notes',
    ];

    protected $casts = [
        'qty_used' => 'integer',
        'usage_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship
    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Update supply qty when usage is created
        static::created(function ($usage) {
            $supply = $usage->supply;
            $supply->decrement('qty', $usage->qty_used);
        });

        // Restore supply qty when usage is deleted
        static::deleted(function ($usage) {
            $supply = $usage->supply;
            $supply->increment('qty', $usage->qty_used);
        });
    }
}
