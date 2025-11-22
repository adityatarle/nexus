<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgricultureOrder;
use App\Models\AgricultureProduct;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_revenue' => AgricultureOrder::where('payment_status', 'paid')->sum('total_amount'),
            'total_orders' => AgricultureOrder::count(),
            'total_products' => AgricultureProduct::count(),
            'total_customers' => User::customers()->count(),
            'total_dealers' => User::dealers()->where('is_dealer_approved', true)->count(),
        ];

        // Revenue trends (last 12 months)
        $revenueTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = AgricultureOrder::where('payment_status', 'paid')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');
            $revenueTrends[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Top selling products
        $topProducts = AgricultureProduct::withCount('orderItems')
            ->withSum('orderItems', 'quantity')
            ->orderBy('order_items_count', 'desc')
            ->take(10)
            ->get();

        // Sales by category
        $categoryStats = \App\Models\AgricultureCategory::withCount('products')
            ->with(['products' => function($query) {
                $query->withCount('orderItems');
            }])
            ->get()
            ->map(function($category) {
                return [
                    'name' => $category->name,
                    'total_products' => $category->products_count,
                    'total_sales' => $category->products->sum('order_items_count'),
                ];
            });

        return view('admin.reports.index', compact('stats', 'revenueTrends', 'topProducts', 'categoryStats'));
    }

    /**
     * Sales report
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));
        $type = $request->get('type', 'both'); // 'customer', 'dealer', or 'both'

        $ordersQuery = AgricultureOrder::with('user', 'items.product')
            ->whereBetween('created_at', [$startDate, $endDate]);

        // Filter by type
        if ($type === 'customer') {
            $ordersQuery->whereHas('user', function($q) {
                $q->where('role', 'customer');
            });
        } elseif ($type === 'dealer') {
            $ordersQuery->whereHas('user', function($q) {
                $q->where('role', 'dealer');
            });
        }

        $orders = $ordersQuery->latest()->paginate(20);

        // Base query for summary
        $summaryQuery = AgricultureOrder::whereBetween('created_at', [$startDate, $endDate]);
        $customerQuery = AgricultureOrder::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('user', function($q) {
                $q->where('role', 'customer');
            });
        $dealerQuery = AgricultureOrder::whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('user', function($q) {
                $q->where('role', 'dealer');
            });

        // Apply type filter to summary if needed
        if ($type === 'customer') {
            $summaryQuery = $customerQuery;
        } elseif ($type === 'dealer') {
            $summaryQuery = $dealerQuery;
        }

        $summary = [
            'total_orders' => $summaryQuery->count(),
            'total_revenue' => $summaryQuery->where('payment_status', 'paid')->sum('total_amount'),
            'average_order_value' => $summaryQuery->avg('total_amount'),
            'customer_orders' => $customerQuery->count(),
            'dealer_orders' => $dealerQuery->count(),
        ];

        return view('admin.reports.sales', compact('orders', 'summary', 'startDate', 'endDate', 'type'));
    }

    /**
     * Inventory report
     */
    public function inventory()
    {
        $lowStockProducts = AgricultureProduct::where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->get();

        $outOfStockProducts = AgricultureProduct::where('stock_quantity', 0)
            ->get();

        $allProducts = AgricultureProduct::with('category')
            ->orderBy('stock_quantity', 'asc')
            ->paginate(20);

        $stats = [
            'total_products' => AgricultureProduct::count(),
            'low_stock' => $lowStockProducts->count(),
            'out_of_stock' => $outOfStockProducts->count(),
            'total_stock_value' => AgricultureProduct::sum(DB::raw('stock_quantity * price')),
        ];

        return view('admin.reports.inventory', compact('lowStockProducts', 'outOfStockProducts', 'allProducts', 'stats'));
    }

    /**
     * Customers report
     */
    public function customers(Request $request)
    {
        $query = User::customers()->withCount('agricultureOrders')
            ->withSum('agricultureOrders', 'total_amount');

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'orders':
                    $query->orderBy('agriculture_orders_count', 'desc');
                    break;
                case 'spent':
                    $query->orderBy('agriculture_orders_sum_total_amount', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $customers = $query->paginate(20);

        $stats = [
            'total_customers' => User::customers()->count(),
            'new_this_month' => User::customers()->whereMonth('created_at', now()->month)->count(),
            'active_customers' => User::customers()->has('agricultureOrders')->count(),
            'average_lifetime_value' => User::customers()->withSum('agricultureOrders', 'total_amount')
                ->get()
                ->avg('agriculture_orders_sum_total_amount'),
        ];

        return view('admin.reports.customers', compact('customers', 'stats'));
    }

    /**
     * Dealers report
     */
    public function dealers(Request $request)
    {
        $query = User::dealers()->withCount('agricultureOrders')
            ->withSum('agricultureOrders', 'total_amount');

        // Sort
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'orders':
                    $query->orderBy('agriculture_orders_count', 'desc');
                    break;
                case 'spent':
                    $query->orderBy('agriculture_orders_sum_total_amount', 'desc');
                    break;
                default:
                    $query->latest();
            }
        } else {
            $query->latest();
        }

        $dealers = $query->paginate(20);

        $stats = [
            'total_dealers' => User::dealers()->count(),
            'approved_dealers' => User::dealers()->where('is_dealer_approved', true)->count(),
            'pending_dealers' => User::dealers()->where('is_dealer_approved', false)->count(),
            'new_this_month' => User::dealers()->whereMonth('created_at', now()->month)->count(),
            'active_dealers' => User::dealers()->has('agricultureOrders')->count(),
            'average_lifetime_value' => User::dealers()->withSum('agricultureOrders', 'total_amount')
                ->get()
                ->avg('agriculture_orders_sum_total_amount'),
        ];

        return view('admin.reports.dealers', compact('dealers', 'stats'));
    }
}


















