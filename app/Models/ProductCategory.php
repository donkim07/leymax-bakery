<?php

namespace App\Models;

use Illuminate\Support\Str;

class ProductCategory extends Category
{
    protected static function boot()
    {
        parent::boot();

        // Always append 'bakery-' to the slug to identify bakery categories
        static::creating(function ($category) {
            if (!Str::startsWith($category->slug ?? '', 'bakery-')) {
                $category->slug = 'bakery-' . $category->business_id . '-' . Str::slug($category->name);
            }
        });
    }

    /**
     * Get the products for this category.
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }

    /**
     * Get the business that owns this category.
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope query to only include bakery categories
     */
    public function scopeBakeryCategories($query)
    {
        return $query->where('slug', 'like', 'bakery-%');
    }
} 