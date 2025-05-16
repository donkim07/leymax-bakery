<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function toggle(Request $request)
    {
        $user = auth()->user();
        $user->language = $user->language === 'en' ? 'sw' : 'en';
        $user->save();

        return back()->with('success', 'Language updated successfully');
    }
}