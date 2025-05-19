<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AssemblyCategory;
use App\Models\AssemblyGroup;
use App\Models\AssemblySize;
use App\Models\Product;
use App\Models\AssembledItem;
use App\Models\Ingredient;

class BusinessDataController extends Controller
{
    /**
     * Get all business data for the current user's business
     * This helps prefetch dropdown data for faster UI
     */
    public function index(Request $request)
    {
        $businessId = session('business_id');
        
        if (!$businessId) {
            return response()->json([
                'success' => false,
                'message' => 'No active business found'
            ], 400);
        }
        
        // Get assembly categories
        $assemblyCategories = AssemblyCategory::where('business_id', $businessId)
            ->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        // Get assembly groups
        $assemblyGroups = AssemblyGroup::where('business_id', $businessId)
            ->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        // Get assembly sizes
        $assemblySizes = AssemblySize::where('business_id', $businessId)
            ->where('is_active', true)
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
            
        // Get products
        $products = Product::where('business_id', $businessId)
            ->where('is_active', true)
            ->select('id', 'name', 'cost_price', 'unit')
            ->orderBy('name')
            ->get();
            
        // Get assembled items
        $assembledItems = AssembledItem::where('business_id', $businessId)
            ->select('id', 'name', 'type', 'total_cost', 'unit')
            ->orderBy('name')
            ->get();
            
        // Get ingredients
        $ingredients = Ingredient::where('business_id', $businessId)
            ->where('status', 'active')
            ->select('id', 'name', 'cost_price', 'unit')
            ->orderBy('name')
            ->get();
            
        return response()->json([
            'success' => true,
            'business_id' => $businessId,
            'user_id' => Auth::id(),
            'data' => [
                'assemblyCategories' => $assemblyCategories,
                'assemblyGroups' => $assemblyGroups,
                'assemblySizes' => $assemblySizes,
                'products' => $products,
                'assembledItems' => $assembledItems,
                'ingredients' => $ingredients
            ]
        ]);
    }
} 