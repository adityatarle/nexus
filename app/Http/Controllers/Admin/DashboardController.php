<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;
use App\Models\AgricultureOrder;
use App\Models\User;
use App\Models\DealerRegistration;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get comprehensive statistics
        $stats = [
            'total_products' => AgricultureProduct::count(),
            'active_products' => AgricultureProduct::where('is_active', true)->count(),
            'total_categories' => AgricultureCategory::count(),
            'total_orders' => AgricultureOrder::count(),
            'pending_orders' => AgricultureOrder::where('order_status', 'pending')->count(),
            'confirmed_orders' => AgricultureOrder::where('order_status', 'confirmed')->count(),
            'shipped_orders' => AgricultureOrder::where('order_status', 'shipped')->count(),
            'delivered_orders' => AgricultureOrder::where('order_status', 'delivered')->count(),
            'cancelled_orders' => AgricultureOrder::where('order_status', 'cancelled')->count(),
            'total_revenue' => AgricultureOrder::where('payment_status', 'paid')->sum('total_amount') ?? 0,
            'monthly_revenue' => AgricultureOrder::where('payment_status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount') ?? 0,
            'low_stock_products' => AgricultureProduct::where('stock_quantity', '<', 10)->count(),
            'out_of_stock_products' => AgricultureProduct::where('stock_quantity', 0)->count(),
            'featured_products' => AgricultureProduct::where('is_featured', true)->count(),
            'total_customers' => User::customers()->count(),
            'total_dealers' => User::dealers()->count(),
            'approved_dealers' => User::where('role', 'dealer')->where('is_dealer_approved', true)->count(),
            'pending_dealer_registrations' => DealerRegistration::where('status', 'pending')->count(),
            'rejected_dealer_registrations' => DealerRegistration::where('status', 'rejected')->count(),
        ];

        // Recent orders
        $recentOrders = AgricultureOrder::with('items.product')
            ->latest()
            ->take(5)
            ->get();

        // Top selling products
        $topProducts = AgricultureProduct::withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
        
        // Ensure order_items_count is available
        $topProducts = $topProducts->map(function($product) {
            if (!isset($product->order_items_count)) {
                $product->order_items_count = $product->orderItems()->count();
            }
            return $product;
        });

        // Recent dealer registrations
        $recentDealerRegistrations = DealerRegistration::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Low stock products
        $lowStockProducts = AgricultureProduct::where('stock_quantity', '<', 10)
            ->orderBy('stock_quantity', 'asc')
            ->take(10)
            ->get();

        // Recent customers
        $recentCustomers = User::customers()
            ->latest()
            ->take(5)
            ->get();

        // Top selling products (duplicate removed - using $topProducts above)
        $topSellingProducts = $topProducts;

        // Orders by status for chart
        $ordersByStatus = [
            'pending' => AgricultureOrder::where('order_status', 'pending')->count(),
            'confirmed' => AgricultureOrder::where('order_status', 'confirmed')->count(),
            'shipped' => AgricultureOrder::where('order_status', 'shipped')->count(),
            'delivered' => AgricultureOrder::where('order_status', 'delivered')->count(),
            'cancelled' => AgricultureOrder::where('order_status', 'cancelled')->count(),
        ];

        // Revenue by month for the last 12 months
        $revenueByMonth = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = AgricultureOrder::where('payment_status', 'paid')
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');
            $revenueByMonth[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue ?? 0
            ];
        }

        // Monthly sales data for chart (driver-aware for SQLite/MySQL)
        $driver = config('database.default');
        $connection = config("database.connections.$driver.driver");

        if ($connection === 'sqlite') {
            $monthlySales = AgricultureOrder::selectRaw('strftime("%m", created_at) as month, SUM(total_amount) as total')
                ->whereRaw('strftime("%Y", created_at) = ?', [date('Y')])
                ->where('payment_status', 'paid')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } else {
            $monthlySales = AgricultureOrder::selectRaw('MONTH(created_at) as month, SUM(total_amount) as total')
                ->whereYear('created_at', date('Y'))
                ->where('payment_status', 'paid')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        // Category distribution
        $categoryStats = AgricultureCategory::withCount('products')
            ->orderBy('products_count', 'desc')
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentOrders',
            'topProducts',
            'monthlySales',
            'categoryStats',
            'recentDealerRegistrations',
            'lowStockProducts',
            'recentCustomers',
            'topSellingProducts',
            'ordersByStatus',
            'revenueByMonth'
        ));
    }
}