<?php

namespace App\Http\Controllers;

use App\Services\ThemeService;
use Illuminate\Http\Request;

class ThemeController extends Controller
{
    protected $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    public function toggle(Request $request)
    {
        $theme = $this->themeService->toggleTheme();
        
        if ($request->wantsJson()) {
            return response()->json(['theme' => $theme]);
        }

        return back()->with('success', 'Theme updated successfully');
    }
} 