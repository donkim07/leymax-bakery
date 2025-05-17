<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Instructor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'bio',
        'specialization',
        'status',
        'avatar_path',
        'hourly_rate',
        'availability'
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'availability' => 'array',
        'status' => 'string'
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'active')
            ->whereNotNull('availability');
    }
} 