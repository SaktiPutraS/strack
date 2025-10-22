<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prospect extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'whatsapp',
        'address',
        'social_media',
        'notes',
        'status'
    ];

    // Status labels untuk display
    public static function getStatusLabels()
    {
        return [
            'BELUM_DIHUBUNGI' => 'Belum Dihubungi',
            'PENGECEKAN_KEAKTIFAN' => 'Pengecekan Keaktifan Usaha',
            'PENAWARAN' => 'Penawaran',
            'FOLLOW_UP' => 'Follow Up Penawaran',
            'TOLAK' => 'Tolak'
        ];
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        $labels = self::getStatusLabels();
        return $labels[$this->status] ?? $this->status;
    }

    // Accessor untuk status badge color
    public function getStatusColorAttribute()
    {
        $colors = [
            'BELUM_DIHUBUNGI' => 'secondary',
            'PENGECEKAN_KEAKTIFAN' => 'info',
            'PENAWARAN' => 'warning',
            'FOLLOW_UP' => 'purple',
            'TOLAK' => 'danger'
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    // Accessor untuk WhatsApp link
    public function getWhatsappLinkAttribute()
    {
        $number = preg_replace('/[^0-9]/', '', $this->whatsapp);
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }
        return 'https://api.whatsapp.com/send?phone=' . $number;
    }
}
