<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureBusinessIdExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!session('business_id')) {
            // Try to get business_id from user's company
            $user = Auth::user();
            
            if ($user && $user->company && $user->company->business_id) {
                session(['business_id' => $user->company->business_id]);
                Log::info('Business ID set in session from user company', [
                    'user_id' => $user->id, 
                    'business_id' => $user->company->business_id
                ]);
            } else {
                // Try to get from database directly
                $businessId = $this->findUserBusinessId($user);
                
                if ($businessId) {
                    session(['business_id' => $businessId]);
                    Log::info('Business ID set in session from database query', [
                        'user_id' => $user->id, 
                        'business_id' => $businessId
                    ]);
                } else {
                    Log::warning('Could not determine business ID for user', [
                        'user_id' => $user ? $user->id : null
                    ]);
                    
                    // If this is an AJAX request, return an error
                    if ($request->ajax() || $request->wantsJson()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No business ID found. Please select a business.'
                        ], 400);
                    }
                    
                    // For non-AJAX requests, just log and continue
                }
            }
        }
        
        return $next($request);
    }
    
    /**
     * Find a user's business ID by querying related tables
     */
    private function findUserBusinessId($user)
    {
        if (!$user) {
            return null;
        }
        
        try {
            // Try different ways to find the business_id depending on your data model
            
            // Method 1: Check if user has businesses relation
            if (method_exists($user, 'businesses') && $user->businesses()->count() > 0) {
                return $user->businesses()->first()->id;
            }
            
            // Method 2: Check if there's a business record with the user as owner
            $business = \App\Models\Business::where('owner_id', $user->id)
                ->orWhere('created_by', $user->id)
                ->first();
                
            if ($business) {
                return $business->id;
            }
            
            // Method 3: Check if user has any role in any business
            $businessUser = \DB::table('business_user')
                ->where('user_id', $user->id)
                ->first();
                
            if ($businessUser) {
                return $businessUser->business_id;
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error finding business ID: ' . $e->getMessage());
            return null;
        }
    }
} 