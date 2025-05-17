<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'notes'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function businesses()
    {
        return $this->belongsToMany(Business::class, 'business_customer')
            ->withTimestamps();
    }

    public static function rules($id = null)
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:customers,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereHas('orders', function($query) {
            $query->where('created_at', '>=', now()->subMonths(6));
        });
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($query) use ($term) {
            $query->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        return implode(', ', $parts);
    }
} 