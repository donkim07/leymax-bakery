<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Business;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Display global sales overview report.
     *
     * @return \Illuminate\View\View
     */
    public function sales()
    {
        // Get sales data across all businesses for the last 12 months
        $monthlySales = Order::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_amount) as total_sales'),
            DB::raw('COUNT(*) as order_count')
        )
            ->where('status', 'completed')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Get top-selling products
        $topProducts = Product::select(
            'products.id',
            'products.name',
            'products.sku',
            DB::raw('SUM(order_items.quantity) as total_quantity'),
            DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
        )
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_sales', 'desc')
            ->limit(10)
            ->get();
        
        // Get sales by business type
        $salesByBusinessType = Business::select(
            'businesses.type',
            DB::raw('SUM(orders.total_amount) as total_sales'),
            DB::raw('COUNT(orders.id) as order_count')
        )
            ->join('orders', 'businesses.id', '=', 'orders.business_id')
            ->where('orders.status', 'completed')
            ->groupBy('businesses.type')
            ->get();
        
        return view('reports.sales', compact('monthlySales', 'topProducts', 'salesByBusinessType'));
    }
    
    /**
     * Display global inventory status report.
     *
     * @return \Illuminate\View\View
     */
    public function inventory()
    {
        // Get inventory status across all businesses
        $inventoryStatus = Inventory::select(
            'businesses.name as business_name',
            'businesses.type as business_type',
            'products.name as product_name',
            'products.sku',
            'inventories.quantity',
            'products.reorder_point as reorder_level'
        )
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->join('businesses', 'inventories.business_id', '=', 'businesses.id')
            ->orderBy('inventories.quantity', 'asc')
            ->get();
        
        // Get products that need reordering
        $lowStockProducts = Inventory::select(
            'businesses.name as business_name',
            'businesses.type as business_type',
            'products.name as product_name',
            'products.sku',
            'inventories.quantity',
            'products.reorder_point as reorder_level'
        )
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->join('businesses', 'inventories.business_id', '=', 'businesses.id')
            ->whereRaw('inventories.quantity <= products.reorder_point')
            ->orderBy('inventories.quantity', 'asc')
            ->get();
        
        // Get total inventory value by business
        $inventoryValue = Inventory::select(
            'businesses.name as business_name',
            'businesses.type as business_type',
            DB::raw('SUM(inventories.quantity * products.cost_price) as total_value'),
            DB::raw('COUNT(DISTINCT products.id) as total_products')
        )
            ->join('products', 'inventories.product_id', '=', 'products.id')
            ->join('businesses', 'inventories.business_id', '=', 'businesses.id')
            ->groupBy('businesses.name', 'businesses.type')
            ->get();
        
        return view('reports.inventory', compact('inventoryStatus', 'lowStockProducts', 'inventoryValue'));
    }
    
    /**
     * Display global financial summary report.
     *
     * @return \Illuminate\View\View
     */
    public function financial()
    {
        // Get monthly revenue and expenses for the last 12 months
        $financialData = Transaction::select(
            DB::raw('YEAR(transactions.created_at) as year'),
            DB::raw('MONTH(transactions.created_at) as month'),
            DB::raw('SUM(CASE WHEN transactions.type = "income" THEN transactions.amount ELSE 0 END) as total_income'),
            DB::raw('SUM(CASE WHEN transactions.type = "expense" THEN transactions.amount ELSE 0 END) as total_expenses'),
            DB::raw('SUM(CASE WHEN transactions.type = "income" THEN transactions.amount ELSE -transactions.amount END) as net_profit')
        )
            ->join('businesses', 'transactions.business_id', '=', 'businesses.id')
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
        
        // Get financial summary by business type
        $businessSummary = Transaction::select(
            'businesses.type as business_type',
            DB::raw('SUM(CASE WHEN transactions.type = "income" THEN transactions.amount ELSE 0 END) as total_income'),
            DB::raw('SUM(CASE WHEN transactions.type = "expense" THEN transactions.amount ELSE 0 END) as total_expenses'),
            DB::raw('SUM(CASE WHEN transactions.type = "income" THEN transactions.amount ELSE -transactions.amount END) as net_profit')
        )
            ->join('businesses', 'transactions.business_id', '=', 'businesses.id')
            ->groupBy('businesses.type')
            ->get();
        
        // Get top expense categories
        $topExpenses = Transaction::select(
            'transactions.category',
            DB::raw('SUM(transactions.amount) as total_amount')
        )
            ->where('transactions.type', 'expense')
            ->groupBy('transactions.category')
            ->orderBy('total_amount', 'desc')
            ->limit(5)
            ->get();
        
        return view('reports.financial', compact('financialData', 'businessSummary', 'topExpenses'));
    }
    
    /**
     * Display bakery specific reports.
     *
     * @return \Illuminate\View\View
     */
    public function bakery()
    {
        return view('bakery.reports.index');
    }
    
    /**
     * Display tools specific reports.
     *
     * @return \Illuminate\View\View
     */
    public function tools()
    {
        return view('tools.reports.index');
    }
    
    /**
     * Display academy specific reports.
     *
     * @return \Illuminate\View\View
     */
    public function academy()
    {
        return view('academy.reports.index');
    }

    /**
     * Alias for sales method - used for global route compatibility
     *
     * @return \Illuminate\View\View
     */
    public function globalSales()
    {
        return view('reports.sales');
    }

    /**
     * Alias for inventory method - used for global route compatibility
     *
     * @return \Illuminate\View\View
     */
    public function globalInventory()
    {
        return view('reports.inventory');
    }

    /**
     * Alias for financial method - used for global route compatibility
     *
     * @return \Illuminate\View\View
     */
    public function globalFinancial()
    {
        return view('reports.financial');
    }
} 