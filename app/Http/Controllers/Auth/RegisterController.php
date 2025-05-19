<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Business;
use App\Models\Company;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'username' => ['required', 'string', 'max:255', 'unique:users', 'alpha_dash'],
            'company_name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'businesses' => ['required', 'array', 'min:1', 'in:bakery,cake_tools,academy'],
            'terms' => ['required', 'accepted'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        DB::beginTransaction();
        
        try {
        // Create the user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'enabled_businesses' => $data['businesses'],
            'current_business' => $data['businesses'][0] ?? null,
        ]);

        // Get or create the user role
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        
        // Assign role to user
        $user->assignRole($userRole);
            
            // Create company
            $company = Company::create([
                'name' => $data['company_name'] ?? ($data['name'] . "'s Company"),
                'email' => $data['email'],
                'owner_id' => $user->id,
                'payment_status' => 'paid',
                'license_expiry' => now()->addYears(50),
                'is_active' => true,
            ]);

        // Create or attach businesses
        foreach ($data['businesses'] as $businessType) {
            // Generate a unique business email
            $businessEmail = $this->generateUniqueBusinessEmail($data['email'], $businessType);

            // Create the business
            $business = Business::create([
                'name' => $data['name'] . "'s " . ucfirst($businessType),
                'type' => $businessType,
                'email' => $businessEmail,
                    'company_id' => $company->id,
            ]);

            // Create a main store for the business
            $business->stores()->create([
                'name' => 'Main Store',
                'code' => Str::upper(Str::random(6)),
                'is_main_store' => true,
                'is_active' => true,
            ]);

            // Attach the business to the user with the 'owner' role
            $user->businesses()->attach($business->id, ['role' => 'owner']);
        }

            DB::commit();
        return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    protected function generateUniqueBusinessEmail($userEmail, $businessType)
    {
        $emailParts = explode('@', $userEmail);
        $baseEmail = $emailParts[0] . '.' . $businessType . '@' . $emailParts[1];
        
        if (!Business::where('email', $baseEmail)->exists()) {
            return $baseEmail;
        }

        $counter = 1;
        do {
            $newEmail = $emailParts[0] . '.' . $businessType . $counter . '@' . $emailParts[1];
            $counter++;
        } while (Business::where('email', $newEmail)->exists());

        return $newEmail;
    }
}
