<?php
// app/Models/Testimonial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'content',
        'rating',
        'is_published',
        'client_photo',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_published' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Testimonial belongs to project
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Get client through project relationship
     */
    public function getClientAttribute()
    {
        return $this->project->client;
    }

    /**
     * Get star rating as HTML
     */
    public function getStarRatingAttribute(): string
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '<i class="fas fa-star text-yellow-400"></i>';
            } else {
                $stars .= '<i class="far fa-star text-gray-300"></i>';
            }
        }
        return $stars;
    }

    /**
     * Get rating color for UI
     */
    public function getRatingColorAttribute(): string
    {
        return match (true) {
            $this->rating >= 5 => 'green',
            $this->rating >= 4 => 'blue',
            $this->rating >= 3 => 'yellow',
            $this->rating >= 2 => 'orange',
            default => 'red'
        };
    }

    /**
     * Get truncated content for preview
     */
    public function getPreviewContentAttribute(): string
    {
        return strlen($this->content) > 100
            ? substr($this->content, 0, 100) . '...'
            : $this->content;
    }

    /**
     * Scope: Published testimonials
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope: High rating testimonials (4-5 stars)
     */
    public function scopeHighRating($query)
    {
        return $query->where('rating', '>=', 4);
    }

    /**
     * Scope: Recent testimonials
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();

        // Update project's has_testimonial when testimonial is created
        static::created(function ($testimonial) {
            $testimonial->project->update(['has_testimonial' => true]);
        });

        // Update project's has_testimonial when testimonial is deleted
        static::deleted(function ($testimonial) {
            $hasOtherTestimonials = static::where('project_id', $testimonial->project_id)->exists();
            $testimonial->project->update(['has_testimonial' => $hasOtherTestimonials]);
        });
    }
}
