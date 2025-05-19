<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturingProcess extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'company_id',
        'assembled_item_id',
        'batch_number',
        'quantity',
        'status',
        'scheduled_date',
        'completed_date',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'scheduled_date' => 'datetime',
        'completed_date' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    public function assembledItem()
    {
        return $this->belongsTo(AssembledItem::class);
    }

    public function ingredients()
    {
        return $this->hasMany(ManufacturingProcessIngredient::class);
    }

    public function waste()
    {
        return $this->hasMany(ManufacturingWaste::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
} 