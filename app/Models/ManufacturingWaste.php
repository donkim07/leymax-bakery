<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ManufacturingWaste extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'manufacturing_process_id',
        'manufacturing_process_ingredient_id',
        'ingredient_id',
        'product_id',
        'assembled_item_id_ref',
        'source_name',
        'source_type',
        'used_amount',
        'waste_amount',
        'unit',
        'waste_percentage',
        'reason',
        'recorded_by',
        'recorded_at',
    ];

    protected $casts = [
        'waste_percentage' => 'float',
        'recorded_at' => 'datetime',
    ];

    public function manufacturingProcess()
    {
        return $this->belongsTo(ManufacturingProcess::class);
    }

    public function manufacturingProcessIngredient()
    {
        return $this->belongsTo(ManufacturingProcessIngredient::class);
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

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
} 