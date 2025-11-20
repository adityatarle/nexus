<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use App\Models\AgricultureProduct;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get user wishlist
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $items = Wishlist::where('user_id', $user->id)
            ->with('product.category')
            ->latest()
            ->get()
            ->map(function ($wishlist) use ($user) {
                return $this->transformProduct($wishlist->product, $user);
            });

        return response()->json([
            'success' => true,
            'data' => $items
        ]);
    }

    /**
     * Add product to wishlist
     */
    public function add(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_id' => 'required|exists:agriculture_products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $wishlist = Wishlist::firstOrCreate([
            'user_id' => $request->user()->id,
            'agriculture_product_id' => $request->product_id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Added to wishlist'
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function remove($productId, Request $request)
    {
        $deleted = Wishlist::where('user_id', $request->user()->id)
            ->where('agriculture_product_id', $productId)
            ->delete();

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Removed from wishlist'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in wishlist'
        ], 404);
    }

    /**
     * Clear wishlist
     */
    public function clear(Request $request)
    {
        Wishlist::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared'
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check($productId, Request $request)
    {
        $exists = Wishlist::where('user_id', $request->user()->id)
            ->where('agriculture_product_id', $productId)
            ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'is_in_wishlist' => $exists
            ]
        ]);
    }

    /**
     * Transform product for API response
     */
    private function transformProduct($product, $user = null)
    {
        $price = $product->getPriceForUser($user);
        
        // Use ImageHelper for consistent image URLs with fallback mechanisms
        $imageUrl = ImageHelper::productImageUrl($product);
        $imageUrl = $this->ensureAbsoluteUrl($imageUrl);
        
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
            'image' => $imageUrl, // Full absolute URL with cache buster
            'category' => [
                'id' => $product->category->id ?? null,
                'name' => $product->category->name ?? null,
            ],
        ];
    }
    
    /**
     * Ensure URL is absolute (full URL) for mobile apps
     * 
     * @param string $url
     * @return string
     */
    private function ensureAbsoluteUrl(string $url): string
    {
        // If already absolute URL (starts with http:// or https://), return as is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        // If relative URL, convert to absolute using APP_URL
        $baseUrl = config('app.url');
        
        // Remove trailing slash from base URL
        $baseUrl = rtrim($baseUrl, '/');
        
        // Remove leading slash from relative URL
        $url = ltrim($url, '/');
        
        // Combine to create absolute URL
        return $baseUrl . '/' . $url;
    }
}

