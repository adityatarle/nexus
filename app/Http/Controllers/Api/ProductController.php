<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get list of products with filters
     */
    public function index(Request $request)
    {
        $query = AgricultureProduct::active()->inStock()->with('category');
        
        // Filter by category
        if ($request->filled('category_id')) {
            $query->byCategory($request->category_id);
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
        
        $query = AgricultureProduct::active()->inStock()->with('category')
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
     */
    public function show(AgricultureProduct $product, Request $request)
    {
        if (!$product->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $product->load('category');
        
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
        $query = AgricultureProduct::active()->inStock()->featured()->with('category');
        
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
        // Get the appropriate image
        $image = $product->primary_image 
            ?? $product->featured_image 
            ?? (is_array($product->gallery_images) && count($product->gallery_images) ? $product->gallery_images[0] : null)
            ?? (is_array($product->images) && count($product->images) ? $product->images[0] : null);
        
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
            'image' => $image ? asset('storage/' . $image) : asset('assets/organic/images/product-thumb-1.png'),
            'images' => array_map(function($img) {
                return asset('storage/' . $img);
            }, $galleryImages),
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
            'created_at' => $product->created_at->toISOString(),
            'updated_at' => $product->updated_at->toISOString(),
        ];
    }
}

