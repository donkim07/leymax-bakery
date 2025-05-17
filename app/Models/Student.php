<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'status',
        'notes'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'student_courses')
            ->withPivot('status', 'progress', 'completed_at')
            ->withTimestamps();
    }

    public function progress()
    {
        return $this->hasMany(StudentProgress::class);
    }
} 