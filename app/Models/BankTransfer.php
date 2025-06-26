<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'transfer_date',
        'transfer_amount',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'transfer_date' => 'date',
        'transfer_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    // Accessors
    public function getFormattedTransferAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->transfer_amount ?? 0, 0, ',', '.');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // Update payment transfer status when bank transfer created
        static::created(function ($transfer) {
            $transfer->payment->update(['is_transferred' => true]);
        });

        // Update payment transfer status when bank transfer deleted
        static::deleted(function ($transfer) {
            $transfer->payment->update(['is_transferred' => false]);
        });
    }
}
