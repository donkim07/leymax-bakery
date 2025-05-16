<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Product;
use App\Models\Store;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $businesses = Business::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        $data = [
            'total_businesses' => $businesses->count(),
            'total_stores' => Store::whereIn('business_id', $businesses->pluck('id'))->count(),
            'total_products' => Product::whereIn('business_id', $businesses->pluck('id'))->count(),
            'recent_businesses' => $businesses->take(5),
            'low_stock_alerts' => Product::whereIn('business_id', $businesses->pluck('id'))
                ->lowStock()
                ->with('business')
                ->take(5)
                ->get(),
        ];

        return view('dashboard', compact('data'));
    }

    public function analytics()
    {
        $user = auth()->user();
        $businesses = Business::whereHas('users', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        $data = [
            'business_stats' => $businesses->map(function ($business) {
                return [
                    'name' => $business->name,
                    'total_sales' => $business->sales()->count(),
                    'total_revenue' => $business->sales()->sum('total_amount'),
                    'total_products' => $business->products()->count(),
                    'low_stock_items' => $business->products()->lowStock()->count(),
                ];
            }),
            'chart_data' => [
                'daily_sales' => $this->getDailySalesData($businesses),
                'product_categories' => $this->getProductCategoriesData($businesses),
                'store_performance' => $this->getStorePerformanceData($businesses),
            ],
        ];

        return view('dashboard.analytics', compact('data'));
    }

    private function getDailySalesData($businesses)
    {
        // Implementation for daily sales chart data
        return [];
    }

    private function getProductCategoriesData($businesses)
    {
        // Implementation for product categories chart data
        return [];
    }

    private function getStorePerformanceData($businesses)
    {
        // Implementation for store performance chart data
        return [];
    }
} 