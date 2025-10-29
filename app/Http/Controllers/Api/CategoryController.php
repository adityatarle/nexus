<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureCategory;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index()
    {
        $categories = AgricultureCategory::active()
            ->ordered()
            ->withCount('products')
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                    'products_count' => $category->products_count,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get single category with products
     */
    public function show(AgricultureCategory $category, Request $request)
    {
        if (!$category->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found'
            ], 404);
        }

        $query = AgricultureProduct::active()
            ->inStock()
            ->byCategory($category->id)
            ->with('category');

        // Sorting
        $sortBy = $request->get('sort', 'name');
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
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);

        // Transform products
        $products->getCollection()->transform(function ($product) use ($request) {
            return $this->transformProduct($product, $request->user());
        });

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image ? asset('storage/' . $category->image) : null,
                ],
                'products' => $products,
            ]
        ]);
    }

    /**
     * Transform product for API response
     */
    private function transformProduct($product, $user = null)
    {
        $image = $product->primary_image 
            ?? $product->featured_image 
            ?? (is_array($product->gallery_images) && count($product->gallery_images) ? $product->gallery_images[0] : null)
            ?? (is_array($product->images) && count($product->images) ? $product->images[0] : null);
        
        $price = $product->getPriceForUser($user);
        
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $price,
            'original_price' => (float) $product->price,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'discount_percentage' => $product->discount_percentage,
            'stock_quantity' => $product->stock_quantity,
            'in_stock' => $product->in_stock,
            'image' => $image ? asset('storage/' . $image) : asset('assets/organic/images/product-thumb-1.png'),
            'category' => [
                'id' => $product->category->id ?? null,
                'name' => $product->category->name ?? null,
            ],
        ];
    }
}

