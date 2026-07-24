<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    protected $fillable = [
        'debt_record_id',
        'amount',
        'payment_date',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function debtRecord(): BelongsTo
    {
        return $this->belongsTo(DebtRecord::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->amount, 0, ',', '.');
    }

    protected static function boot()
    {
        parent::boot();

        // Sinkronkan paid_amount + status catatan induk saat pembayaran berubah.
        static::saved(function (DebtPayment $payment) {
            $payment->debtRecord?->recalcFromPayments();
        });

        static::deleted(function (DebtPayment $payment) {
            $payment->debtRecord?->recalcFromPayments();
        });
    }
}
