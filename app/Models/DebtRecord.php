<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DebtRecord extends Model
{
    protected $fillable = [
        'type',
        'party_name',
        'title',
        'principal_amount',
        'paid_amount',
        'status',
        'due_date',
        'notes',
    ];

    protected $casts = [
        'principal_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Riwayat pembayaran/cicilan untuk catatan ini.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    /**
     * Hitung ulang paid_amount dari total pembayaran, lalu simpan.
     * Status LUNAS/BERJALAN dihitung otomatis lewat event saving.
     */
    public function recalcFromPayments(): void
    {
        $this->paid_amount = $this->payments()->sum('amount');
        $this->save();
    }

    // ── Accessor nilai ──────────────────────────────────────────────────────
    public function getRemainingAmountAttribute(): float
    {
        return max(0, (float) $this->principal_amount - (float) $this->paid_amount);
    }

    public function getProgressPercentageAttribute(): int
    {
        if ((float) $this->principal_amount <= 0) {
            return 0;
        }
        return (int) min(100, round((float) $this->paid_amount / (float) $this->principal_amount * 100));
    }

    public function getFormattedPrincipalAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->principal_amount, 0, ',', '.');
    }

    public function getFormattedPaidAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->paid_amount, 0, ',', '.');
    }

    public function getFormattedRemainingAttribute(): string
    {
        return 'Rp ' . number_format($this->remaining_amount, 0, ',', '.');
    }

    // ── Accessor label / warna ──────────────────────────────────────────────
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'HUTANG' ? 'Hutang' : 'Piutang';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->type === 'HUTANG' ? 'danger' : 'success';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'PAID' ? 'Lunas' : 'Berjalan';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'PAID' ? 'success' : 'warning';
    }

    // ── Jatuh tempo ─────────────────────────────────────────────────────────
    public function getIsOverdueAttribute(): bool
    {
        if (!$this->due_date || $this->status === 'PAID') {
            return false;
        }
        return Carbon::now()->startOfDay()->gt($this->due_date);
    }

    public function getIsDueSoonAttribute(): bool
    {
        if (!$this->due_date || $this->status === 'PAID') {
            return false;
        }
        $daysUntil = Carbon::now()->startOfDay()->diffInDays($this->due_date, false);
        return $daysUntil >= 0 && $daysUntil <= 7;
    }

    // ── Scopes ──────────────────────────────────────────────────────────────
    public function scopeHutang($query)
    {
        return $query->where('type', 'HUTANG');
    }

    public function scopePiutang($query)
    {
        return $query->where('type', 'PIUTANG');
    }

    public function scopeOngoing($query)
    {
        return $query->where('status', 'ONGOING');
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('party_name', 'like', "%{$search}%")
                ->orWhere('title', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }

    protected static function boot()
    {
        parent::boot();

        // Status LUNAS bila terbayar penuh, selain itu BERJALAN.
        static::saving(function (DebtRecord $record) {
            $record->status = ((float) $record->principal_amount > 0
                && (float) $record->paid_amount >= (float) $record->principal_amount)
                ? 'PAID'
                : 'ONGOING';
        });
    }
}
