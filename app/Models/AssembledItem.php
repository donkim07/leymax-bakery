<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssembledItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'business_id',
        'company_id',
        'name',
        'description',
        'type',
        'unit',
        'selling_price',
        'other_costs',
        'total_cost',
        'assembly_category_id',
        'assembly_group_id',
        'assembly_size_id',
        'category',
        'group',
        'size',
        'created_by'
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'other_costs' => 'decimal:2',
        'total_cost' => 'decimal:2'
    ];

    // Type constants
    const TYPE_SINGLE = 'single';
    const TYPE_PASTE = 'paste';

    // Relationships
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function assemblyCategory()
    {
        return $this->belongsTo(AssemblyCategory::class);
    }

    public function assemblyGroup()
    {
        return $this->belongsTo(AssemblyGroup::class);
    }

    public function assemblySize()
    {
        return $this->belongsTo(AssemblySize::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function ingredients()
    {
        return $this->hasMany(AssembledItemIngredient::class);
    }
    

    public function pasteDivisions()
    {
        return $this->hasMany(PasteDivision::class);
    }

    public function outputDivisions()
    {
        return $this->hasMany(PasteDivision::class, 'output_assembled_item_id');
    }

    // Scopes
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function isSingleItem()
    {
        return $this->type === self::TYPE_SINGLE;
    }

    public function isDecoratedItem()
    {
        return $this->type === self::TYPE_DECORATED;
    }

    public function isPaste()
    {
        return $this->type === self::TYPE_PASTE;
    }
    /**
     * Calculate the cost per unit
     */
    public function calculateUnitCost()
    {
        // If we have a unit quantity defined, divide by that
        $unitQuantity = 1;
        
        return $this->total_cost / $unitQuantity;
    }

    public function calculateTotalCost()
    {
        $ingredientCost = $this->ingredients()->sum('cost');
        return $ingredientCost + $this->other_costs;
    }

    public function updateTotalCost()
    {
        $this->total_cost = $this->calculateTotalCost();
        $this->save();
    }
} 