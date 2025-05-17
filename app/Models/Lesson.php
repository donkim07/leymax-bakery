<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'name',
        'description',
        'duration',
        'order',
        'status',
        'video_url',
        'attachment_path'
    ];

    protected $casts = [
        'duration' => 'integer',
        'order' => 'integer',
        'status' => 'string'
    ];

    // Relationships
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    // Helper methods
    public function getVideoUrlAttribute($value)
    {
        return $value ? asset($value) : null;
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment_path ? asset('storage/' . $this->attachment_path) : null;
    }

    public function getDurationForHumans()
    {
        if (!$this->duration) {
            return 'N/A';
        }

        $hours = floor($this->duration / 60);
        $minutes = $this->duration % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm', $hours, $minutes);
        }

        return sprintf('%dm', $minutes);
    }
} 