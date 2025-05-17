<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateBusinessAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure API version header is present
        if (!$request->hasHeader('X-API-Version')) {
            return response()->json([
                'message' => 'API version header is required',
                'error' => 'Missing X-API-Version header'
            ], 400);
        }

        // Validate business access if business ID is present
        if ($business = $request->route('business')) {
            // Check if user has active subscription or license
            if (!$business->hasValidSubscription() && !$business->hasValidLicense()) {
                return response()->json([
                    'message' => 'Invalid or expired subscription',
                    'error' => 'Subscription required'
                ], 403);
            }

            // Check if business is active
            if (!$business->is_active) {
                return response()->json([
                    'message' => 'Business is currently inactive',
                    'error' => 'Inactive business'
                ], 403);
            }

            // Add business to request for easy access in controllers
            $request->attributes->set('current_business', $business);
        }

        return $next($request);
    }
} 