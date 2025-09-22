<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CalendarNote extends Model
{
    protected $fillable = [
        'user_id',
        'date',
        'title',
        'content'
    ];

    protected $casts = [
        'date' => 'date'
    ];

    public static function getNotesForMonth($userId, $year, $month)
    {
        return self::where('user_id', $userId)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get()
            ->keyBy(function ($note) {
                return $note->date->day;
            });
    }

    public static function getNoteForDate($userId, $date)
    {
        return self::where('user_id', $userId)
            ->where('date', $date)
            ->first();
    }
}
