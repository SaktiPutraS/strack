<?php
// app/Models/TaskAssignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'user_id',
        'assigned_date',
        'status',
        'remarks',
        'attachment',
        'submitted_at',
        'validated_at'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'submitted_at' => 'datetime',
        'validated_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship dengan task
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Mendapatkan status dalam bahasa Indonesia
    public function getStatusTextAttribute()
    {
        $statusTexts = [
            'pending' => 'Belum Dikerjakan',
            'dikerjakan' => 'Dikerjakan',
            'valid' => 'Valid'
        ];

        return $statusTexts[$this->status] ?? $this->status;
    }

    // Mendapatkan warna status untuk tampilan
    public function getStatusColorAttribute()
    {
        $statusColors = [
            'pending' => 'warning',
            'dikerjakan' => 'info',
            'valid' => 'success'
        ];

        return $statusColors[$this->status] ?? 'secondary';
    }

    // Scope untuk status tertentu
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk user tertentu
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope untuk tanggal tertentu
    public function scopeByDate($query, $date)
    {
        return $query->where('assigned_date', $date);
    }

    // Scope untuk tugas yang sudah dikerjakan dan perlu validasi
    public function scopeNeedValidation($query)
    {
        return $query->where('status', 'dikerjakan');
    }

    // Method untuk submit tugas
    public function submitTask($remarks, $attachment = null)
    {
        $this->update([
            'status' => 'dikerjakan',
            'remarks' => $remarks,
            'attachment' => $attachment,
            'submitted_at' => now()
        ]);
    }

    // Method untuk validasi tugas oleh admin
    public function validateTask()
    {
        $this->update([
            'status' => 'valid',
            'validated_at' => now()
        ]);
    }

    // Cek apakah tugas sudah dikerjakan
    public function isSubmitted()
    {
        return in_array($this->status, ['dikerjakan', 'valid']);
    }

    // Cek apakah tugas sudah divalidasi
    public function isValidated()
    {
        return $this->status === 'valid';
    }

    // Method untuk mendapatkan URL attachment jika ada
    public function getAttachmentUrlAttribute()
    {
        if ($this->attachment) {
            return asset('storage/task-attachments/' . $this->attachment);
        }
        return null;
    }

    // Method untuk mendapatkan nama file attachment tanpa path
    public function getAttachmentNameAttribute()
    {
        if ($this->attachment) {
            return basename($this->attachment);
        }
        return null;
    }
}
