<?php
// app/Models/Task.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'schedule',
        'status',
        'admin_id',
        'target_date' // Tambahan untuk tugas sekali kerja
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'target_date' => 'date', // Tambahan untuk tugas sekali kerja
    ];

    // Relationship dengan task assignments
    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    // Mendapatkan assignment untuk user tertentu pada tanggal tertentu
    public function getAssignmentForUserAndDate($userId, $date)
    {
        return $this->assignments()
            ->where('user_id', $userId)
            ->where('assigned_date', $date)
            ->first();
    }

    // Cek apakah task berlaku untuk tanggal tertentu
    public function isApplicableForDate(Carbon $date)
    {
        if ($this->status !== 'active') {
            return false;
        }

        switch ($this->schedule) {
            case 'daily':
                // Senin-Jumat (1=Senin, 5=Jumat)
                return $date->isWeekday();

            case 'weekly':
                // Setiap hari Senin
                return $date->isMonday();

            case 'monthly':
                // Tanggal 1 setiap bulan
                return $date->day === 1;

            case 'once':
                // Tugas sekali kerja pada tanggal target
                return $this->target_date && $date->isSameDay($this->target_date);

            default:
                return false;
        }
    }

    // Scope untuk task yang aktif
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope untuk task berdasarkan schedule
    public function scopeBySchedule($query, $schedule)
    {
        return $query->where('schedule', $schedule);
    }

    // Mendapatkan task yang berlaku untuk hari ini
    public static function getTasksForDate(Carbon $date)
    {
        $tasks = self::active()->get();

        return $tasks->filter(function ($task) use ($date) {
            return $task->isApplicableForDate($date);
        });
    }

    // Mendapatkan schedule dalam bahasa Indonesia
    public function getScheduleTextAttribute()
    {
        $scheduleTexts = [
            'daily' => 'Senin-Jumat (Setiap Hari)',
            'weekly' => 'Seminggu Sekali (Senin)',
            'monthly' => 'Sebulan Sekali (Tanggal 1)',
            'once' => 'Sekali Kerja' . ($this->target_date ? ' (' . $this->target_date->format('d M Y') . ')' : '')
        ];

        return $scheduleTexts[$this->schedule] ?? $this->schedule;
    }

    // Mendapatkan status dalam bahasa Indonesia
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif'
        ];

        return $statusTexts[$this->status] ?? $this->status;
    }
}
