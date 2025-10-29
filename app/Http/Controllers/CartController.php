<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        $cartTotal = session('cart_total', 0);
        
        return view('cart.index', compact('cart', 'cartTotal'));
    }
    
    public function add(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;
        
        $product = Product::findOrFail($productId);
        
        $cart = session('cart', []);
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->current_price,
                'image' => $product->featured_image,
                'quantity' => $quantity,
            ];
        }
        
        session(['cart' => $cart]);
        $this->updateCartTotal();
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'cart_count' => count($cart),
                'cart_total' => session('cart_total', 0)
            ]);
        }
        
        return redirect()->back()->with('success', 'Product added to cart!');
    }
    
    public function update(Request $request)
    {
        $productId = $request->product_id;
        $quantity = $request->quantity;
        
        $cart = session('cart', []);
        
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
        }
        
        session(['cart' => $cart]);
        $this->updateCartTotal();
        
        return redirect()->back()->with('success', 'Cart updated!');
    }
    
    public function remove(Request $request)
    {
        $productId = $request->product_id;
        
        $cart = session('cart', []);
        unset($cart[$productId]);
        
        session(['cart' => $cart]);
        $this->updateCartTotal();
        
        return redirect()->back()->with('success', 'Product removed from cart!');
    }
    
    public function clear()
    {
        session()->forget(['cart', 'cart_total']);
        
        return redirect()->back()->with('success', 'Cart cleared!');
    }
    
    private function updateCartTotal()
    {
        $cart = session('cart', []);
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        session(['cart_total' => $total]);
    }
}