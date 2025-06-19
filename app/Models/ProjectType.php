<?php
// app/Models/ProjectType.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: ProjectType has many projects
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'type', 'name');
    }

    /**
     * Scope: Active project types only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by sort_order and name
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get formatted name for display
     */
    public function getFormattedNameAttribute(): string
    {
        return $this->display_name ?: $this->name;
    }

    /**
     * Auto-generate display name if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($projectType) {
            if (empty($projectType->display_name)) {
                $projectType->display_name = ucwords(strtolower(str_replace(['_', '-'], ' ', $projectType->name)));
            }

            if ($projectType->sort_order === 0) {
                $maxOrder = static::max('sort_order') ?: 0;
                $projectType->sort_order = $maxOrder + 10;
            }
        });
    }
}
