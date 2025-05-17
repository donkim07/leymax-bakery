<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'store_id',
        'product_id',
        'quantity',
        'reserved_quantity',
        'available_quantity',
        'rack_location',
        'bin_location',
        'batch_number',
        'expiry_date',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reserved_quantity' => 'decimal:2',
        'available_quantity' => 'decimal:2',
        'expiry_date' => 'date',
    ];

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

    public function movements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Scopes
    public function scopeInStock($query)
    {
        return $query->where('available_quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('available_quantity <= products.alert_quantity')
            ->join('products', 'products.id', '=', 'inventory.product_id');
    }

    public function scopeExpiring($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<=', now()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', now());
    }

    // Helper methods
    public function isLowStock()
    {
        return $this->available_quantity <= $this->product->alert_quantity;
    }

    public function isOutOfStock()
    {
        return $this->available_quantity <= 0;
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function isExpiring($days = 30)
    {
        return $this->expiry_date && 
            $this->expiry_date->isFuture() && 
            $this->expiry_date->lte(now()->addDays($days));
    }

    public function getLocationAttribute()
    {
        $parts = array_filter([
            $this->rack_location,
            $this->bin_location,
        ]);

        return implode(' - ', $parts);
    }
} 