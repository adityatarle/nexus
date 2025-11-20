<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureProduct;
use App\Models\Cart;
use App\Models\CartItem;
use App\Helpers\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Get cart items
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $cartItems = [];
        $subtotal = 0;

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $items = $cart->items()->with('product')->get();

            foreach ($items as $item) {
                $product = $item->product;
                if (!$product || !$product->is_active) {
                    continue;
                }
                $price = (float) $item->price;
                $quantity = (int) $item->quantity;
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                // Use ImageHelper for consistent image URLs
                $imageUrl = ImageHelper::productImageUrl($product);
                $imageUrl = $this->ensureAbsoluteUrl($imageUrl);

                $cartItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => $price,
                    'quantity' => $quantity,
                    'subtotal' => (float) $itemSubtotal,
                    'image' => $imageUrl, // Full absolute URL
                    'in_stock' => $product->in_stock,
                    'stock_quantity' => $product->stock_quantity,
                ];
            }
        } else {
            // Fallback for guests (session-based)
            $cart = Session::get('cart', []);
            foreach ($cart as $item) {
                $product = AgricultureProduct::find($item['product_id']);
                if ($product && $product->is_active) {
                    $price = $item['price'] ?? $product->getPriceForUser(null);
                    $quantity = $item['quantity'] ?? 1;
                    $itemSubtotal = $price * $quantity;
                    $subtotal += $itemSubtotal;

                    // Use ImageHelper for consistent image URLs
                    $imageUrl = ImageHelper::productImageUrl($product);
                    $imageUrl = $this->ensureAbsoluteUrl($imageUrl);

                    $cartItems[] = [
                        'product_id' => $product->id,
                        'name' => $product->name,
                        'sku' => $product->sku,
                        'price' => (float) $price,
                        'quantity' => $quantity,
                        'subtotal' => (float) $itemSubtotal,
                        'image' => $imageUrl, // Full absolute URL
                        'in_stock' => $product->in_stock,
                        'stock_quantity' => $product->stock_quantity,
                    ];
                }
            }
        }

        $taxRate = 0.08; // 8% tax
        $shippingCost = 25;
        $taxAmount = $subtotal * $taxRate;
        $total = $subtotal + $taxAmount + $shippingCost;

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'subtotal' => (float) $subtotal,
                'tax_amount' => (float) $taxAmount,
                'shipping_amount' => (float) $shippingCost,
                'total' => (float) $total,
                'items_count' => count($cartItems),
            ]
        ]);
    }

    /**
     * Add product to cart
     */
    public function add(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_id' => 'required|exists:agriculture_products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product = AgricultureProduct::findOrFail($request->product_id);
        
        if (!$product->in_stock) {
            return response()->json([
                'success' => false,
                'message' => 'This product is currently out of stock.'
            ], 400);
        }
        
        $user = $request->user();
        $price = $product->getPriceForUser($user);

        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = CartItem::firstOrNew([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
            ]);
            $item->quantity = ($item->exists ? $item->quantity : 0) + $request->quantity;
            $item->price = $price;
            $item->save();
        } else {
            $cart = Session::get('cart', []);
            $productId = $request->product_id;

            $existingIndex = null;
            foreach ($cart as $index => $item) {
                if ($item['product_id'] == $productId) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                $cart[$existingIndex]['quantity'] += $request->quantity;
            } else {
                $cart[] = [
                    'product_id' => $productId,
                    'quantity' => $request->quantity,
                    'price' => $price
                ];
            }

            Session::put('cart', $cart);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!'
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'product_id' => 'required|exists:agriculture_products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $item = $cart->items()->where('product_id', $request->product_id)->first();
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found in cart'
                ], 404);
            }
            $item->quantity = $request->quantity;
            $item->save();
            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!'
            ]);
        } else {
            $cart = Session::get('cart', []);
            $productId = $request->product_id;

            foreach ($cart as $index => $item) {
                if ($item['product_id'] == $productId) {
                    $cart[$index]['quantity'] = $request->quantity;
                    Session::put('cart', $cart);

                    return response()->json([
                        'success' => true,
                        'message' => 'Cart updated successfully!'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }
    }

    /**
     * Remove product from cart
     */
    public function remove($productId)
    {
        $user = request()->user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $deleted = $cart->items()->where('product_id', $productId)->delete();
            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product removed from cart!'
                ]);
            }
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        } else {
            $cart = Session::get('cart', []);
            foreach ($cart as $index => $item) {
                if ($item['product_id'] == $productId) {
                    unset($cart[$index]);
                    Session::put('cart', array_values($cart));

                    return response()->json([
                        'success' => true,
                        'message' => 'Product removed from cart!'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }
    }

    /**
     * Clear cart
     */
    public function clear()
    {
        $user = request()->user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $cart->items()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!'
            ]);
        }

        Session::forget('cart');
        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully!'
        ]);
    }

    /**
     * Get cart items count
     */
    public function count()
    {
        $user = request()->user();
        if ($user) {
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $totalItems = (int) $cart->items()->sum('quantity');
        } else {
            $cart = Session::get('cart', []);
            $totalItems = array_sum(array_column($cart, 'quantity'));
        }

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $totalItems
            ]
        ]);
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

