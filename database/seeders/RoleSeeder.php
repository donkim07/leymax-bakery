<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $roles = [
            'admin' => 'Full access to all features',
            'manager' => 'Manage business operations',
            'staff' => 'Handle day-to-day operations',
            'user' => 'Basic user access',
        ];

        foreach ($roles as $name => $description) {
            Role::create([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        // Create permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Business management
            'view business',
            'create business',
            'edit business',
            'delete business',
            
            // Store management
            'view stores',
            'create stores',
            'edit stores',
            'delete stores',
            
            // Product management
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Inventory management
            'view inventory',
            'manage inventory',
            
            // Order management
            'view orders',
            'create orders',
            'edit orders',
            'delete orders',
            
            // Report access
            'view reports',
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        // Assign permissions to roles
        $adminRole = Role::findByName('admin');
        $adminRole->givePermissionTo(Permission::all());

        $managerRole = Role::findByName('manager');
        $managerRole->givePermissionTo([
            'view users', 'create users', 'edit users',
            'view business', 'edit business',
            'view stores', 'create stores', 'edit stores',
            'view products', 'create products', 'edit products',
            'view inventory', 'manage inventory',
            'view orders', 'create orders', 'edit orders',
            'view reports',
        ]);

        $staffRole = Role::findByName('staff');
        $staffRole->givePermissionTo([
            'view products',
            'view inventory', 'manage inventory',
            'view orders', 'create orders',
        ]);

        $userRole = Role::findByName('user');
        $userRole->givePermissionTo([
            'view products',
            'view orders', 'create orders',
        ]);
    }
} 