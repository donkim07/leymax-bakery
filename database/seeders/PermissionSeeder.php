<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing permissions
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Permission::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
        
        // Define global permissions
        $globalPermissions = [
            // User management
            ['name' => 'view_users', 'guard_name' => 'web', 'business_type' => null, 'description' => 'View system users', 'group' => 'user'],
            ['name' => 'create_users', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Create new users', 'group' => 'user'],
            ['name' => 'edit_users', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Edit existing users', 'group' => 'user'],
            ['name' => 'delete_users', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Delete users', 'group' => 'user'],
            
            // Role management
            ['name' => 'view_roles', 'guard_name' => 'web', 'business_type' => null, 'description' => 'View system roles', 'group' => 'role'],
            ['name' => 'create_roles', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Create new roles', 'group' => 'role'],
            ['name' => 'edit_roles', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Edit existing roles', 'group' => 'role'],
            ['name' => 'delete_roles', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Delete roles', 'group' => 'role'],
            
            // General settings
            ['name' => 'view_settings', 'guard_name' => 'web', 'business_type' => null, 'description' => 'View system settings', 'group' => 'setting'],
            ['name' => 'edit_settings', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Edit system settings', 'group' => 'setting'],
            
            // Reports
            ['name' => 'view_reports', 'guard_name' => 'web', 'business_type' => null, 'description' => 'View system reports', 'group' => 'report'],
            ['name' => 'export_reports', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Export reports', 'group' => 'report'],
            
            // Backup
            ['name' => 'create_backups', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Create system backups', 'group' => 'backup'],
            ['name' => 'restore_backups', 'guard_name' => 'web', 'business_type' => null, 'description' => 'Restore from backups', 'group' => 'backup'],
        ];
        
        // Define bakery permissions
        $bakeryPermissions = [
            // Products
            ['name' => 'view_bakery_products', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'View bakery products', 'group' => 'bakery_product'],
            ['name' => 'create_bakery_products', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Create bakery products', 'group' => 'bakery_product'],
            ['name' => 'edit_bakery_products', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Edit bakery products', 'group' => 'bakery_product'],
            ['name' => 'delete_bakery_products', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Delete bakery products', 'group' => 'bakery_product'],
            
            // Inventory
            ['name' => 'view_bakery_inventory', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'View bakery inventory', 'group' => 'bakery_inventory'],
            ['name' => 'adjust_bakery_inventory', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Adjust bakery inventory', 'group' => 'bakery_inventory'],
            ['name' => 'transfer_bakery_inventory', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Transfer bakery inventory', 'group' => 'bakery_inventory'],
            
            // Sales
            ['name' => 'view_bakery_sales', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'View bakery sales', 'group' => 'bakery_sales'],
            ['name' => 'create_bakery_sales', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Create bakery sales', 'group' => 'bakery_sales'],
            ['name' => 'edit_bakery_sales', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Edit bakery sales', 'group' => 'bakery_sales'],
            ['name' => 'cancel_bakery_sales', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Cancel bakery sales', 'group' => 'bakery_sales'],
            
            // Manufacturing
            ['name' => 'view_bakery_manufacturing', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'View bakery manufacturing', 'group' => 'bakery_manufacturing'],
            ['name' => 'create_bakery_manufacturing', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Create bakery manufacturing orders', 'group' => 'bakery_manufacturing'],
            ['name' => 'edit_bakery_manufacturing', 'guard_name' => 'web', 'business_type' => 'bakery', 'description' => 'Edit bakery manufacturing orders', 'group' => 'bakery_manufacturing'],
        ];
        
        // Define tools permissions
        $toolsPermissions = [
            // Products
            ['name' => 'view_tools_products', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'View tools products', 'group' => 'tools_product'],
            ['name' => 'create_tools_products', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Create tools products', 'group' => 'tools_product'],
            ['name' => 'edit_tools_products', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Edit tools products', 'group' => 'tools_product'],
            ['name' => 'delete_tools_products', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Delete tools products', 'group' => 'tools_product'],
            
            // Inventory
            ['name' => 'view_tools_inventory', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'View tools inventory', 'group' => 'tools_inventory'],
            ['name' => 'adjust_tools_inventory', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Adjust tools inventory', 'group' => 'tools_inventory'],
            ['name' => 'transfer_tools_inventory', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Transfer tools inventory', 'group' => 'tools_inventory'],
            
            // Sales
            ['name' => 'view_tools_sales', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'View tools sales', 'group' => 'tools_sales'],
            ['name' => 'create_tools_sales', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Create tools sales', 'group' => 'tools_sales'],
            ['name' => 'edit_tools_sales', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Edit tools sales', 'group' => 'tools_sales'],
            ['name' => 'cancel_tools_sales', 'guard_name' => 'web', 'business_type' => 'cake_tools', 'description' => 'Cancel tools sales', 'group' => 'tools_sales'],
        ];
        
        // Define academy permissions
        $academyPermissions = [
            // Courses
            ['name' => 'view_academy_courses', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'View academy courses', 'group' => 'academy_course'],
            ['name' => 'create_academy_courses', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Create academy courses', 'group' => 'academy_course'],
            ['name' => 'edit_academy_courses', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Edit academy courses', 'group' => 'academy_course'],
            ['name' => 'delete_academy_courses', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Delete academy courses', 'group' => 'academy_course'],
            
            // Students
            ['name' => 'view_academy_students', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'View academy students', 'group' => 'academy_student'],
            ['name' => 'create_academy_students', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Create academy students', 'group' => 'academy_student'],
            ['name' => 'edit_academy_students', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Edit academy students', 'group' => 'academy_student'],
            ['name' => 'delete_academy_students', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Delete academy students', 'group' => 'academy_student'],
            
            // Media
            ['name' => 'view_academy_media', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'View academy media', 'group' => 'academy_media'],
            ['name' => 'upload_academy_media', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Upload academy media', 'group' => 'academy_media'],
            ['name' => 'delete_academy_media', 'guard_name' => 'web', 'business_type' => 'academy', 'description' => 'Delete academy media', 'group' => 'academy_media'],
        ];
        
        // Insert all permissions
        $allPermissions = array_merge($globalPermissions, $bakeryPermissions, $toolsPermissions, $academyPermissions);
        Permission::insert($allPermissions);
        
        $this->command->info('Permissions created successfully!');
    }
} 