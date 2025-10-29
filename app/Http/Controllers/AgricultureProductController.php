<?php

namespace App\Http\Controllers;

use App\Models\AgricultureProduct;
use App\Models\AgricultureCategory;
use Illuminate\Http\Request;

class AgricultureProductController extends Controller
{
    public function index(Request $request)
    {
        $query = AgricultureProduct::active()->inStock()->with('category');
        
        // Filter by category
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }
        
        // Filter by brand
        if ($request->filled('brand')) {
            $query->byBrand($request->brand);
        }
        
        // Filter by power source
        if ($request->filled('power_source')) {
            $query->byPowerSource($request->power_source);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%");
            });
        }
        
        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->latest();
                break;
            case 'name':
            default:
                $query->orderBy('name', $sortDirection);
                break;
        }
        
        $products = $query->paginate(12);
        $categories = AgricultureCategory::active()->ordered()->get();
        $brands = AgricultureProduct::active()->distinct()->pluck('brand')->filter();
        $powerSources = AgricultureProduct::active()->distinct()->pluck('power_source')->filter();
        
        // Get current user for pricing
        $user = auth()->user();
        
        return view('agriculture.products.index', compact('products', 'categories', 'brands', 'powerSources', 'user'));
    }
    
    public function show(AgricultureProduct $product)
    {
        $relatedProducts = AgricultureProduct::active()
            ->inStock()
            ->byCategory($product->agriculture_category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();
            
        // Get current user for pricing
        $user = auth()->user();
            
        return view('agriculture.products.show', compact('product', 'relatedProducts', 'user'));
    }
    
    public function search(Request $request)
    {
        $searchTerm = $request->get('q', '');
        
        if (empty($searchTerm)) {
            return redirect()->route('agriculture.products.index');
        }
        
        $products = AgricultureProduct::active()
            ->inStock()
            ->where(function($query) use ($searchTerm) {
                $query->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%")
                      ->orWhere('brand', 'like', "%{$searchTerm}%")
                      ->orWhere('model', 'like', "%{$searchTerm}%");
            })
            ->paginate(12);
            
        return view('agriculture.products.search', compact('products', 'searchTerm'));
    }
}