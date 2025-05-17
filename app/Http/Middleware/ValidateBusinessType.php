<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Business;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class ValidateBusinessType
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Get business type from the route prefix
        $type = $this->getBusinessTypeFromPrefix($request->segment(1));
        
        Log::info('Business type validation', [
            'user_id' => $user->id,
            'route_prefix' => $request->segment(1),
            'business_type' => $type
        ]);
        
        if (!$type) {
            Log::warning('Invalid business type', [
                'user_id' => $user->id,
                'route_prefix' => $request->segment(1)
            ]);
            
            // Initialize empty variables for the view
            View::share([
                'stats' => [],
                'salesTrend' => ['labels' => [], 'data' => []],
                'topProducts' => [],
                'recentOrders' => [],
                'lowStockItems' => [],
                'categories' => [],
                'topItems' => [],
                'inventoryAlerts' => [],
                'enrollmentTrend' => ['labels' => [], 'data' => []],
                'coursePopularity' => [],
                'recentEnrollments' => [],
                'courseProgress' => []
            ]);
            
            return redirect()->route('dashboard')
                ->with('error', 'Invalid business type.');
        }

        // Get the current business for the user
        $business = Business::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->where('type', $type)->first();

        if (!$business) {
            Log::warning('User does not have access to business type', [
                'user_id' => $user->id,
                'business_type' => $type
            ]);
            
            // Initialize empty variables for the view
            View::share([
                'stats' => [],
                'salesTrend' => ['labels' => [], 'data' => []],
                'topProducts' => [],
                'recentOrders' => [],
                'lowStockItems' => [],
                'categories' => [],
                'topItems' => [],
                'inventoryAlerts' => [],
                'enrollmentTrend' => ['labels' => [], 'data' => []],
                'coursePopularity' => [],
                'recentEnrollments' => [],
                'courseProgress' => []
            ]);
            
            return redirect()->route('dashboard')
                ->with('error', 'You do not have access to this business type.');
        }

        // Store the current business in the request for easy access
        $request->attributes->set('current_business', $business);
        
        Log::info('Business type validated successfully', [
            'user_id' => $user->id,
            'business_id' => $business->id,
            'business_type' => $type
        ]);

        return $next($request);
    }

    /**
     * Get the business type from the route prefix
     */
    private function getBusinessTypeFromPrefix(?string $prefix): ?string
    {
        return match ($prefix) {
            'bakery' => 'bakery',
            'tools' => 'cake_tools',
            'academy' => 'academy',
            default => null,
        };
    }
} 