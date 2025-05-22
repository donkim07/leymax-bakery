<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductionPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'company_id',
        'assembled_item_id',
        'title',
        'description',
        'scheduled_date',
        'scheduled_time',
        'quantity',
        'unit',
        'priority', // high, normal, low
        'status', // planned, in_progress, completed, cancelled
        'resource_allocation',
        'notes',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'resource_allocation' => 'array',
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function assembledItem()
    {
        return $this->belongsTo(AssembledItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function materialRequirements()
    {
        return $this->hasMany(ProductionPlanMaterial::class);
    }
} 