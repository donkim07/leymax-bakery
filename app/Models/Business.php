<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'registration_number',
        'tax_number',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'currency',
        'timezone',
        'logo_path',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function mainStore()
    {
        return $this->hasOne(Store::class)->where('is_main_store', true);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'business_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function isBakery()
    {
        return $this->type === 'bakery';
    }

    public function isCakeTools()
    {
        return $this->type === 'cake_tools';
    }

    public function isAcademy()
    {
        return $this->type === 'academy';
    }
} 