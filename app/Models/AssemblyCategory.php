<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssemblyCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'company_id',
        'name',
        'description',
        'is_active'
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function assembledItems()
    {
        return $this->hasMany(AssembledItem::class);
    }
} 