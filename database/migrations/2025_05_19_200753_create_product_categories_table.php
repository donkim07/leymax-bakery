<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Instead of creating a view, we'll insert some default bakery categories
        $businessIds = DB::table('businesses')->pluck('id');
        
        foreach ($businessIds as $businessId) {
            // Check if business already has bakery categories
            $hasCategories = DB::table('categories')
                ->where('business_id', $businessId)
                ->where('slug', 'like', 'bakery-%')
                ->exists();
                
            if (!$hasCategories) {
                // Add some default bakery categories
                $defaultCategories = [
                    'Bread',
                    'Cakes',
                    'Pastries',
                    'Cookies',
                    'Pies'
                ];
                
                foreach ($defaultCategories as $category) {
                    // Create a unique slug by combining business ID and category name
                    $slug = 'bakery-' . $businessId . '-' . Str::slug($category);
                    
                    // Check if slug already exists (just to be safe)
                    $exists = DB::table('categories')->where('slug', $slug)->exists();
                    if (!$exists) {
                        DB::table('categories')->insert([
                            'business_id' => $businessId,
                            'name' => $category,
                            'slug' => $slug,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete all bakery categories
        DB::table('categories')
            ->where('slug', 'like', 'bakery-%')
            ->delete();
    }
};
