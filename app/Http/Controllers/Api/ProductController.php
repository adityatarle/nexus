<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureProduct;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get list of products with filters
     */
    public function index(Request $request)
    {
        $query = AgricultureProduct::active()->inStock()->with(['category', 'subcategory']);
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
        }
        
        // Filter by subcategory
        if ($request->filled('subcategory_id')) {
            $query->bySubcategory($request->subcategory_id);
        }
        
        // Filter by brand
        if ($request->filled('brand')) {
            $query->byBrand($request->brand);
        }
        
        // Filter by power source
        if ($request->filled('power_source')) {
            $query->byPowerSource($request->power_source);
        }
        
        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Featured products filter
        if ($request->boolean('featured')) {
            $query->featured();
        }
        
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
            case 'name':
            default:
                $query->orderBy('name', 'asc');
                break;
        }
        
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);
        
        // Get authenticated user (optional - for dealer pricing)
        $user = $this->getAuthenticatedUser($request);
        
        // Transform products with image URLs
        $products->getCollection()->transform(function ($product) use ($user) {
            return $this->transformProduct($product, $user);
        });
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    
    /**
     * Get authenticated user from token (optional authentication)
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
     * Search products
     */
    public function search(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'q' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $searchTerm = $request->q;
        
        $query = AgricultureProduct::active()->inStock()->with(['category', 'subcategory'])
            ->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('brand', 'like', "%{$searchTerm}%")
                  ->orWhere('model', 'like', "%{$searchTerm}%");
            });
        
        $perPage = $request->get('per_page', 15);
        $products = $query->paginate($perPage);
        
        // Get authenticated user (optional - for dealer pricing)
        $user = $this->getAuthenticatedUser($request);
        
        // Transform products
        $products->getCollection()->transform(function ($product) use ($user) {
            return $this->transformProduct($product, $user);
        });
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get single product details
     * Supports both ID and slug for route model binding
     */
    public function show(Request $request, $product)
    {
        // Try to find product by ID first (if numeric), then by slug
        if (is_numeric($product)) {
            $productModel = AgricultureProduct::with(['category', 'subcategory'])
                ->where('id', $product)
                ->first();
        } else {
            $productModel = AgricultureProduct::with(['category', 'subcategory'])
                ->where('slug', $product)
                ->first();
        }
        
        if (!$productModel) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        if (!$productModel->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }
        
        $product = $productModel;

        $product->load(['category', 'subcategory']);
        
        // Get authenticated user (optional - for dealer pricing)
        $user = $this->getAuthenticatedUser($request);
        
        // Get related products
        $relatedProducts = AgricultureProduct::active()
            ->inStock()
            ->byCategory($product->agriculture_category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get()
            ->map(function ($related) use ($user) {
                return $this->transformProduct($related, $user);
            });
        
        $transformedProduct = $this->transformProduct($product, $user);
        $transformedProduct['related_products'] = $relatedProducts;
        
        return response()->json([
            'success' => true,
            'data' => $transformedProduct
        ]);
    }

    /**
     * Get featured products
     */
    public function featured(Request $request)
    {
        $query = AgricultureProduct::active()->inStock()->featured()->with(['category', 'subcategory']);
        
        $limit = $request->get('limit', 10);
        $products = $query->limit($limit)->get();
        
        // Get authenticated user (optional - for dealer pricing)
        $user = $this->getAuthenticatedUser($request);
        
        // Transform products
        $products = $products->map(function ($product) use ($user) {
            return $this->transformProduct($product, $user);
        });
        
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Transform product for API response
     */
    private function transformProduct($product, $user = null)
    {
        // Get price based on user role
        $price = $product->getPriceForUser($user);
        $originalPrice = $user && $user->canAccessDealerPricing() 
            ? ($product->dealer_price ?? $product->price) 
            : $product->price;
        
        // Ensure gallery images is an array (handle JSON string from DB)
        $galleryImages = $product->gallery_images ?? [];
        if (is_string($galleryImages)) {
            $decodedGallery = json_decode($galleryImages, true);
            $galleryImages = is_array($decodedGallery) ? $decodedGallery : [];
        }

        // Use ImageHelper for consistent image URL generation with full URLs
        // This ensures mobile apps get absolute URLs with fallback mechanisms
        $mainImageUrl = ImageHelper::productImageUrl($product);
        
        // Convert relative URLs to absolute URLs for mobile apps
        $mainImageUrl = $this->ensureAbsoluteUrl($mainImageUrl);
        
        // Transform gallery images with full URLs
        $galleryImageUrls = ImageHelper::galleryImageUrls($galleryImages);
        $galleryImageUrls = array_map(function($url) {
            return $this->ensureAbsoluteUrl($url);
        }, $galleryImageUrls);

        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'sku' => $product->sku,
            'price' => (float) $price,
            'original_price' => (float) $originalPrice,
            'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
            'dealer_price' => $user && $user->canAccessDealerPricing() ? (float) ($product->dealer_price ?? 0) : null,
            'dealer_sale_price' => $user && $user->canAccessDealerPricing() ? ($product->dealer_sale_price ? (float) $product->dealer_sale_price : null) : null,
            'discount_percentage' => $product->discount_percentage,
            'stock_quantity' => $product->stock_quantity,
            'in_stock' => $product->in_stock,
            'is_featured' => $product->is_featured,
            'image' => $mainImageUrl, // Full absolute URL with cache buster
            'images' => $galleryImageUrls, // Array of full absolute URLs
            'brand' => $product->brand,
            'model' => $product->model,
            'power_source' => $product->power_source,
            'warranty' => $product->warranty,
            'weight' => $product->weight,
            'dimensions' => $product->dimensions,
            'category' => [
                'id' => $product->category->id ?? null,
                'name' => $product->category->name ?? null,
                'slug' => $product->category->slug ?? null,
            ],
            'subcategory' => $product->subcategory ? [
                'id' => $product->subcategory->id,
                'name' => $product->subcategory->name,
                'slug' => $product->subcategory->slug,
            ] : null,
            'created_at' => $product->created_at->toISOString(),
            'updated_at' => $product->updated_at->toISOString(),
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

