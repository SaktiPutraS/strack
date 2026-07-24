<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceTask extends Model
{
    protected $table = 'maintenance_tasks';

    protected $fillable = [
        'name',
        'schedule_type',
        'schedule_value',
        'notes',
        'last_done_at',
        'interval_km',
        'last_km',
    ];

    protected $casts = [
        'last_done_at' => 'date',
        'interval_km' => 'integer',
        'last_km' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Riwayat penyelesaian (terbaru dulu).
     */
    public function logs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class)->orderByDesc('done_at')->orderByDesc('id');
    }

    // ── Jadwal ──────────────────────────────────────────────────────────────
    public function getScheduleTypeLabelAttribute(): string
    {
        return match ($this->schedule_type) {
            'TEXT' => 'Catatan',
            'DATE' => 'Tanggal',
            'MONTH' => 'Bulan',
            'YEAR' => 'Tahun',
            'ODOMETER' => 'Odometer',
            default => '-',
        };
    }

    public function getScheduleColorAttribute(): string
    {
        return match ($this->schedule_type) {
            'DATE' => 'primary',
            'MONTH' => 'info',
            'YEAR' => 'secondary',
            'ODOMETER' => 'dark',
            'TEXT' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Nomor-nomor bulan (1-12) untuk tipe MONTH.
     */
    public function monthNumbers(): array
    {
        $value = $this->schedule_value;
        if (!$value) {
            return [];
        }
        if (str_contains($value, '-')) {
            // format lama YYYY-MM
            [, $m] = array_pad(explode('-', $value), 2, null);
            return $m ? [(int) $m] : [];
        }
        return collect(explode(',', $value))
            ->filter(fn ($x) => $x !== '')
            ->map(fn ($x) => (int) $x)
            ->all();
    }

    public function getScheduleLabelAttribute(): string
    {
        try {
            switch ($this->schedule_type) {
                case 'DATE':
                    if (!$this->schedule_value) {
                        return '-';
                    }
                    $d = Carbon::parse($this->schedule_value);
                    return $d->day . ' ' . (self::MONTHS[$d->month] ?? '') . ' ' . $d->year;
                case 'MONTH':
                    if (str_contains((string) $this->schedule_value, '-')) {
                        [$y, $m] = array_pad(explode('-', $this->schedule_value), 2, null);
                        return (self::MONTHS[(int) $m] ?? $this->schedule_value) . ' ' . $y;
                    }
                    $names = collect($this->monthNumbers())
                        ->map(fn ($m) => self::MONTHS[$m] ?? $m)
                        ->implode(', ');
                    return $names !== '' ? $names : '-';
                case 'YEAR':
                    return (string) ($this->schedule_value ?: '-');
                case 'ODOMETER':
                    return $this->interval_km ? 'Tiap ' . number_format($this->interval_km, 0, ',', '.') . ' km' : '-';
                case 'TEXT':
                default:
                    return (string) ($this->schedule_value ?: '-');
            }
        } catch (\Throwable $e) {
            return (string) $this->schedule_value;
        }
    }

    /**
     * Odometer target servis berikutnya (untuk tipe ODOMETER).
     */
    public function getNextKmAttribute(): ?int
    {
        if ($this->schedule_type !== 'ODOMETER' || !$this->interval_km) {
            return null;
        }
        return (int) ($this->last_km ?? 0) + (int) $this->interval_km;
    }

    // ── Status todo ─────────────────────────────────────────────────────────
    /**
     * Status: DUE (perlu dikerjakan), SCHEDULED (terjadwal), DONE (selesai).
     */
    public function getStatusAttribute(): string
    {
        $today = Carbon::today();

        switch ($this->schedule_type) {
            case 'DATE':
                if ($this->last_done_at) {
                    return 'DONE';
                }
                if (!$this->schedule_value) {
                    return 'SCHEDULED';
                }
                return $today->gte(Carbon::parse($this->schedule_value)) ? 'DUE' : 'SCHEDULED';

            case 'YEAR':
                $year = (int) $this->schedule_value;
                if ($this->last_done_at && $this->last_done_at->year >= $year) {
                    return 'DONE';
                }
                return $today->year >= $year ? 'DUE' : 'SCHEDULED';

            case 'MONTH':
                $months = $this->monthNumbers();
                if (empty($months)) {
                    return 'SCHEDULED';
                }
                if (!in_array($today->month, $months, true)) {
                    return 'SCHEDULED';
                }
                $doneThisMonth = $this->last_done_at
                    && $this->last_done_at->year === $today->year
                    && $this->last_done_at->month === $today->month;
                return $doneThisMonth ? 'DONE' : 'DUE';

            case 'ODOMETER':
                // Tanpa odometer live, tak bisa dihitung otomatis; tampil sebagai terjadwal (referensi km).
                return 'SCHEDULED';

            case 'TEXT':
            default:
                return $this->last_done_at ? 'DONE' : 'DUE';
        }
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'DUE' => 'Perlu dikerjakan',
            'DONE' => 'Selesai',
            default => 'Terjadwal',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'DUE' => 'danger',
            'DONE' => 'success',
            default => 'secondary',
        };
    }

    public function getStatusSortAttribute(): int
    {
        return match ($this->status) {
            'DUE' => 0,
            'SCHEDULED' => 1,
            default => 2,
        };
    }

    public function getLastDoneLabelAttribute(): ?string
    {
        if (!$this->last_done_at) {
            return null;
        }
        return $this->last_done_at->day . ' ' . (self::MONTHS[$this->last_done_at->month] ?? '') . ' ' . $this->last_done_at->year;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('schedule_value', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
        });
    }
}
