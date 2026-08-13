<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    /** Ambang "akan habis" dalam hari. */
    public const EXPIRING_DAYS = 30;

    protected $fillable = [
        'name',
        'client_id',
        'project_id',
        'provider',
        'registered_at',
        'expires_at',
        'renewal_cost',
        'is_hosted',
        'notes',
    ];

    protected $casts = [
        'registered_at' => 'date',
        'expires_at' => 'date',
        'renewal_cost' => 'decimal:2',
        'is_hosted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /** Sisa hari menuju kedaluwarsa (negatif jika sudah lewat), null bila tak ada tanggal. */
    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->expires_at) {
            return null;
        }

        return (int) Carbon::now()->startOfDay()->diffInDays($this->expires_at->startOfDay(), false);
    }

    /** Status: EXPIRED / EXPIRING_SOON / ACTIVE / UNKNOWN. */
    public function getStatusAttribute(): string
    {
        $days = $this->days_until_expiry;

        if ($days === null) {
            return 'UNKNOWN';
        }

        if ($days < 0) {
            return 'EXPIRED';
        }

        if ($days <= self::EXPIRING_DAYS) {
            return 'EXPIRING_SOON';
        }

        return 'ACTIVE';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'EXPIRED' => 'Kedaluwarsa',
            'EXPIRING_SOON' => 'Akan Habis',
            'ACTIVE' => 'Aktif',
            default => 'Tanpa Tanggal',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'EXPIRED' => 'danger',
            'EXPIRING_SOON' => 'warning',
            'ACTIVE' => 'success',
            default => 'secondary',
        };
    }

    public function getFormattedRenewalCostAttribute(): string
    {
        return $this->renewal_cost !== null
            ? 'Rp ' . number_format((float) $this->renewal_cost, 0, ',', '.')
            : '-';
    }

    // ── Scopes ────────────────────────────────────────────────────────────────
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('provider', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%")
                ->orWhereHas('client', fn ($c) => $c->where('name', 'like', "%{$search}%"));
        });
    }

    /** Domain yang akan habis dalam N hari (termasuk yang sudah lewat). */
    public function scopeExpiringWithin($query, int $days)
    {
        return $query->whereNotNull('expires_at')
            ->whereDate('expires_at', '<=', Carbon::now()->addDays($days)->toDateString());
    }
}
