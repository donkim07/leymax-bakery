<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'barcode',
        'description',
        'cost_price',
        'selling_price',
        'wholesale_price',
        'discount_price',
        'discount_start_date',
        'discount_end_date',
        'unit',
        'is_featured',
        'is_digital',
        'track_inventory',
        'alert_quantity',
        'type',
        'attributes',
        'metadata',
        'is_active',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'track_inventory' => 'boolean',
        'is_active' => 'boolean',
        'attributes' => 'array',
        'metadata' => 'array',
        'discount_start_date' => 'datetime',
        'discount_end_date' => 'datetime',
    ];

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->name);
            }
            if (empty($product->sku)) {
                $product->sku = static::generateSku();
            }
        });
    }

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function inventoryMovements()
    {
        return $this->hasMany(InventoryMovement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeInStock($query)
    {
        return $query->whereHas('inventory', function ($query) {
            $query->where('available_quantity', '>', 0);
        });
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('inventory', function ($query) {
            $query->whereRaw('available_quantity <= alert_quantity');
        });
    }

    // Helper methods
    public static function generateSku()
    {
        do {
            $sku = strtoupper(Str::random(8));
        } while (static::where('sku', $sku)->exists());

        return $sku;
    }

    public function getCurrentPrice()
    {
        if ($this->isOnDiscount()) {
            return $this->discount_price;
        }
        return $this->selling_price;
    }

    public function isOnDiscount()
    {
        if (!$this->discount_price) {
            return false;
        }

        $now = now();
        return $this->discount_price > 0 &&
            (!$this->discount_start_date || $this->discount_start_date <= $now) &&
            (!$this->discount_end_date || $this->discount_end_date >= $now);
    }

    public function getDiscountPercentageAttribute()
    {
        if (!$this->isOnDiscount()) {
            return 0;
        }

        return round((($this->selling_price - $this->discount_price) / $this->selling_price) * 100);
    }

    public function getTotalStockAttribute()
    {
        return $this->inventory()->sum('available_quantity');
    }

    public function isBakeryProduct()
    {
        return $this->type === 'bakery';
    }

    public function isCakeTool()
    {
        return $this->type === 'cake_tool';
    }
}