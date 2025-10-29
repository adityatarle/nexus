<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Get cart items
     */
    public function index(Request $request)
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $subtotal = 0;
        
        foreach ($cart as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product && $product->is_active) {
                $price = $item['price'] ?? $product->getPriceForUser($request->user());
                $quantity = $item['quantity'] ?? 1;
                $itemSubtotal = $price * $quantity;
                $subtotal += $itemSubtotal;

                $image = $product->primary_image 
                    ?? $product->featured_image 
                    ?? (is_array($product->gallery_images) && count($product->gallery_images) ? $product->gallery_images[0] : null);

                $cartItems[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'price' => (float) $price,
                    'quantity' => $quantity,
                    'subtotal' => (float) $itemSubtotal,
                    'image' => $image ? asset('storage/' . $image) : asset('assets/organic/images/product-thumb-1.png'),
                    'in_stock' => $product->in_stock,
                    'stock_quantity' => $product->stock_quantity,
                ];
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
        
        $price = $product->getPriceForUser($request->user());
        $cart = Session::get('cart', []);
        $productId = $request->product_id;
        
        // Check if product already exists in cart
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

    /**
     * Remove product from cart
     */
    public function remove($productId)
    {
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

    /**
     * Clear cart
     */
    public function clear()
    {
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
        $cart = Session::get('cart', []);
        $totalItems = array_sum(array_column($cart, 'quantity'));
        
        return response()->json([
            'success' => true,
            'data' => [
                'count' => $totalItems
            ]
        ]);
    }
}

