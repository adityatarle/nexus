<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;

class OfferController extends Controller
{
    /**
     * Get all active offers
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Offer::valid()->with(['product', 'category', 'subcategory']);

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by featured
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Filter by user type
        if ($user && $user->isDealer()) {
            $query->forDealers();
        } else {
            $query->forCustomers();
        }

        // Order by priority and sort order
        $offers = $query->orderBy('priority', 'desc')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Transform offers with image URLs
        $offers = $offers->map(function ($offer) use ($user) {
            return $this->transformOffer($offer, $user);
        });

        return response()->json([
            'success' => true,
            'data' => $offers,
            'count' => $offers->count()
        ]);
    }

    /**
     * Get a single offer
     */
    public function show(Offer $offer, Request $request)
    {
        $user = $request->user();

        // Check if offer is valid and accessible
        if (!$offer->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Offer is not available'
            ], 404);
        }

        if (!$offer->canBeUsedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Offer is not available for your account type'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformOffer($offer, $user)
        ]);
    }

    /**
     * Get offers applicable to a specific product
     */
    public function forProduct($productId, Request $request)
    {
        $user = $request->user();

        $offers = Offer::valid()
            ->where(function($query) use ($productId) {
                $query->where('offer_type', 'general')
                    ->orWhere(function($q) use ($productId) {
                        $q->where('offer_type', 'product')
                          ->where('product_id', $productId);
                    })
                    ->orWhere(function($q) use ($productId) {
                        $product = \App\Models\AgricultureProduct::find($productId);
                        if ($product) {
                            if ($product->agriculture_category_id) {
                                $q->where('offer_type', 'category')
                                  ->where('category_id', $product->agriculture_category_id);
                            }
                            if ($product->agriculture_subcategory_id) {
                                $q->orWhere(function($subQ) use ($product) {
                                    $subQ->where('offer_type', 'subcategory')
                                         ->where('subcategory_id', $product->agriculture_subcategory_id);
                                });
                            }
                        }
                    });
            });

        // Filter by user type
        if ($user && $user->isDealer()) {
            $offers->forDealers();
        } else {
            $offers->forCustomers();
        }

        $offers = $offers->orderBy('priority', 'desc')
            ->orderBy('sort_order', 'asc')
            ->get();

        // Transform offers
        $offers = $offers->map(function ($offer) use ($user) {
            return $this->transformOffer($offer, $user);
        });

        return response()->json([
            'success' => true,
            'data' => $offers,
            'count' => $offers->count()
        ]);
    }

    /**
     * Transform offer for API response
     */
    private function transformOffer(Offer $offer, $user = null)
    {
        $data = [
            'id' => $offer->id,
            'title' => $offer->title,
            'slug' => $offer->slug,
            'description' => $offer->description,
            'banner_image' => $offer->banner_image ? ImageHelper::imageUrl($offer->banner_image) : null,
            'offer_type' => $offer->offer_type,
            'discount_type' => $offer->discount_type,
            'discount_value' => (float) $offer->discount_value,
            'min_purchase_amount' => $offer->min_purchase_amount ? (float) $offer->min_purchase_amount : null,
            'min_quantity' => $offer->min_quantity,
            'start_date' => $offer->start_date->toIso8601String(),
            'end_date' => $offer->end_date->toIso8601String(),
            'max_uses' => $offer->max_uses,
            'max_uses_per_user' => $offer->max_uses_per_user,
            'used_count' => $offer->used_count,
            'is_featured' => $offer->is_featured,
            'terms_conditions' => $offer->terms_conditions,
            'for_customers' => $offer->for_customers,
            'for_dealers' => $offer->for_dealers,
            'days_remaining' => max(0, now()->diffInDays($offer->end_date, false)),
            'is_valid' => $offer->isValid(),
        ];

        // Add related entity information
        if ($offer->offer_type === 'product' && $offer->product) {
            $data['product'] = [
                'id' => $offer->product->id,
                'name' => $offer->product->name,
                'slug' => $offer->product->slug,
                'image' => ImageHelper::productImageUrl($offer->product),
                'price' => (float) $offer->product->price,
                'current_price' => (float) $offer->product->current_price,
            ];
        }

        if ($offer->offer_type === 'category' && $offer->category) {
            $data['category'] = [
                'id' => $offer->category->id,
                'name' => $offer->category->name,
                'slug' => $offer->category->slug,
                'image' => $offer->category->image ? ImageHelper::imageUrl($offer->category->image) : null,
            ];
        }

        if ($offer->offer_type === 'subcategory' && $offer->subcategory) {
            $data['subcategory'] = [
                'id' => $offer->subcategory->id,
                'name' => $offer->subcategory->name,
                'slug' => $offer->subcategory->slug,
                'image' => $offer->subcategory->image ? ImageHelper::imageUrl($offer->subcategory->image) : null,
            ];
        }

        return $data;
    }

    /**
     * Calculate discount for a product using applicable offers
     */
    public function calculateDiscount(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:agriculture_products,id',
            'quantity' => 'nullable|integer|min:1',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $user = $request->user();
        $product = \App\Models\AgricultureProduct::find($request->product_id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $quantity = $request->quantity ?? 1;
        $amount = $request->amount ?? ($product->price * $quantity);

        // Get applicable offers
        $offers = Offer::valid()
            ->where(function($query) use ($product) {
                $query->where('offer_type', 'general')
                    ->orWhere(function($q) use ($product) {
                        $q->where('offer_type', 'product')
                          ->where('product_id', $product->id);
                    })
                    ->orWhere(function($q) use ($product) {
                        if ($product->agriculture_category_id) {
                            $q->where('offer_type', 'category')
                              ->where('category_id', $product->agriculture_category_id);
                        }
                        if ($product->agriculture_subcategory_id) {
                            $q->orWhere(function($subQ) use ($product) {
                                $subQ->where('offer_type', 'subcategory')
                                     ->where('subcategory_id', $product->agriculture_subcategory_id);
                            });
                        }
                    });
            });

        // Filter by user type
        if ($user && $user->isDealer()) {
            $offers->forDealers();
        } else {
            $offers->forCustomers();
        }

        $offers = $offers->orderBy('priority', 'desc')->get();

        $bestOffer = null;
        $maxDiscount = 0;

        foreach ($offers as $offer) {
            // Check minimum requirements
            if ($offer->min_purchase_amount && $amount < $offer->min_purchase_amount) {
                continue;
            }

            if ($offer->min_quantity && $quantity < $offer->min_quantity) {
                continue;
            }

            if (!$offer->canBeUsedBy($user)) {
                continue;
            }

            $discount = $offer->calculateDiscount($amount);
            
            if ($discount > $maxDiscount) {
                $maxDiscount = $discount;
                $bestOffer = $offer;
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'original_amount' => (float) $amount,
                'discount_amount' => (float) $maxDiscount,
                'final_amount' => (float) ($amount - $maxDiscount),
                'discount_percentage' => $amount > 0 ? round(($maxDiscount / $amount) * 100, 2) : 0,
                'best_offer' => $bestOffer ? $this->transformOffer($bestOffer, $user) : null,
            ]
        ]);
    }
}
