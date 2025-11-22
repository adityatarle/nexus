<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureOrder;
use App\Models\AgricultureOrderItem;
use App\Models\AgricultureProduct;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get user orders
     * 
     * Query parameters:
     * - status: Filter by order status (inquiry, pending, processing, shipped, delivered, cancelled)
     * - payment_status: Filter by payment status (not_required, pending, paid, failed, refunded)
     * - per_page: Number of items per page (default: 15)
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = AgricultureOrder::where('user_id', $user->id)
            ->with('items.product')
            ->latest();

        // Filter by order status
        if ($request->filled('status')) {
            $validStatuses = ['inquiry', 'pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (in_array($request->status, $validStatuses)) {
                $query->where('order_status', $request->status);
            }
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $validPaymentStatuses = ['not_required', 'pending', 'paid', 'failed', 'refunded'];
            if (in_array($request->payment_status, $validPaymentStatuses)) {
                $query->where('payment_status', $request->payment_status);
            }
        }

        $perPage = $request->get('per_page', 15);
        $orders = $query->paginate($perPage);

        $orders->getCollection()->transform(function ($order) {
            return $this->transformOrder($order);
        });

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }

    /**
     * Get single order
     */
    public function show($orderNumber, Request $request)
    {
        $user = $request->user();
        
        $order = AgricultureOrder::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with('items.product')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformOrder($order)
        ]);
    }

    /**
     * Create new order (as inquiry)
     * 
     * This endpoint creates an order inquiry. No payment is required.
     * The admin will contact the customer to confirm the order details.
     * 
     * Required fields:
     * - customer_name: Customer's full name
     * - customer_email: Customer's email address
     * - customer_phone: Customer's phone number (required for follow-up)
     * - billing_address: Billing address
     * 
     * Optional fields:
     * - shipping_address: Shipping address (defaults to billing address if not provided)
     * - notes: Additional notes or special instructions
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'billing_address' => 'required|string',
            'shipping_address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $cartItems = [];
        
        // Get cart items - check database for authenticated users, session for guests
        if ($user) {
            // For authenticated users, get cart from database
            $cart = Cart::where('user_id', $user->id)->first();
            
            if (!$cart || $cart->items()->count() === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty. Please add items before checkout.'
                ], 400);
            }
            
            // Convert database cart items to array format
            foreach ($cart->items()->with('product')->get() as $item) {
                $product = $item->product;
                if ($product && $product->is_active) {
                    $cartItems[] = [
                        'product_id' => $product->id,
                        'quantity' => $item->quantity,
                        'price' => $item->price
                    ];
                }
            }
        } else {
            // For guests, get cart from session
            $cart = Session::get('cart', []);
            
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your cart is empty. Please add items before checkout.'
                ], 400);
            }
            
            $cartItems = $cart;
        }
        
        if (empty($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Please add items before checkout.'
            ], 400);
        }

        // Calculate totals
        $subtotal = 0;
        $taxRate = 0.08; // 8% tax
        $shippingCost = 25;

        foreach ($cartItems as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                $price = $item['price'] ?? $product->getPriceForUser($user);
                $subtotal += $price * $item['quantity'];
            }
        }

        // Round subtotal to 2 decimal places
        $subtotal = round($subtotal, 2);
        $taxAmount = round($subtotal * $taxRate, 2);
        $totalAmount = round($subtotal + $taxAmount + $shippingCost, 2);

        // Generate unique order number starting from GL-1001
        $orderNumber = AgricultureOrder::generateOrderNumber();

        // Create order as inquiry (no payment required)
        $order = AgricultureOrder::create([
            'order_number' => $orderNumber,
            'user_id' => $user ? $user->id : null,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'billing_address' => ['address' => $request->billing_address], // Store as array for JSON column
            'shipping_address' => $request->shipping_address ? ['address' => $request->shipping_address] : ['address' => $request->billing_address],
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingCost,
            'total_amount' => $totalAmount,
            'payment_method' => 'inquiry', // Mark as inquiry (no payment)
            'payment_status' => 'not_required', // No payment needed
            'order_status' => 'inquiry', // Status: inquiry (admin will follow up)
            'notes' => $request->notes
        ]);

        // Create order items and recalculate subtotal from items to ensure accuracy
        $calculatedSubtotal = 0;
        foreach ($cartItems as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                // Get base price (without offers)
                $originalPrice = $item['price'] ?? $product->getPriceForUser($user);
                
                // Calculate offer discount for this product
                $offerResult = $this->calculateOfferForProduct($product, $originalPrice, $item['quantity'], $user);
                
                // Use discounted price if offer applies, otherwise use original price
                $finalPrice = $offerResult['final_price'];
                $discountAmount = $offerResult['discount_amount'];
                $bestOffer = $offerResult['offer'];
                
                $itemTotal = round($finalPrice * $item['quantity'], 2);
                $calculatedSubtotal += $itemTotal;
                
                // Prepare offer details for storage
                $offerDetails = null;
                if ($bestOffer) {
                    $offerDetails = [
                        'id' => $bestOffer->id,
                        'title' => $bestOffer->title,
                        'discount_type' => $bestOffer->discount_type,
                        'discount_value' => (float) $bestOffer->discount_value,
                    ];
                }
                
                AgricultureOrderItem::create([
                    'agriculture_order_id' => $order->id,
                    'agriculture_product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'original_price' => round($originalPrice, 2),
                    'price' => round($finalPrice, 2), // Final price after discount
                    'discount_amount' => round($discountAmount, 2),
                    'offer_id' => $bestOffer ? $bestOffer->id : null,
                    'offer_details' => $offerDetails,
                    'total' => $itemTotal
                ]);

                // Don't reduce stock - this is just an inquiry, not a confirmed order
                // Stock will be managed when admin confirms the order
            }
        }
        
        // Recalculate tax and total based on actual order items subtotal
        $calculatedSubtotal = round($calculatedSubtotal, 2);
        $calculatedTaxAmount = round($calculatedSubtotal * $taxRate, 2);
        $calculatedTotalAmount = round($calculatedSubtotal + $calculatedTaxAmount + $shippingCost, 2);
        
        // Update order with recalculated values to ensure accuracy
        $order->update([
            'subtotal' => $calculatedSubtotal,
            'tax_amount' => $calculatedTaxAmount,
            'total_amount' => $calculatedTotalAmount
        ]);

        // Clear cart - database for authenticated users, session for guests
        if ($user) {
            $cart = Cart::where('user_id', $user->id)->first();
            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        } else {
            Session::forget('cart');
        }

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Inquiry received! We will contact you shortly to confirm your order.',
            'data' => $this->transformOrder($order)
        ], 201);
    }

    /**
     * Download invoice (returns invoice data)
     */
    public function invoice($orderNumber, Request $request)
    {
        $user = $request->user();
        
        $order = AgricultureOrder::where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->with('items.product')
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->transformOrder($order)
        ]);
    }

    /**
     * Transform order for API response
     * Includes inquiry status support
     */
    private function transformOrder($order)
    {
        $isInquiry = $order->order_status === 'inquiry';
        
        return [
            'order_number' => $order->order_number,
            'customer_name' => $order->customer_name,
            'customer_email' => $order->customer_email,
            'customer_phone' => $order->customer_phone,
            'billing_address' => $order->billing_address,
            'shipping_address' => $order->shipping_address,
            'subtotal' => (float) $order->subtotal,
            'tax_amount' => (float) $order->tax_amount,
            'shipping_amount' => (float) $order->shipping_amount,
            'total_amount' => (float) $order->total_amount,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'order_status' => $order->order_status,
            'is_inquiry' => $isInquiry, // Helper flag for mobile apps
            'notes' => $order->notes,
            'items' => $order->items->map(function ($item) use ($order) {
                // Use ImageHelper for consistent image URLs
                $imageUrl = null;
                if ($item->product) {
                    $imageUrl = \App\Helpers\ImageHelper::productImageUrl($item->product);
                    $imageUrl = $this->ensureAbsoluteUrl($imageUrl);
                }
                
                return [
                    'product_id' => $item->agriculture_product_id,
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'original_price' => $item->original_price ? (float) $item->original_price : (float) $item->price,
                    'price' => (float) $item->price, // Final price after discount
                    'discount_amount' => $item->discount_amount ? (float) $item->discount_amount : 0,
                    'total' => (float) $item->total,
                    'image' => $imageUrl, // Full absolute URL
                    'offer' => $item->offer_details ? [
                        'id' => $item->offer_details['id'] ?? null,
                        'title' => $item->offer_details['title'] ?? null,
                        'discount_type' => $item->offer_details['discount_type'] ?? null,
                        'discount_value' => $item->offer_details['discount_value'] ?? null,
                    ] : null,
                ];
            }),
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
    
    /**
     * Calculate the best offer for a product
     * 
     * @param AgricultureProduct $product
     * @param float $originalPrice
     * @param int $quantity
     * @param mixed $user
     * @return array
     */
    private function calculateOfferForProduct($product, $originalPrice, $quantity, $user = null)
    {
        $amount = $originalPrice * $quantity;
        
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

        // Calculate final price per unit
        $totalDiscount = $maxDiscount;
        $finalAmount = $amount - $totalDiscount;
        $finalPricePerUnit = $quantity > 0 ? ($finalAmount / $quantity) : $originalPrice;

        return [
            'original_price' => $originalPrice,
            'final_price' => round($finalPricePerUnit, 2),
            'discount_amount' => round($totalDiscount, 2),
            'offer' => $bestOffer
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

