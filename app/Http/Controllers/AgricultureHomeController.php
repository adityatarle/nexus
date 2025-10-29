<?php

namespace App\Http\Controllers;

use App\Models\AgricultureCategory;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;

class AgricultureHomeController extends Controller
{
    public function index()
    {
        $categories = AgricultureCategory::active()->ordered()->take(8)->get();
        $featuredProducts = AgricultureProduct::active()->featured()->inStock()->take(8)->get();
        $latestProducts = AgricultureProduct::active()->inStock()->latest()->take(6)->get();
        
        return view('agriculture.home', compact('categories', 'featuredProducts', 'latestProducts'));
    }
}