<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'supplier_id',
        'number',
        'purchase_id',
        'amount',
        'paid_amount',
        'due_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'due_date' => 'datetime'
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Accessors
    public function getSupplierNameAttribute()
    {
        return $this->supplier ? $this->supplier->name : 'Unknown';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'paid' => 'success',
            'pending' => 'warning',
            'overdue' => 'danger',
            default => 'primary'
        };
    }
} 