<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    public function handle(Request $request, Closure $next)
    {
        $language = auth()->check() ? auth()->user()->language : 'en';
        App::setLocale($language);
        
        return $next($request);
    }
} 