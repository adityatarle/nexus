<?php

namespace App\Http\Controllers;

use App\Models\AgricultureCategory;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;

class AgricultureCategoryController extends Controller
{
    public function index()
    {
        $categories = AgricultureCategory::active()
            ->ordered()
            ->withCount('products')
            ->withCount(['products as in_stock_products_count' => function($query) {
                $query->where('is_active', true)
                      ->where('in_stock', true);
            }])
            ->get();
        return view('agriculture.categories.index', compact('categories'));
    }
    
    public function show(AgricultureCategory $category)
    {
        $products = AgricultureProduct::active()
            ->inStock()
            ->byCategory($category->id)
            ->paginate(12);
            
        return view('agriculture.categories.show', compact('category', 'products'));
    }
}