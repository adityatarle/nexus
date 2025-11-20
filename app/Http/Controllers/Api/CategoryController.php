<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureCategory;
use App\Models\AgricultureProduct;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all categories
     */
    public function index(Request $request)
    {
        $categories = AgricultureCategory::active()
            ->ordered()
            ->withCount('products')
            ->with(['subcategories' => function($query) {
                $query->active()->ordered();
            }])
            ->get()
            ->map(function ($category) {
                $imageUrl = $category->image 
                    ? ImageHelper::imageUrl($category->image)
                    : null;
                
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $imageUrl ? $this->ensureAbsoluteUrl($imageUrl) : null,
                    'products_count' => $category->products_count,
                    'subcategories' => $category->subcategories->map(function ($subcategory) {
                        $subImageUrl = $subcategory->image 
                            ? ImageHelper::imageUrl($subcategory->image)
                            : null;
                        
                        return [
                            'id' => $subcategory->id,
                            'name' => $subcategory->name,
                            'slug' => $subcategory->slug,
                            'description' => $subcategory->description,
                            'image' => $subImageUrl ? $this->ensureAbsoluteUrl($subImageUrl) : null,
                        ];
                    }),
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
            ->with(['category', 'subcategory']);

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

        // Get authenticated user (optional - for dealer pricing)
        // This allows dealers to see dealer prices even on public routes
        $user = $this->getAuthenticatedUser($request);

        // Transform products
        $products->getCollection()->transform(function ($product) use ($user) {
            return $this->transformProduct($product, $user);
        });

        return response()->json([
            'success' => true,
            'data' => [
                'category' => [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'image' => $category->image 
                        ? $this->ensureAbsoluteUrl(ImageHelper::imageUrl($category->image))
                        : null,
                ],
                'products' => $products,
            ]
        ]);
    }

    /**
     * Transform product for API response
     * Includes dealer pricing for authenticated dealers
     */
    private function transformProduct($product, $user = null)
    {
        // Get price based on user role (dealer gets dealer price, customer gets retail price)
        $price = $product->getPriceForUser($user);
        
        // Calculate original price based on user role
        $originalPrice = $user && $user->canAccessDealerPricing() 
            ? ($product->dealer_price ?? $product->price) 
            : $product->price;
        
        // Use ImageHelper for consistent image URLs
        $imageUrl = ImageHelper::productImageUrl($product);
        $imageUrl = $this->ensureAbsoluteUrl($imageUrl);
        
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'price' => (float) $price, // Current price (dealer or retail based on user)
            'original_price' => (float) $originalPrice, // Base price for user's role
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null, // Retail sale price
            'dealer_price' => $user && $user->canAccessDealerPricing() ? (float) ($product->dealer_price ?? 0) : null, // Dealer base price
            'dealer_sale_price' => $user && $user->canAccessDealerPricing() ? ($product->dealer_sale_price ? (float) $product->dealer_sale_price : null) : null, // Dealer sale price
            'discount_percentage' => $product->discount_percentage,
            'stock_quantity' => $product->stock_quantity,
            'in_stock' => $product->in_stock,
            'image' => $imageUrl, // Full absolute URL
            'category' => [
                'id' => $product->category->id ?? null,
                'name' => $product->category->name ?? null,
            ],
            'subcategory' => $product->subcategory ? [
                'id' => $product->subcategory->id,
                'name' => $product->subcategory->name,
            ] : null,
        ];
    }
    
    /**
     * Get authenticated user from token (optional authentication)
     * This allows dealers to see dealer prices even on public routes
     * 
     * @param Request $request
     * @return \App\Models\User|null
     */
    private function getAuthenticatedUser(Request $request)
    {
        // Try to get user from request (if middleware authenticated)
        if ($request->user()) {
            return $request->user();
        }
        
        // If not authenticated, try to authenticate from token in header
        $token = $request->bearerToken();
        if ($token) {
            try {
                $personalAccessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                if ($personalAccessToken) {
                    return $personalAccessToken->tokenable;
                }
            } catch (\Exception $e) {
                // Token invalid or expired
                return null;
            }
        }
        
        return null;
    }
    
    /**
     * Ensure URL is absolute (full URL) for mobile apps
     * 
     * @param string $url
     * @return string
     */
    private function ensureAbsoluteUrl(string $url): string
    {
        // If already absolute URL, return as is
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) {
            return $url;
        }
        
        // Convert relative URL to absolute using APP_URL
        $baseUrl = rtrim(config('app.url'), '/');
        $url = ltrim($url, '/');
        
        return $baseUrl . '/' . $url;
    }
}

