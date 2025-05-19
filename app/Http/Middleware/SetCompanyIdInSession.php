<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Company;
use App\Models\Business;
use Symfony\Component\HttpFoundation\Response;

class SetCompanyIdInSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $companyId = null;
            $companyName = null;
            
            // If user is a company owner, set company_id in session
            $ownedCompany = Company::where('owner_id', $user->id)->first();
            if ($ownedCompany) {
                $companyId = $ownedCompany->id;
                $companyName = $ownedCompany->name;
                Log::debug('User is company owner', [
                    'user_id' => $user->id,
                    'company_id' => $companyId
                ]);
            } 
            // If user has a company_id field (employee or member of a company)
            elseif ($user->company_id) {
                $companyId = $user->company_id;
                $company = Company::find($user->company_id);
                if ($company) {
                    $companyName = $company->name;
                }
                Log::debug('User is company employee', [
                    'user_id' => $user->id,
                    'company_id' => $companyId
                ]);
            }
            
            if ($companyId) {
                session(['company_id' => $companyId]);
                session(['company_name' => $companyName]);
                
                // Always try to set business_id (whether or not it's already in session)
                $business = Business::where('company_id', $companyId)
                    ->where('is_active', true)
                    ->first();
                
                if (!$business) {
                    // Fallback: get ANY business for this company
                    $business = Business::where('company_id', $companyId)->first();
                    if ($business) {
                        Log::warning('No active business found, using first business for company', [
                            'user_id' => $user->id,
                            'company_id' => $companyId,
                            'business_id' => $business->id,
                            'business_name' => $business->name
                        ]);
                    }
                }
                
                if (!$business) {
                    // If no business found by company_id, try using the user-business relationship
                    if ($user->businesses()->count() > 0) {
                        $business = $user->businesses()->first();
                        Log::warning('No business found by company_id, using first user-business', [
                            'user_id' => $user->id,
                            'business_id' => $business->id,
                            'business_name' => $business->name
                        ]);
                    }
                }
                
                if ($business) {
                    session(['business_id' => $business->id]);
                    session(['business_name' => $business->name]);
                    session(['business_type' => $business->type]);
                    
                    Log::debug('Set business ID in session', [
                        'user_id' => $user->id,
                        'company_id' => $companyId,
                        'business_id' => $business->id,
                        'business_name' => $business->name
                    ]);
                } else {
                    Log::warning('No business found for user/company', [
                        'user_id' => $user->id,
                        'company_id' => $companyId
                    ]);
                    
                    // If no business found, create a debug message in the session
                    if ($request->ajax()) {
                        session(['business_error' => 'No business found for this user/company.']);
                    }
                }
            }
        }
        
        return $next($request);
    }
}
