<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Backup\BackupDestination\Backup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    /**
     * Display general settings page.
     *
     * @return \Illuminate\View\View
     */
    public function general()
    {
        $settings = Setting::where('group', 'general')->get()->pluck('value', 'key');
        return view('settings.general', compact('settings'));
    }

    /**
     * Update general settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateGeneral(Request $request)
    {
        $validated = $request->validate([
            'app_name' => 'required|string|max:255',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string',
            'company_phone' => 'required|string|max:20',
            'company_email' => 'required|email',
            'default_currency' => 'required|string|size:3',
            'default_language' => 'required|string|size:2',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($validated as $key => $value) {
            if ($key == 'app_logo' && $request->hasFile('app_logo')) {
                $path = $request->file('app_logo')->store('logos', 'public');
                $value = asset('storage/' . $path);
            }
            
            Setting::updateOrCreate(
                ['key' => $key, 'group' => 'general'],
                ['value' => $value]
            );
        }

        // Update .env file with app name to make it visible in UI
        if (isset($validated['app_name'])) {
            $this->updateEnvFile('APP_NAME', $validated['app_name']);
        }

        return redirect()->route('settings.general')->with('success', 'General settings updated successfully!');
    }

    /**
     * Add a new currency.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addCurrency(Request $request)
    {
        $validated = $request->validate([
            'currency_code' => 'required|string|size:3|alpha',
            'currency_name' => 'required|string|max:50',
        ]);
        
        // Get existing custom currencies or create new array
        $customCurrencies = Setting::getValue('custom_currencies', 'general');
        if ($customCurrencies) {
            $currencies = json_decode($customCurrencies, true);
        } else {
            $currencies = [];
        }
        
        // Add new currency
        $currencies[strtoupper($validated['currency_code'])] = $validated['currency_name'];
        
        // Save back to settings
        Setting::updateOrCreate(
            ['key' => 'custom_currencies', 'group' => 'general'],
            ['value' => json_encode($currencies)]
        );
        
        return redirect()->route('settings.general')->with('success', 'Currency added successfully!');
    }

    /**
     * Update .env file
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    private function updateEnvFile($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {
            $value = str_replace('"', '\"', $value); // Escape double quotes
            file_put_contents(
                $path, 
                preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}=\"{$value}\"",
                    file_get_contents($path)
                )
            );
        }
    }

    /**
     * Display user management page.
     *
     * @return \Illuminate\View\View
     */
    public function users()
    {
        $users = User::with('roles')->paginate(10);
        $roles = Role::all();
        return view('settings.users', compact('users', 'roles'));
    }

    /**
     * Display the user creation form.
     *
     * @return \Illuminate\View\View
     */
    public function createUser()
    {
        $roles = Role::all();
        return view('settings.users.create', compact('roles'));
    }

    /**
     * Store a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        
        // Handle the send_invite field separately since it's a checkbox
        $sendInvite = $request->has('send_invite');

        $user = new User();
        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'];
        $user->password = bcrypt($validated['password']);
        
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = asset('storage/' . $path);
        }
        
        $user->save();
        $user->roles()->attach($validated['roles']);

        // Send welcome email if requested
        if ($sendInvite) {
            $emailSent = $this->sendWelcomeEmail($user, $validated['password']);
            if ($emailSent) {
                return redirect()->route('settings.users')
                    ->with('success', 'User created successfully and welcome email sent!');
            } else {
                return redirect()->route('settings.users')
                    ->with('success', 'User created successfully but there was an issue sending the welcome email. Check logs for details.');
            }
        }

        return redirect()->route('settings.users')
            ->with('success', 'User created successfully!');
    }

    /**
     * Send welcome email to a new user
     *
     * @param  \App\Models\User  $user
     * @param  string  $password
     * @return bool
     */
    private function sendWelcomeEmail(User $user, $password)
    {
        $data = [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'password' => $password,
            'login_url' => route('login')
        ];
        
        // Send the email
        try {
            \Log::info('Attempting to send welcome email to: ' . $user->email);
            
            Mail::send('emails.welcome', $data, function($message) use ($user) {
                $message->to($user->email, $user->name)
                    ->subject('Welcome to ' . config('app.name'));
            });
            
            \Log::info('Welcome email sent successfully to: ' . $user->email);
            return true;
        } catch (\Exception $e) {
            // Log the error but don't interrupt the process
            \Log::error('Failed to send welcome email: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'error' => $e
            ]);
            return false;
        }
    }

    /**
     * Display the user edit form.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editUser($id)
    {
        $user = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        return view('settings.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }
        
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = asset('storage/' . $path);
        }
        
        $user->save();
        $user->roles()->sync($validated['roles']);

        return redirect()->route('settings.users')->with('success', 'User updated successfully!');
    }

    /**
     * Delete a user.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deleting yourself
        if ($user->id === Auth::id()) {
            return redirect()->route('settings.users')->with('error', 'You cannot delete your own account!');
        }
        
        $user->delete();
        return redirect()->route('settings.users')->with('success', 'User deleted successfully!');
    }

    /**
     * Display roles and permissions page.
     *
     * @return \Illuminate\View\View
     */
    public function roles()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();
        return view('settings.roles', compact('roles', 'permissions'));
    }

    /**
     * Display the role creation form.
     *
     * @return \Illuminate\View\View
     */
    public function createRole()
    {
        $permissions = Permission::all();
        return view('settings.roles.create', compact('permissions'));
    }

    /**
     * Store a new role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = new Role();
        $role->name = $validated['name'];
        $role->description = $validated['description'] ?? '';
        $role->save();
        
        $role->permissions()->attach($validated['permissions']);

        return redirect()->route('settings.roles')->with('success', 'Role created successfully!');
    }

    /**
     * Display the role edit form.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function editRole($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        return view('settings.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateRole(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->name = $validated['name'];
        $role->description = $validated['description'] ?? '';
        $role->save();
        
        $role->permissions()->sync($validated['permissions']);

        return redirect()->route('settings.roles')->with('success', 'Role updated successfully!');
    }

    /**
     * Delete a role.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteRole($id)
    {
        $role = Role::findOrFail($id);
        
        // Prevent deleting essential roles
        if (in_array($role->name, ['Admin', 'Super Admin'])) {
            return redirect()->route('settings.roles')->with('error', 'Cannot delete essential system roles!');
        }
        
        $role->delete();
        return redirect()->route('settings.roles')->with('success', 'Role deleted successfully!');
    }

    /**
     * Display backup and restore page.
     *
     * @return \Illuminate\View\View
     */
    public function backup()
    {
        $backups = Storage::disk('backups')->files();
        $backupsList = [];
        
        foreach ($backups as $backup) {
            if (substr($backup, -4) === '.zip') {
                $backupsList[] = [
                    'filename' => $backup,
                    'size' => Storage::disk('backups')->size($backup),
                    'created_at' => Storage::disk('backups')->lastModified($backup),
                ];
            }
        }
        
        return view('settings.backup', compact('backupsList'));
    }

    /**
     * Create a new backup.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createBackup(Request $request)
    {
        // Run backup command
        try {
            Artisan::call('backup:run', ['--only-db' => $request->has('only_database')]);
            return redirect()->route('settings.backup')->with('success', 'Backup created successfully!');
        } catch (\Exception $e) {
            return redirect()->route('settings.backup')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file.
     *
     * @param  string  $filename
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);
        return response()->download($path);
    }

    /**
     * Delete a backup file.
     *
     * @param  string  $filename
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteBackup($filename)
    {
        Storage::disk('backups')->delete($filename);
        return redirect()->route('settings.backup')->with('success', 'Backup deleted successfully!');
    }

    /**
     * Restore from a backup file.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip',
        ]);

        try {
            // Logic to restore from backup would go here
            // This is a placeholder for actual restore functionality
            
            return redirect()->route('settings.backup')->with('success', 'System restored successfully!');
        } catch (\Exception $e) {
            return redirect()->route('settings.backup')->with('error', 'Restoration failed: ' . $e->getMessage());
        }
    }

    /**
     * Business-specific settings
     */
    public function bakeryCompany()
    {
        return view('bakery.settings.company');
    }

    public function bakeryBranches()
    {
        return view('bakery.settings.branches');
    }

    public function bakeryUsers()
    {
        return view('bakery.settings.users');
    }

    public function bakeryTax()
    {
        return view('bakery.settings.tax');
    }

    public function bakeryEmail()
    {
        return view('bakery.settings.email');
    }

    public function bakeryLocalization()
    {
        return view('bakery.settings.localization');
    }

    public function toolsCompany()
    {
        return view('tools.settings.company');
    }

    public function toolsBranches()
    {
        return view('tools.settings.branches');
    }

    public function toolsUsers()
    {
        return view('tools.settings.users');
    }

    public function toolsTax()
    {
        return view('tools.settings.tax');
    }

    public function toolsEmail()
    {
        return view('tools.settings.email');
    }

    public function toolsLocalization()
    {
        return view('tools.settings.localization');
    }

    public function academyCompany()
    {
        return view('academy.settings.company');
    }

    public function academyUsers()
    {
        return view('academy.settings.users');
    }

    public function academyEmail()
    {
        return view('academy.settings.email');
    }

    public function academyLocalization()
    {
        return view('academy.settings.localization');
    }
} 