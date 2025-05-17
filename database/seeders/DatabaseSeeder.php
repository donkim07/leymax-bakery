<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call the role seeder first
        $this->call(RoleSeeder::class);
        
        // Call the permission seeder
        $this->call(PermissionSeeder::class);

        // Create a test user
        $user = User::create([
            'name' => 'Test User',
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create businesses
        $bakery = Business::create([
            'name' => 'Bakery Shop',
            'type' => 'bakery',
            'email' => 'bakery@example.com',
            'is_active' => true,
        ]);

        $tools = Business::create([
            'name' => 'Cake Tools',
            'type' => 'cake_tools',
            'email' => 'tools@example.com',
            'is_active' => true,
        ]);

        $academy = Business::create([
            'name' => 'Academy',
            'type' => 'academy',
            'email' => 'academy@example.com',
            'is_active' => true,
        ]);

        // Attach user to businesses (using sync to avoid duplicates)
        $user->businesses()->sync([$bakery->id, $tools->id, $academy->id]);

        // Create categories for Cake Tools
        $toolsCategories = [
            'Baking Pans' => 'Various types of baking pans and molds',
            'Decorating Tools' => 'Tools for cake decoration and finishing',
            'Measuring Tools' => 'Precision measuring equipment',
            'Mixing Equipment' => 'Mixers and mixing accessories',
            'Storage Solutions' => 'Storage containers and organization tools',
        ];

        foreach ($toolsCategories as $name => $description) {
            Category::create([
                'business_id' => $tools->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $tools->id,
                'description' => $description,
                'is_active' => true,
            ]);
        }

        // Create categories for Bakery
        $bakeryCategories = [
            'Cakes' => 'Fresh and custom cakes',
            'Pastries' => 'Assorted pastries and baked goods',
            'Breads' => 'Fresh bread varieties',
            'Cookies' => 'Homemade cookies and biscuits',
            'Special Orders' => 'Custom and special order items',
        ];

        foreach ($bakeryCategories as $name => $description) {
            Category::create([
                'business_id' => $bakery->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $bakery->id,
                'description' => $description,
                'is_active' => true,
            ]);
        }

        // Create categories for Academy
        $academyCategories = [
            'Beginner Courses' => 'Fundamental baking courses',
            'Advanced Techniques' => 'Advanced baking and decoration',
            'Professional Training' => 'Professional certification courses',
            'Specialty Classes' => 'Specialized baking workshops',
            'Online Courses' => 'Virtual learning programs',
        ];

        foreach ($academyCategories as $name => $description) {
            Category::create([
                'business_id' => $academy->id,
                'name' => $name,
                'slug' => Str::slug($name) . '-' . $academy->id,
                'description' => $description,
                'is_active' => true,
            ]);
        }
    }
}
