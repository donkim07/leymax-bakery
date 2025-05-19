<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasteDivision extends Model
{
    use HasFactory;

    protected $fillable = [
        'assembled_item_id',
        'output_assembled_item_id',
        'quantity',
        'unit',
        'flavor',
        'flavor_quantity',
        'flavor_unit',
        'flavor_cost',
        'waste_quantity'
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'flavor_quantity' => 'decimal:3',
        'flavor_cost' => 'decimal:2',
        'waste_quantity' => 'decimal:3'
    ];

    // Relationships
    public function pasteItem()
    {
        return $this->belongsTo(AssembledItem::class, 'assembled_item_id');
    }

    public function outputItem()
    {
        return $this->belongsTo(AssembledItem::class, 'output_assembled_item_id');
    }

    public function outputAssembledItem()
    {
        return $this->outputItem();
    }

    // Helper methods
    public function calculateWastePercentage()
    {
        if (!$this->waste_quantity) {
            return 0;
        }
        
        $originalQuantity = $this->pasteItem->ingredients->sum(function ($ingredient) {
            return $ingredient->quantity;
        });
        
        if ($originalQuantity <= 0) {
            return 0;
        }
        
        return ($this->waste_quantity / $originalQuantity) * 100;
    }
} 