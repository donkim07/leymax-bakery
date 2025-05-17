<?php

namespace App\Services;

use Illuminate\Support\Facades\Cookie;

class ThemeService
{
    public function getCurrentTheme()
    {
        return auth()->check() 
            ? auth()->user()->theme 
            : Cookie::get('theme', 'light');
    }

    public function setTheme($theme)
    {
        if (auth()->check()) {
            auth()->user()->update(['theme' => $theme]);
        }

        Cookie::queue('theme', $theme, 60 * 24 * 365); // 1 year
        return $theme;
    }

    public function toggleTheme()
    {
        $currentTheme = $this->getCurrentTheme();
        $newTheme = $currentTheme === 'dark' ? 'light' : 'dark';
        
        return $this->setTheme($newTheme);
    }
} 