<?php
// app/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Client has many projects
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    /**
     * Get active projects for this client
     */
    public function activeProjects(): HasMany
    {
        return $this->projects()->whereIn('status', ['WAITING', 'PROGRESS']);
    }

    /**
     * Get finished projects for this client
     */
    public function finishedProjects(): HasMany
    {
        return $this->projects()->where('status', 'FINISHED');
    }

    /**
     * Get total value of all projects
     */
    public function getTotalProjectValueAttribute(): float
    {
        return $this->projects()->sum('total_value');
    }

    /**
     * Get total paid amount from all projects
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->projects()->sum('paid_amount');
    }

    /**
     * Get total remaining amount from all projects
     */
    public function getTotalRemainingAttribute(): float
    {
        return $this->projects()->sum(DB::raw('total_value - paid_amount'));
    }

    /**
     * Format phone number for WhatsApp link
     */
    public function getWhatsappLinkAttribute(): string
    {
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        return "https://api.whatsapp.com/send?phone={$phone}";
    }

    /**
     * Scope: Search clients
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }
}
