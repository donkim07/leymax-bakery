<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturingProcessIngredient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'manufacturing_process_id',
        'ingredient_id',
        'product_id',
        'assembled_item_id_ref',
        'quantity',
        'unit',
        'cost',
        'notes',
    ];

    public function manufacturingProcess()
    {
        return $this->belongsTo(ManufacturingProcess::class);
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
} 