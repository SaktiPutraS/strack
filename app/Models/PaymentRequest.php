<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'order_id',
        'gateway',
        'amount',
        'status',
        'payment_url',
        'snap_token',
        'gateway_ref',
        'paid_at',
        'expired_at',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'raw_response' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: tagihan milik satu project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Format nominal
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount ?? 0, 0, ',', '.');
    }

    /**
     * Sudah dibayar?
     */
    public function getIsPaidAttribute(): bool
    {
        return $this->status === 'PAID';
    }

    /**
     * Masih bisa dibayar (PENDING & belum kedaluwarsa)?
     */
    public function getIsPayableAttribute(): bool
    {
        if ($this->status !== 'PENDING') {
            return false;
        }
        return is_null($this->expired_at) || $this->expired_at->isFuture();
    }

    /**
     * Warna badge status untuk UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'PAID' => 'success',
            'PENDING' => 'warning',
            'EXPIRED', 'CANCELLED' => 'secondary',
            'FAILED' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Scope: tagihan yang masih menunggu pembayaran
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }
}
