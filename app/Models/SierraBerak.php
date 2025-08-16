<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SierraBerak extends Model
{
    use HasFactory;

    protected $table = 'sierra_berak';

    protected $fillable = [
        'tanggal',
        'waktu',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu' => 'datetime:H:i'
    ];

    public function getFormattedWaktuAttribute()
    {
        return Carbon::parse($this->waktu)->format('H:i');
    }

    public function getFormattedTanggalAttribute()
    {
        return $this->tanggal->format('d M Y');
    }

    public function getFormattedTanggalLengkapAttribute()
    {
        return $this->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    public function scopeByMonth($query, $year, $month)
    {
        return $query->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal', $year);
    }

    public static function today()
    {
        return self::byDate(now()->toDateString())
            ->orderBy('waktu')
            ->get();
    }

    public static function thisMonth()
    {
        return self::byMonth(now()->year, now()->month)
            ->orderBy('tanggal')
            ->orderBy('waktu')
            ->get();
    }
}
