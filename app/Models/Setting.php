<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];

    /**
     * Get specific setting value
     *
     * @param string $key
     * @param string $group
     * @param mixed $default
     * @return mixed
     */
    public static function getValue(string $key, string $group = 'general', $default = null)
    {
        $setting = static::where('key', $key)
            ->where('group', $group)
            ->first();
        
        return $setting ? $setting->value : $default;
    }

    /**
     * Get all settings for a specific group
     *
     * @param string $group
     * @return \Illuminate\Support\Collection
     */
    public static function getGroupSettings(string $group = 'general')
    {
        return static::where('group', $group)
            ->get()
            ->pluck('value', 'key');
    }
} 