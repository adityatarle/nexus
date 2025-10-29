<?php

namespace App\Http\Controllers;

use App\Models\AgricultureCategory;
use App\Models\AgricultureProduct;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get categories that have products in stock
        $categories = AgricultureCategory::active()
            ->whereHas('products', function($query) {
                $query->where('is_active', true)
                      ->where('in_stock', true);
            })
            ->withCount(['products' => function($query) {
                $query->where('is_active', true)
                      ->where('in_stock', true);
            }])
            ->ordered()
            ->take(8)
            ->get();
        
        $featuredProducts = AgricultureProduct::active()
            ->featured()
            ->inStock()
            ->with('category')
            ->take(8)
            ->get();
        
        $newArrivals = AgricultureProduct::active()
            ->inStock()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();
        
        $bestSellers = AgricultureProduct::active()
            ->inStock()
            ->with('category')
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(8)
            ->get();
        
        // Get statistics
        $stats = [
            'total_products' => AgricultureProduct::active()->count(),
            'total_customers' => User::customers()->count(),
            'total_categories' => AgricultureCategory::active()->count(),
            'total_farmers_served' => User::customers()->count() + User::where('role', 'dealer')->where('is_dealer_approved', true)->count(),
            'total_service_centers' => Setting::get('total_service_centers', 15),
        ];
        
        return view('home', compact('categories', 'featuredProducts', 'newArrivals', 'bestSellers', 'stats'));
    }
}
