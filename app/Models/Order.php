<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'customer_id',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    public static function rules()
    {
        return [
            'business_id' => 'required|exists:businesses,id',
            'customer_id' => 'required|exists:customers,id',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeForBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Helper methods
    public function updateTotalAmount()
    {
        $this->total_amount = $this->items()->sum('subtotal');
        $this->save();
    }

    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
    }

    public function markAsPaid()
    {
        $this->payment_status = 'paid';
        $this->save();
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->status = 'cancelled';
            $this->save();
            return true;
        }
        return false;
    }
} 