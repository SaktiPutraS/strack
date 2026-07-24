<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class MaintenanceTask extends Model
{
    protected $table = 'maintenance_tasks';

    protected $fillable = [
        'name',
        'schedule_type',
        'schedule_value',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    private const MONTHS = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * Label tipe jadwal untuk UI.
     */
    public function getScheduleTypeLabelAttribute(): string
    {
        return match ($this->schedule_type) {
            'TEXT' => 'Catatan',
            'DATE' => 'Tanggal',
            'MONTH' => 'Bulan',
            'YEAR' => 'Tahun',
            default => '-',
        };
    }

    /**
     * Warna badge tipe jadwal.
     */
    public function getScheduleColorAttribute(): string
    {
        return match ($this->schedule_type) {
            'DATE' => 'primary',
            'MONTH' => 'info',
            'YEAR' => 'secondary',
            'TEXT' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Jadwal yang sudah diformat untuk ditampilkan.
     */
    public function getScheduleLabelAttribute(): string
    {
        $value = $this->schedule_value;
        if ($value === null || $value === '') {
            return '-';
        }

        try {
            switch ($this->schedule_type) {
                case 'DATE':
                    $d = Carbon::parse($value);
                    return $d->day . ' ' . (self::MONTHS[$d->month] ?? '') . ' ' . $d->year;
                case 'MONTH':
                    [$y, $m] = array_pad(explode('-', $value), 2, null);
                    return (self::MONTHS[(int) $m] ?? $value) . ' ' . $y;
                case 'YEAR':
                    return $value;
                case 'TEXT':
                default:
                    return $value;
            }
        } catch (\Throwable $e) {
            return $value;
        }
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
