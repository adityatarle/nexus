<?php

namespace App\Http\Controllers;

use App\Models\AgricultureOrder;
use App\Models\AgricultureOrderItem;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AgricultureCheckoutController extends Controller
{
    public function index()
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('agriculture.cart.index')
                ->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        return view('agriculture.checkout');
    }

    public function process(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'billing_address' => 'required|string',
            'shipping_address' => 'nullable|string',
            'payment_method' => 'required|in:credit_card,bank_transfer,cash_on_delivery',
            'notes' => 'nullable|string',
            'terms' => 'required|accepted'
        ]);

        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('agriculture.cart.index')
                ->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        // Calculate totals
        $subtotal = 0;
        $taxRate = 0.08; // 8% tax
        $shippingCost = 25;

        foreach ($cart as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                $subtotal += $product->current_price * $item['quantity'];
            }
        }

        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount + $shippingCost;

        // Generate unique order number
        $orderNumber = 'AGR-' . strtoupper(Str::random(8));

        // Create order
        $order = AgricultureOrder::create([
            'order_number' => $orderNumber,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'billing_address' => $request->billing_address,
            'shipping_address' => $request->shipping_address ?: $request->billing_address,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_amount' => $shippingCost,
            'total_amount' => $totalAmount,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'notes' => $request->notes
        ]);

        // Create order items
        foreach ($cart as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                AgricultureOrderItem::create([
                    'agriculture_order_id' => $order->id,
                    'agriculture_product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'price' => $product->current_price,
                    'total' => $product->current_price * $item['quantity']
                ]);

                // Update product stock
                if ($product->manage_stock) {
                    $product->decrement('stock_quantity', $item['quantity']);
                    
                    // Mark as out of stock if quantity reaches 0
                    if ($product->stock_quantity <= 0) {
                        $product->update(['in_stock' => false]);
                    }
                }
            }
        }

        // Clear cart
        session()->forget('cart');
        session()->forget('cart_total');

        // Redirect to order confirmation
        return redirect()->route('agriculture.checkout.success', $order->order_number)
            ->with('success', 'Order placed successfully!');
    }

    public function success($orderNumber)
    {
        $order = AgricultureOrder::where('order_number', $orderNumber)->firstOrFail();
        $order->load('items.product');

        return view('agriculture.checkout-success', compact('order'));
    }
}
