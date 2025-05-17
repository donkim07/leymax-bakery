<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientUsage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ingredient_id',
        'order_item_id',
        'quantity',
        'unit',
        'cost',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'cost' => 'decimal:2'
    ];

    // Relationships
    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
} 