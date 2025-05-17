<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'duration',
        'price',
        'status',
        'start_date',
        'end_date',
        'max_students',
        'image_path'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'price' => 'decimal:2',
        'max_students' => 'integer',
        'status' => 'string'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_courses')
            ->withPivot('status', 'progress', 'completed_at')
            ->withTimestamps();
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }

    public function studentProgress()
    {
        return $this->hasMany(StudentProgress::class);
    }

    public function orderItems()
    {
        return $this->morphMany(OrderItem::class, 'orderable');
    }
} 