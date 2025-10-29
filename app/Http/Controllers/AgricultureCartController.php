<?php

namespace App\Http\Controllers;

use App\Models\AgricultureProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AgricultureCartController extends Controller
{
    public function index()
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;
        
        foreach ($cart as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                $item['product'] = $product;
                // Use the price stored in cart (which is based on user role at time of adding)
                $price = $item['price'] ?? $product->getPriceForUser(auth()->user());
                $item['subtotal'] = $price * $item['quantity'];
                $total += $item['subtotal'];
                $cartItems[] = $item;
            }
        }
        
        return view('agriculture.cart.index', compact('cartItems', 'total'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:agriculture_products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $product = AgricultureProduct::findOrFail($request->product_id);
        
        if (!$product->in_stock) {
            return redirect()->back()->with('error', 'This product is currently out of stock.');
        }
        
        // Get price based on user role
        $price = $product->getPriceForUser(auth()->user());
        
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
        
        return redirect()->back()->with('success', 'Product added to cart successfully!');
    }
    
    public function update(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:agriculture_products,id',
            'quantity' => 'required|integer|min:1'
        ]);
        
        $cart = Session::get('cart', []);
        $productId = $request->product_id;
        
        foreach ($cart as $index => $item) {
            if ($item['product_id'] == $productId) {
                $cart[$index]['quantity'] = $request->quantity;
                Session::put('cart', $cart);
                break;
            }
        }
        
        return redirect()->route('agriculture.cart.index')->with('success', 'Cart updated successfully!');
    }
    
    public function remove(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:agriculture_products,id'
        ]);
        
        $cart = Session::get('cart', []);
        $productId = $request->product_id;
        
        foreach ($cart as $index => $item) {
            if ($item['product_id'] == $productId) {
                unset($cart[$index]);
                Session::put('cart', array_values($cart)); // Re-index array
                break;
            }
        }
        
        return redirect()->route('agriculture.cart.index')->with('success', 'Product removed from cart!');
    }
    
    public function clear()
    {
        Session::forget('cart');
        return redirect()->route('agriculture.cart.index')->with('success', 'Cart cleared successfully!');
    }
    
    public function getCartCount()
    {
        $cart = Session::get('cart', []);
        return array_sum(array_column($cart, 'quantity'));
    }
}