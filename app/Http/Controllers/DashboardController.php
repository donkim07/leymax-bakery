<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\Product;
use App\Models\Store;
use App\Models\Order;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Category;
use App\Models\Student;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\StudentProgress;
use App\Models\StudentCourse;

class DashboardController extends Controller
{
    private $cacheTime = 3600; // 1 hour

    public function index()
    {
        try {
            $user = auth()->user();
            $businesses = Business::whereHas('users', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })->get();

            if ($businesses->isEmpty()) {
                Log::warning('User {user} has no associated businesses', ['user' => $user->id]);
                return view('dashboard')->with('warning', 'No businesses found for your account.');
            }

            // Get date ranges
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $monthStart = Carbon::now()->startOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $yearStart = Carbon::now()->startOfYear();

            // Wrap all database operations in a try-catch block
            try {
                DB::beginTransaction();

                // Calculate total sales and growth using cache
                $todaySales = Cache::remember('sales.today', $this->cacheTime, function() use ($today) {
                    return Order::whereDate('created_at', $today)->count();
                });

                $yesterdaySales = Cache::remember('sales.yesterday', $this->cacheTime, function() use ($yesterday) {
                    return Order::whereDate('created_at', $yesterday)->count();
                });

                $salesGrowth = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

                // Calculate total revenue and growth using cache
                $monthRevenue = Cache::remember('revenue.month', $this->cacheTime, function() use ($monthStart) {
                    return Order::whereBetween('created_at', [$monthStart, now()])->sum('total_amount');
                });

                $lastMonthRevenue = Cache::remember('revenue.lastMonth', $this->cacheTime, function() use ($lastMonthStart, $monthStart) {
                    return Order::whereBetween('created_at', [$lastMonthStart, $monthStart])->sum('total_amount');
                });

                $revenueGrowth = $lastMonthRevenue > 0 ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

                // Calculate total customers and growth using cache
                $totalCustomers = Cache::remember('customers.total', $this->cacheTime, function() {
                    return Customer::count();
                });

                $newCustomers = Cache::remember('customers.new', $this->cacheTime, function() use ($monthStart) {
                    return Customer::whereBetween('created_at', [$monthStart, now()])->count();
                });

                $lastMonthCustomers = Cache::remember('customers.lastMonth', $this->cacheTime, function() use ($lastMonthStart, $monthStart) {
                    return Customer::whereBetween('created_at', [$lastMonthStart, $monthStart])->count();
                });

                $customerGrowth = $lastMonthCustomers > 0 ? (($newCustomers - $lastMonthCustomers) / $lastMonthCustomers) * 100 : 0;

                // Get business units performance data with cache
                $businessUnitsData = Cache::remember('business.performance', $this->cacheTime, function() {
                    return $this->getBusinessUnitsPerformance();
                });

                // Get top selling products with cache
                $topProducts = Cache::remember('products.top', $this->cacheTime, function() use ($monthStart) {
                    return Product::select(
                        'products.name',
                        'businesses.name as business_unit',
                        'products.selling_price as price',
                        DB::raw('COUNT(order_items.id) as quantity_sold'),
                        DB::raw('SUM(order_items.quantity * products.selling_price) as revenue')
                    )
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->join('businesses', 'products.business_id', '=', 'businesses.id')
                    ->whereBetween('orders.created_at', [$monthStart, now()])
                    ->groupBy('products.id', 'products.name', 'businesses.name', 'products.selling_price')
                    ->orderByDesc('quantity_sold')
                    ->limit(5)
                    ->get();
                });

                // Get revenue distribution with cache
                $revenueDistribution = Cache::remember('revenue.distribution', $this->cacheTime, function() {
                    return $this->getRevenueDistribution();
                });

                // Get low stock alerts with cache
                $lowStockAlerts = Cache::remember('stock.alerts', 300, function() { // Cache for 5 minutes only
                    return Product::select(
                        'products.name as product_name',
                        'businesses.name as business_unit',
                        'products.stock_quantity as current_stock'
                    )
                    ->join('businesses', 'products.business_id', '=', 'businesses.id')
                    ->where('stock_quantity', '<=', DB::raw('reorder_point'))
                    ->orderBy('stock_quantity')
                    ->limit(5)
                    ->get();
                });

                // Get business growth data with cache
                $businessGrowth = Cache::remember('business.growth', $this->cacheTime, function() {
                    return $this->getBusinessGrowth();
                });

                DB::commit();

                // Calculate total sales for the month
                $totalSales = Cache::remember('sales.total.month', $this->cacheTime, function() use ($monthStart) {
                    return Order::whereBetween('created_at', [$monthStart, now()])->count();
                });
                $totalRevenue = $monthRevenue;

                // Set default values for all variables to avoid undefined variables
                $businessUnitsData = $businessUnitsData ?? [
                    'labels' => [],
                    'bakery' => [],
                    'tools' => [],
                    'academy' => []
                ];
                
                $topProducts = $topProducts ?? collect([]);
                
                $revenueDistribution = $revenueDistribution ?? [
                    'labels' => ['Bakery Shop', 'Cake Tools', 'Academy'],
                    'values' => [0, 0, 0]
                ];
                
                $lowStockAlerts = $lowStockAlerts ?? collect([]);
                
                $businessGrowth = $businessGrowth ?? [
                    'labels' => ['Bakery Shop', 'Cake Tools', 'Academy'],
                    'values' => [0, 0, 0]
                ];

                return view('dashboard', compact(
                    'todaySales',
                    'totalSales',
                    'salesGrowth',
                    'monthRevenue',
                    'totalRevenue',
                    'revenueGrowth',
                    'totalCustomers',
                    'newCustomers',
                    'customerGrowth',
                    'businessUnitsData',
                    'topProducts',
                    'revenueDistribution',
                    'lowStockAlerts',
                    'businessGrowth'
                ));

            } catch (Exception $e) {
                DB::rollBack();
                Log::error('Error in dashboard data retrieval: ' . $e->getMessage(), [
                    'exception' => $e,
                    'user_id' => $user->id
                ]);
                return view('dashboard')->with('error', 'An error occurred while loading the dashboard data. Please try again later.');
            }

        } catch (Exception $e) {
            Log::error('Error in dashboard initialization: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return view('dashboard')->with('error', 'An error occurred while loading the dashboard. Please try again later.');
        }
    }

    private function getBusinessUnitsPerformance()
    {
        try {
            $monthStart = Carbon::now()->startOfMonth();
            $labels = [];
            $bakeryData = [];
            $toolsData = [];
            $academyData = [];

            // Get daily data for the current month
            for ($date = $monthStart->copy(); $date <= now(); $date->addDay()) {
                $labels[] = $date->format('d M');

                // Bakery Shop data
                $bakeryData[] = Order::whereHas('business', function($query) {
                    $query->where('type', 'bakery');
                })->whereDate('created_at', $date)->sum('total_amount');

                // Cake Tools data
                $toolsData[] = Order::whereHas('business', function($query) {
                    $query->where('type', 'cake_tools');
                })->whereDate('created_at', $date)->sum('total_amount');

                // Academy data
                $academyData[] = Order::whereHas('business', function($query) {
                    $query->where('type', 'academy');
                })->whereDate('created_at', $date)->sum('total_amount');
            }

            return [
                'labels' => $labels,
                'bakery' => $bakeryData,
                'tools' => $toolsData,
                'academy' => $academyData
            ];

        } catch (Exception $e) {
            Log::error('Error in getBusinessUnitsPerformance: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return [
                'labels' => [],
                'bakery' => [],
                'tools' => [],
                'academy' => []
            ];
        }
    }

    private function getRevenueDistribution()
    {
        try {
            $monthStart = Carbon::now()->startOfMonth();

            $bakeryRevenue = Order::whereHas('business', function($query) {
                $query->where('type', 'bakery');
            })->whereBetween('created_at', [$monthStart, now()])->sum('total_amount');

            $toolsRevenue = Order::whereHas('business', function($query) {
                $query->where('type', 'cake_tools');
            })->whereBetween('created_at', [$monthStart, now()])->sum('total_amount');

            $academyRevenue = Order::whereHas('business', function($query) {
                $query->where('type', 'academy');
            })->whereBetween('created_at', [$monthStart, now()])->sum('total_amount');

            return [
                'labels' => ['Bakery Shop', 'Cake Tools', 'Academy'],
                'values' => [$bakeryRevenue, $toolsRevenue, $academyRevenue]
            ];

        } catch (Exception $e) {
            Log::error('Error in getRevenueDistribution: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return [
                'labels' => ['Bakery Shop', 'Cake Tools', 'Academy'],
                'values' => [0, 0, 0]
            ];
        }
    }

    private function getBusinessGrowth()
    {
        try {
            $yearStart = Carbon::now()->startOfYear();
            $lastYearStart = Carbon::now()->subYear()->startOfYear();

            $businesses = ['Bakery Shop', 'Cake Tools', 'Academy'];
            $growthData = [];

            foreach ($businesses as $type) {
                $thisYearRevenue = Order::whereHas('business', function($query) use ($type) {
                    $query->where('type', strtolower(explode(' ', $type)[0]));
                })->whereBetween('created_at', [$yearStart, now()])->sum('total_amount');

                $lastYearRevenue = Order::whereHas('business', function($query) use ($type) {
                    $query->where('type', strtolower(explode(' ', $type)[0]));
                })->whereBetween('created_at', [$lastYearStart, $yearStart])->sum('total_amount');

                $growth = $lastYearRevenue > 0 ? (($thisYearRevenue - $lastYearRevenue) / $lastYearRevenue) * 100 : 0;
                $growthData[] = round($growth, 1);
            }

            return [
                'labels' => $businesses,
                'values' => $growthData
            ];

        } catch (Exception $e) {
            Log::error('Error in getBusinessGrowth: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return [
                'labels' => ['Bakery Shop', 'Cake Tools', 'Academy'],
                'values' => [0, 0, 0]
            ];
        }
    }

    public function bakery()
    {
        try {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $monthStart = Carbon::now()->startOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();

            // Get bakery business
            $business = Business::where('type', 'bakery')
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })->firstOrFail();

            // Calculate statistics
            $stats = [
                'sales_today' => Order::where('business_id', $business->id)
                    ->whereDate('created_at', $today)
                    ->sum('total_amount'),
                'sales_growth' => 0,
                'active_orders' => Order::where('business_id', $business->id)
                    ->whereIn('status', ['pending', 'processing'])
                    ->count(),
                'completed_orders' => Order::where('business_id', $business->id)
                    ->where('status', 'completed')
                    ->whereDate('created_at', $today)
                    ->count(),
                'revenue_month' => Order::where('business_id', $business->id)
                    ->whereBetween('created_at', [$monthStart, now()])
                    ->sum('total_amount'),
                'revenue_growth' => 0,
                'low_stock' => Product::where('business_id', $business->id)
                    ->where('stock_quantity', '<=', DB::raw('reorder_point'))
                    ->count()
            ];

            // Calculate growth percentages
            $yesterdaySales = Order::where('business_id', $business->id)
                ->whereDate('created_at', $yesterday)
                ->sum('total_amount');
            
            $lastMonthRevenue = Order::where('business_id', $business->id)
                ->whereBetween('created_at', [$lastMonthStart, $monthStart])
                ->sum('total_amount');

            $stats['sales_growth'] = $yesterdaySales > 0 ? 
                (($stats['sales_today'] - $yesterdaySales) / $yesterdaySales) * 100 : 0;
            
            $stats['revenue_growth'] = $lastMonthRevenue > 0 ? 
                (($stats['revenue_month'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

            // Get sales trend data with enhanced structure for combined chart
            $salesTrend = [];
            $labels = [];
            $data = [];
            $orders = [];
            
            for($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('D');
                $data[] = Order::where('business_id', $business->id)
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
                $orders[] = Order::where('business_id', $business->id)
                    ->whereDate('created_at', $date)
                    ->count();
            }
            
            $salesTrend = [
                'labels' => $labels,
                'data' => $data,
                'orders' => $orders
            ];

            // Get top products data with enhanced metrics
            $topProducts = Product::where('business_id', $business->id)
                ->select(
                    'products.id',
                    'products.name', 
                    'products.selling_price as price',
                    'products.cost_price',
                    DB::raw('SUM(order_items.quantity) as total_sold'),
                    DB::raw('SUM(order_items.quantity * products.selling_price) as total_revenue')
                )
                ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                ->leftJoin('orders', 'order_items.order_id', '=', 'orders.id')
                ->whereBetween('orders.created_at', [$monthStart, now()])
                ->groupBy('products.id', 'products.name', 'products.selling_price', 'products.cost_price')
                ->orderByDesc('total_sold')
                ->limit(10)
                ->get()
                ->map(function($product) {
                    // Calculate profit margin for each product
                    $margin = $product->price > 0 ? 
                        (($product->price - $product->cost_price) / $product->price) * 100 : 0;
                    $product->profit_margin = round($margin, 1);
                    return $product;
                });

            // Build product performance matrix data
            $productMatrix = [
                'stars' => 0,
                'cash_cows' => 0,
                'question_marks' => 0,
                'dogs' => 0,
                'data' => []
            ];

            // Process all products for matrix visualization
            $allProducts = Product::where('business_id', $business->id)
                ->select(
                    'products.id',
                    'products.name',
                    'products.selling_price',
                    'products.cost_price'
                )
                ->withCount(['orderItems as total_sold' => function($query) use ($monthStart) {
                    $query->whereHas('order', function($q) use ($monthStart) {
                        $q->whereBetween('created_at', [$monthStart, now()]);
                    });
                }])
                ->get();

            $maxSales = $allProducts->max('total_sold') ?: 1;

            foreach ($allProducts as $product) {
                $margin = $product->selling_price > 0 ? 
                    (($product->selling_price - $product->cost_price) / $product->selling_price) * 100 : 0;
                $salesVolume = ($product->total_sold / $maxSales) * 100;
                
                // Determine product category
                $category = '';
                if ($margin >= 50 && $salesVolume >= 50) {
                    $category = 'Stars';
                    $productMatrix['stars']++;
                } elseif ($margin < 50 && $salesVolume >= 50) {
                    $category = 'Cash Cows';
                    $productMatrix['cash_cows']++;
                } elseif ($margin >= 50 && $salesVolume < 50) {
                    $category = 'Question Marks';
                    $productMatrix['question_marks']++;
                } else {
                    $category = 'Dogs';
                    $productMatrix['dogs']++;
                }
                
                // Add to matrix data for visualization
                $productMatrix['data'][] = [
                    round($margin, 1),
                    round($salesVolume, 1),
                    $product->name,
                    $category
                ];
            }

            // Get recent orders
            $recentOrders = Order::where('business_id', $business->id)
                ->with('customer')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => optional($order->customer)->name ?? 'Guest',
                        'total' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at
                    ];
                });

            // Get low stock items with enhanced detail
            $lowStockItems = Product::where('business_id', $business->id)
                ->where('stock_quantity', '<=', DB::raw('reorder_point'))
                ->select(
                    'name', 
                    'stock_quantity as current_stock', 
                    'reorder_point as min_stock',
                    DB::raw('(stock_quantity / reorder_point * 100) as stock_level')
                )
                ->orderBy('stock_level')
                ->limit(5)
                ->get();

            // Get categories with product count
            $categories = Category::where('business_id', $business->id)
                ->withCount('products')
                ->get();

            // Initialize customer segments data
            $customerSegments = collect([
                ['type' => 'new', 'count' => Customer::where('created_at', '>=', $monthStart)->count()],
                ['type' => 'returning', 'count' => Customer::whereHas('orders', function($query) use ($monthStart) {
                    $query->where('created_at', '>=', $monthStart)
                        ->where('business_id', Business::where('type', 'bakery')->first()->id ?? 0);
                })->count()],
                ['type' => 'inactive', 'count' => Customer::where('created_at', '<', $monthStart)
                    ->whereDoesntHave('orders', function($query) use ($monthStart) {
                        $query->where('created_at', '>=', Carbon::now()->subMonths(3));
                    })->count()]
            ]);

            // Enhanced cost analysis data
            $costAnalysis = [
                'gross_margin' => 42.5,
                'prev_gross_margin' => 40.2,
                'cost_per_sale' => 28.75,
                'ingredient_cost_percent' => 65,
                'revenue_trend' => [18500, 19200, 20100, 19800, 21500, 22800, 23900],
                'cost_trend' => [10600, 11100, 11600, 11400, 12300, 13100, 13700],
                'profit_trend' => [7900, 8100, 8500, 8400, 9200, 9700, 10200],
                'dates' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
            ];
            
            // Enhance financials data
            $financials = [
                'receivables' => 7850.25,
                'receivables_count' => 12,
                'payables' => 5420.80,
                'payables_count' => 8,
                'credits' => 1250.50,
                'credits_count' => 5
            ];
            
            $receivables = collect([
                ['customer' => 'Customer A', 'amount' => 1250.00, 'due_date' => now()->addDays(5)],
                ['customer' => 'Customer B', 'amount' => 975.50, 'due_date' => now()->addDays(3)]
            ]);
            
            $payables = collect([
                ['supplier' => 'Supplier X', 'amount' => 2100.75, 'due_date' => now()->addDays(7)],
                ['supplier' => 'Supplier Y', 'amount' => 1450.25, 'due_date' => now()->addDays(4)]
            ]);
            
            $credits = collect([
                ['customer' => 'Customer C', 'amount' => 350.00, 'expiry_date' => now()->addDays(30)],
                ['customer' => 'Customer D', 'amount' => 425.75, 'expiry_date' => now()->addDays(60)]
            ]);
            
            // Add order status data
            $completed = Order::where('business_id', $business->id)
                ->where('status', 'completed')
                ->count();
            $inProgress = Order::where('business_id', $business->id)
                ->where('status', 'processing')
                ->count();
            $pending = Order::where('business_id', $business->id)
                ->where('status', 'pending')
                ->count();
                
            $orderStatus = [
                'data' => [$completed, $inProgress, $pending],
                'total' => $completed + $inProgress + $pending
            ];
            
            // Add hourly pattern data
            $hourlyData = [];
            $hourlyLabels = [];
            
            for ($hour = 0; $hour < 24; $hour++) {
                $hourlyLabels[] = sprintf('%02d:00', $hour);
                $startTime = Carbon::today()->setHour($hour)->setMinute(0)->setSecond(0);
                $endTime = Carbon::today()->setHour($hour)->setMinute(59)->setSecond(59);
                
                $hourlyData[] = Order::where('business_id', $business->id)
                    ->whereBetween('created_at', [$startTime, $endTime])
                    ->count();
            }
            
            $hourlyPattern = [
                'data' => $hourlyData,
                'labels' => $hourlyLabels
            ];

            // Mock ingredient usage data (can be replaced with real data)
            $ingredientUsage = collect([
                (object)['name' => 'Flour', 'used_quantity' => 120.5, 'unit' => 'kg', 'cost' => 241.00, 'stock_level' => 72],
                (object)['name' => 'Sugar', 'used_quantity' => 85.2, 'unit' => 'kg', 'cost' => 170.40, 'stock_level' => 64],
                (object)['name' => 'Butter', 'used_quantity' => 62.8, 'unit' => 'kg', 'cost' => 314.00, 'stock_level' => 45],
                (object)['name' => 'Eggs', 'used_quantity' => 520, 'unit' => 'pcs', 'cost' => 156.00, 'stock_level' => 38],
                (object)['name' => 'Milk', 'used_quantity' => 95.3, 'unit' => 'L', 'cost' => 190.60, 'stock_level' => 52],
                (object)['name' => 'Chocolate', 'used_quantity' => 42.1, 'unit' => 'kg', 'cost' => 294.70, 'stock_level' => 25],
                (object)['name' => 'Vanilla Extract', 'used_quantity' => 5.2, 'unit' => 'L', 'cost' => 156.00, 'stock_level' => 18],
                (object)['name' => 'Baking Powder', 'used_quantity' => 8.5, 'unit' => 'kg', 'cost' => 42.50, 'stock_level' => 62],
                (object)['name' => 'Salt', 'used_quantity' => 4.2, 'unit' => 'kg', 'cost' => 12.60, 'stock_level' => 85],
                (object)['name' => 'Yeast', 'used_quantity' => 3.1, 'unit' => 'kg', 'cost' => 46.50, 'stock_level' => 27]
            ]);

            return view('dashboard.bakery.index', compact(
                'stats',
                'salesTrend',
                'topProducts',
                'recentOrders',
                'lowStockItems',
                'categories',
                'ingredientUsage',
                'customerSegments',
                'costAnalysis',
                'financials',
                'receivables',
                'payables',
                'credits',
                'orderStatus',
                'hourlyPattern',
                'productMatrix'
            ));

        } catch (Exception $e) {
            Log::error('Error in bakery dashboard: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id()
            ]);
            return view('dashboard.bakery.index')->with('error', 'An error occurred while loading the dashboard data. Please try again later.');
        }
    }

    public function tools()
    {
        try {
            $today = Carbon::today();
            $yesterday = Carbon::yesterday();
            $monthStart = Carbon::now()->startOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();

            // Get tools business
            $business = Business::where('type', 'cake_tools')
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })->firstOrFail();

            // Calculate statistics
            $stats = [
                'sales_today' => Order::where('business_id', $business->id)
                    ->whereDate('created_at', $today)
                    ->sum('total_amount'),
                'sales_growth' => 0,
                'inventory_value' => Product::where('business_id', $business->id)
                    ->sum(DB::raw('stock_quantity * cost_price')),
                'total_items' => Product::where('business_id', $business->id)
                    ->where('stock_quantity', '>', 0)
                    ->count(),
                'pending_orders' => Order::where('business_id', $business->id)
                    ->where('status', 'pending')
                    ->count(),
                'low_stock' => Product::where('business_id', $business->id)
                    ->where('stock_quantity', '<=', DB::raw('reorder_point'))
                    ->count()
            ];

            // Calculate growth percentages
            $yesterdaySales = Order::where('business_id', $business->id)
                ->whereDate('created_at', $yesterday)
                ->sum('total_amount');

            $stats['sales_growth'] = $yesterdaySales > 0 ? 
                (($stats['sales_today'] - $yesterdaySales) / $yesterdaySales) * 100 : 0;

            // Get sales trend data
            $labels = [];
            $data = [];
            
            for($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('D');
                $data[] = Order::where('business_id', $business->id)
                    ->whereDate('created_at', $date)
                    ->sum('total_amount');
            }
            
            $salesTrend = [
                'labels' => $labels,
                'data' => $data
            ];

            // Get categories with product count
            $categories = Category::where('business_id', $business->id)
                ->withCount('products')
                ->get();

            // Get revenue by category data
            $categoryLabels = [];
            $categoryData = [];

            foreach($categories as $category) {
                $revenue = Order::where('business_id', $business->id)
                    ->whereHas('items.product', function($query) use ($category) {
                        $query->where('category_id', $category->id);
                    })
                    ->whereBetween('created_at', [$monthStart, now()])
                    ->sum('total_amount');

                $categoryLabels[] = $category->name;
                $categoryData[] = $revenue;
            }

            $revenueByCategory = [
                'labels' => $categoryLabels,
                'data' => $categoryData
            ];

            // Get top selling products
            $topProducts = Product::where('business_id', $business->id)
                ->withCount(['orderItems as total_sold' => function($query) use ($monthStart) {
                    $query->whereBetween('created_at', [$monthStart, now()]);
                }])
                ->withSum(['orderItems as revenue' => function($query) use ($monthStart) {
                    $query->whereBetween('created_at', [$monthStart, now()]);
                }], 'subtotal')
                ->orderByDesc('total_sold')
                ->limit(5)
                ->get();

            // Get recent orders
            $recentOrders = Order::where('business_id', $business->id)
                ->with('customer')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function($order) {
                    return [
                        'id' => $order->id,
                        'customer_name' => optional($order->customer)->name ?? 'Guest',
                        'total' => $order->total_amount,
                        'status' => $order->status
                    ];
                });

            // Get inventory alerts
            $inventoryAlerts = Product::where('business_id', $business->id)
                ->where('stock_quantity', '<=', DB::raw('reorder_point'))
                ->select('name', 'stock_quantity as current_stock', 'reorder_point as min_stock')
                ->limit(5)
                ->get();

            return view('dashboard.tools.index', compact(
                'stats',
                'salesTrend',
                'categories',
                'revenueByCategory',
                'topProducts',
                'recentOrders',
                'inventoryAlerts'
            ));

        } catch (Exception $e) {
            Log::error('Error in tools dashboard: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id()
            ]);
            return view('dashboard.tools.index')->with('error', 'An error occurred while loading the dashboard data. Please try again later.');
        }
    }

    public function academy()
    {
        try {
            $today = Carbon::today();
            $monthStart = Carbon::now()->startOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();

            // Get academy business
            $business = Business::where('type', 'academy')
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })->firstOrFail();

            // Calculate statistics
            $stats = [
                'total_students' => Student::where('business_id', $business->id)
                    ->count(),
                'active_students' => Student::where('business_id', $business->id)
                    ->where('status', 'active')
                    ->count(),
                'student_growth' => 0,
                'revenue_month' => Order::where('business_id', $business->id)
                    ->whereBetween('created_at', [$monthStart, now()])
                    ->sum('total_amount'),
                'revenue_growth' => 0,
                'active_classes' => Course::where('business_id', $business->id)
                    ->where('status', 'active')
                    ->count(),
                'total_classes' => Course::where('business_id', $business->id)
                    ->count(),
                'total_lessons' => Lesson::whereHas('course', function($query) use ($business) {
                    $query->where('business_id', $business->id);
                })->count(),
                'completion_rate' => StudentProgress::whereHas('course', function($query) use ($business) {
                    $query->where('business_id', $business->id);
                })->avg('progress') ?? 0
            ];

            // Calculate growth percentages
            $lastMonthStudents = Student::where('business_id', $business->id)
                ->where('status', 'active')
                ->where('created_at', '<', $monthStart)
                ->count();

            $lastMonthRevenue = Order::where('business_id', $business->id)
                ->whereBetween('created_at', [$lastMonthStart, $monthStart])
                ->sum('total_amount');

            $stats['student_growth'] = $lastMonthStudents > 0 ? 
                (($stats['active_students'] - $lastMonthStudents) / $lastMonthStudents) * 100 : 0;

            $stats['revenue_growth'] = $lastMonthRevenue > 0 ? 
                (($stats['revenue_month'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

            // Get enrollment trend data
            $labels = [];
            $data = [];
            
            for($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $labels[] = $date->format('D');
                $data[] = Student::where('business_id', $business->id)
                    ->whereDate('created_at', $date)
                    ->count();
            }
            
            $enrollmentTrend = [
                'labels' => $labels,
                'data' => $data
            ];

            // Get popular courses data
            $popularCourses = Course::where('business_id', $business->id)
                ->withCount('students')
                ->withSum(['orderItems as revenue' => function($query) use ($monthStart) {
                    $query->whereBetween('created_at', [$monthStart, now()]);
                }], 'subtotal')
                ->with('instructor')
                ->orderByDesc('students_count')
                ->limit(5)
                ->get();

            // Get recent enrollments
            $recentEnrollments = StudentCourse::whereHas('course', function($query) use ($business) {
                    $query->where('business_id', $business->id);
                })
                ->with(['student', 'course'])
                ->latest()
                ->limit(5)
                ->get();

            // Get upcoming classes
            $upcomingClasses = Course::where('business_id', $business->id)
                ->where('status', 'active')
                ->where('start_date', '>', now())
                ->with('instructor')
                ->orderBy('start_date')
                ->limit(5)
                ->get();

            // Get course progress data
            $courseProgress = Course::where('business_id', $business->id)
                ->withCount('students as enrolled_students')
                ->with('studentProgress')
                ->get()
                ->map(function($course) {
                    return [
                        'name' => $course->name,
                        'enrolled_students' => $course->enrolled_students,
                        'average_progress' => $course->studentProgress->avg('progress') ?? 0
                    ];
                });

            return view('dashboard.academy.index', compact(
                'stats',
                'enrollmentTrend',
                'popularCourses',
                'recentEnrollments',
                'upcomingClasses',
                'courseProgress'
            ));

        } catch (Exception $e) {
            Log::error('Error in academy dashboard: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id()
            ]);
            return view('dashboard.academy.index')->with('error', 'An error occurred while loading the dashboard data. Please try again later.');
        }
    }

    public function filterToolsData(Request $request)
    {
        try {
            $chart = $request->query('chart');
            $period = $request->query('period');
            $business = Business::where('type', 'cake_tools')
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })->firstOrFail();

            switch($chart) {
                case 'salesTrendChart':
                    $data = $this->getFilteredSalesTrend($business->id, $period);
                    break;
                case 'categoryChart':
                    $data = $this->getFilteredCategoryDistribution($business->id, $period);
                    break;
                case 'revenueCategoryChart':
                    $data = $this->getFilteredRevenueByCategory($business->id, $period);
                    break;
                default:
                    return response()->json(['error' => 'Invalid chart type'], 400);
            }

            return response()->json($data);

        } catch (Exception $e) {
            Log::error('Error in filterToolsData: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'chart' => $request->query('chart'),
                'period' => $request->query('period')
            ]);
            return response()->json(['error' => 'An error occurred while filtering data'], 500);
        }
    }

    private function getFilteredSalesTrend($businessId, $period)
    {
        $labels = [];
        $data = [];

        switch($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $format = 'D';
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $format = 'd M';
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $format = 'M Y';
                break;
            default:
                $startDate = Carbon::now()->subDays(6);
                $format = 'D';
        }

        for($date = $startDate; $date <= now(); $date->addDay()) {
            $labels[] = $date->format($format);
            $data[] = Order::where('business_id', $businessId)
                ->whereDate('created_at', $date)
                ->sum('total_amount');
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getFilteredCategoryDistribution($businessId, $period)
    {
        $query = Category::where('business_id', $businessId);

        if ($period === 'active') {
            $query->whereHas('products', function($q) {
                $q->where('status', 'active');
            });
        }

        $categories = $query->withCount('products')->get();

        return [
            'data' => $categories->pluck('products_count')->toArray(),
            'labels' => $categories->pluck('name')->toArray()
        ];
    }

    private function getFilteredRevenueByCategory($businessId, $period)
    {
        $categories = Category::where('business_id', $businessId)->get();
        $labels = [];
        $data = [];

        switch($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
        }

        foreach($categories as $category) {
            $revenue = Order::where('business_id', $businessId)
                ->whereHas('items.product', function($query) use ($category) {
                    $query->where('category_id', $category->id);
                })
                ->whereBetween('created_at', [$startDate, now()])
                ->sum('total_amount');

            $labels[] = $category->name;
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function filterAcademyData(Request $request)
    {
        try {
            $chart = $request->query('chart');
            $period = $request->query('period');
            $business = Business::where('type', 'academy')
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })->firstOrFail();

            switch($chart) {
                case 'enrollmentChart':
                    $data = $this->getFilteredEnrollmentTrend($business->id, $period);
                    break;
                case 'courseProgressChart':
                    $data = $this->getFilteredCourseProgress($business->id, $period);
                    break;
                default:
                    return response()->json(['error' => 'Invalid chart type'], 400);
            }

            return response()->json($data);

        } catch (Exception $e) {
            Log::error('Error in filterAcademyData: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id(),
                'chart' => $request->query('chart'),
                'period' => $request->query('period')
            ]);
            return response()->json(['error' => 'An error occurred while filtering data'], 500);
        }
    }

    public function refreshAcademyData()
    {
        try {
            $business = Business::where('type', 'academy')
                ->whereHas('users', function($query) {
                    $query->where('user_id', auth()->id());
                })->firstOrFail();

            $today = Carbon::today();
            $monthStart = Carbon::now()->startOfMonth();
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();

            // Calculate statistics
            $stats = [
                'total_students' => Student::where('business_id', $business->id)->count(),
                'active_students' => Student::where('business_id', $business->id)
                    ->where('status', 'active')
                    ->count(),
                'revenue_month' => Order::where('business_id', $business->id)
                    ->whereBetween('created_at', [$monthStart, now()])
                    ->sum('total_amount'),
                'active_classes' => Course::where('business_id', $business->id)
                    ->where('status', 'active')
                    ->count(),
                'total_classes' => Course::where('business_id', $business->id)->count(),
                'completion_rate' => StudentProgress::whereHas('course', function($query) use ($business) {
                    $query->where('business_id', $business->id);
                })->avg('progress') ?? 0
            ];

            // Calculate growth percentages
            $lastMonthStudents = Student::where('business_id', $business->id)
                ->where('status', 'active')
                ->where('created_at', '<', $monthStart)
                ->count();

            $lastMonthRevenue = Order::where('business_id', $business->id)
                ->whereBetween('created_at', [$lastMonthStart, $monthStart])
                ->sum('total_amount');

            $stats['student_growth'] = $lastMonthStudents > 0 ? 
                (($stats['active_students'] - $lastMonthStudents) / $lastMonthStudents) * 100 : 0;

            $stats['revenue_growth'] = $lastMonthRevenue > 0 ? 
                (($stats['revenue_month'] - $lastMonthRevenue) / $lastMonthRevenue) * 100 : 0;

            // Get enrollment trend data
            $enrollmentTrend = $this->getFilteredEnrollmentTrend($business->id, 'week');

            // Get course progress data
            $courseProgress = $this->getFilteredCourseProgress($business->id, 'all');

            return response()->json([
                'stats' => $stats,
                'enrollmentTrend' => $enrollmentTrend,
                'courseProgress' => $courseProgress
            ]);

        } catch (Exception $e) {
            Log::error('Error in refreshAcademyData: ' . $e->getMessage(), [
                'exception' => $e,
                'user_id' => auth()->id()
            ]);
            return response()->json(['error' => 'An error occurred while refreshing data'], 500);
        }
    }

    private function getFilteredEnrollmentTrend($businessId, $period)
    {
        $labels = [];
        $data = [];

        switch($period) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $format = 'D';
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $format = 'd M';
                break;
            case 'year':
                $startDate = Carbon::now()->startOfYear();
                $format = 'M Y';
                break;
            default:
                $startDate = Carbon::now()->subDays(6);
                $format = 'D';
        }

        for($date = $startDate; $date <= now(); $date->addDay()) {
            $labels[] = $date->format($format);
            $data[] = Student::where('business_id', $businessId)
                ->whereDate('created_at', $date)
                ->count();
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    private function getFilteredCourseProgress($businessId, $period)
    {
        $query = Course::where('business_id', $businessId);

        switch($period) {
            case 'active':
                $query->where('status', 'active');
                break;
            case 'completed':
                $query->whereHas('studentProgress', function($q) {
                    $q->where('status', 'completed');
                });
                break;
            case 'month':
                $query->where('created_at', '>=', Carbon::now()->startOfMonth());
                break;
        }

        $courses = $query->withCount('students')
            ->with('studentProgress')
            ->get()
            ->map(function($course) {
                return [
                    'name' => $course->name,
                    'enrolled_students' => $course->students_count,
                    'average_progress' => round($course->studentProgress->avg('progress') ?? 0, 1)
                ];
            });

        return [
            'labels' => $courses->pluck('name')->toArray(),
            'data' => $courses->pluck('average_progress')->toArray()
        ];
    }
} 