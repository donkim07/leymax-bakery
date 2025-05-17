<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'name',
        'description',
        'unit',
        'cost_price',
        'stock_quantity',
        'reorder_point',
        'status'
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'stock_quantity' => 'decimal:2',
        'reorder_point' => 'decimal:2'
    ];

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function usages()
    {
        return $this->hasMany(IngredientUsage::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivot('quantity', 'unit')
            ->withTimestamps();
    }
} 