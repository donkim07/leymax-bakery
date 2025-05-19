<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'registration_number',
        'tax_number',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'logo_path',
        'payment_status',
        'license_expiry',
        'is_active',
        'owner_id'
    ];

    protected $casts = [
        'license_expiry' => 'date',
        'is_active' => 'boolean'
    ];

    // Relationships
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function businesses()
    {
        return $this->hasMany(Business::class);
    }

    // Check if license is valid
    public function hasValidLicense()
    {
        return $this->payment_status === 'paid' && $this->license_expiry->isFuture();
    }
} 