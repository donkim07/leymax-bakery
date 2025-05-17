<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });

        static::updating(function ($item) {
            $item->subtotal = $item->quantity * $item->price;
        });
    }

    public static function rules()
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ];
    }

    // Scopes
    public function scopeForProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForOrder($query, $orderId)
    {
        return $query->where('order_id', $orderId);
    }

    // Helper methods
    public function updateQuantity($quantity)
    {
        $this->quantity = $quantity;
        $this->save();
        
        // Update order total
        $this->order->updateTotalAmount();
    }

    public function updatePrice($price)
    {
        $this->price = $price;
        $this->save();
        
        // Update order total
        $this->order->updateTotalAmount();
    }

    public function getDiscountAmount()
    {
        $product = $this->product;
        if ($product && $product->isOnDiscount()) {
            return ($product->selling_price - $product->discount_price) * $this->quantity;
        }
        return 0;
    }
} 