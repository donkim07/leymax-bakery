<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssembledItemIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'assembled_item_id',
        'ingredient_id',
        'product_id',
        'assembled_item_id_ref',
        'quantity',
        'unit',
        'cost'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'cost' => 'decimal:2'
    ];

    // Relationships
    public function assembledItem()
    {
        return $this->belongsTo(AssembledItem::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function referencedAssembledItem()
    {
        return $this->belongsTo(AssembledItem::class, 'assembled_item_id_ref');
    }

    // Helper methods
    public function getSourceTypeAttribute()
    {
        if ($this->ingredient_id) {
            return 'ingredient';
        } elseif ($this->product_id) {
            return 'product';
        } elseif ($this->assembled_item_id_ref) {
            return 'assembled_item';
        }
        
        return null;
    }

    public function getSourceAttribute()
    {
        switch ($this->source_type) {
            case 'ingredient':
                return $this->ingredient;
            case 'product':
                return $this->product;
            case 'assembled_item':
                return $this->referencedAssembledItem;
            default:
                return null;
        }
    }

    public function getSourceNameAttribute()
    {
        return $this->source ? $this->source->name : 'Unknown';
    }
} 