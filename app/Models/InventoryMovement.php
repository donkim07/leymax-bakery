<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'store_id',
        'product_id',
        'type',
        'quantity',
        'reference_type',
        'reference_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
    ];

    // Constants
    const TYPE_IN = 'in';
    const TYPE_OUT = 'out';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_ADJUSTMENT = 'adjustment';

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInflow($query)
    {
        return $query->where('type', self::TYPE_IN);
    }

    public function scopeOutflow($query)
    {
        return $query->where('type', self::TYPE_OUT);
    }

    public function scopeTransfers($query)
    {
        return $query->where('type', self::TYPE_TRANSFER);
    }

    public function scopeAdjustments($query)
    {
        return $query->where('type', self::TYPE_ADJUSTMENT);
    }

    // Helper methods
    public function isInflow()
    {
        return $this->type === self::TYPE_IN;
    }

    public function isOutflow()
    {
        return $this->type === self::TYPE_OUT;
    }

    public function isTransfer()
    {
        return $this->type === self::TYPE_TRANSFER;
    }

    public function isAdjustment()
    {
        return $this->type === self::TYPE_ADJUSTMENT;
    }

    public function getFormattedQuantityAttribute()
    {
        $prefix = $this->isInflow() ? '+' : '-';
        return $prefix . number_format($this->quantity, 2);
    }
} 