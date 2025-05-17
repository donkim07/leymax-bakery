<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function toggle(Request $request)
    {
        $user = auth()->user();
        $newLanguage = $user->language === 'en' ? 'sw' : 'en';
        
        // Update user's language preference
        $user->update(['language' => $newLanguage]);

        // Set the application locale
        App::setLocale($newLanguage);
        Session::put('locale', $newLanguage);
        
        if ($request->wantsJson()) {
            return response()->json(['language' => $newLanguage]);
        }

        return back()->with('success', __('messages.language_updated'));
    }
}