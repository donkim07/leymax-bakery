<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Services\ThemeService;

class ThemeSwitcher extends Component
{
    public $theme;

    public function mount(ThemeService $themeService)
    {
        $this->theme = $themeService->getCurrentTheme();
    }

    public function toggleTheme(ThemeService $themeService)
    {
        $this->theme = $themeService->toggleTheme();
        $this->emit('themeChanged', $this->theme);
    }

    public function render()
    {
        return view('livewire.theme-switcher');
    }
} 