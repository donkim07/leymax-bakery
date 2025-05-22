<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'business_id',
        'store_id',
        'customer_id',
        'cashier_id',
        'payment_method_id',
        'invoice_number',
        'sub_total',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'amount_paid',
        'change_amount',
        'notes',
        'status',
    ];

    /**
     * Sale status constants
     */
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_PARTIAL_REFUND = 'partial_refund';

    /**
     * Get the business that owns the sale
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Get the store that owns the sale
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the customer associated with the sale
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the cashier (user) who processed the sale
     */
    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * Get the payment method used for the sale
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the items for the sale
     */
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    /**
     * Get the refunds for the sale
     */
    public function refunds()
    {
        return $this->hasMany(SaleRefund::class);
    }

    /**
     * Scope a query to only include completed sales
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Scope a query to only include cancelled sales
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', self::STATUS_CANCELLED);
    }

    /**
     * Scope a query to only include refunded sales
     */
    public function scopeRefunded($query)
    {
        return $query->whereIn('status', [self::STATUS_REFUNDED, self::STATUS_PARTIAL_REFUND]);
    }

    /**
     * Check if the sale has been paid in full
     */
    public function isPaid()
    {
        return $this->amount_paid >= $this->total_amount;
    }
} 