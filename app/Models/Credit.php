<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'customer_id',
        'number',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Accessors
    public function getCustomerNameAttribute()
    {
        return $this->customer ? $this->customer->name : 'Guest';
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'settled' => 'success',
            'active' => 'warning',
            'overdue' => 'danger',
            default => 'primary'
        };
    }
} 