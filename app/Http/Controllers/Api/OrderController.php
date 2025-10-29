<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AgricultureOrder;
use App\Models\AgricultureOrderItem;
use App\Models\AgricultureProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Get user orders
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = AgricultureOrder::where('user_id', $user->id)
            ->with('items.product')
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
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
     * Create new order
     */
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'billing_address' => 'required|string',
            'shipping_address' => 'nullable|string',
            'payment_method' => 'required|in:credit_card,bank_transfer,cash_on_delivery',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $cart = Session::get('cart', []);
        
        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Please add items before checkout.'
            ], 400);
        }

        // Calculate totals
        $subtotal = 0;
        $taxRate = 0.08; // 8% tax
        $shippingCost = 25;

        foreach ($cart as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                $price = $item['price'] ?? $product->getPriceForUser($request->user());
                $subtotal += $price * $item['quantity'];
            }
        }

        $taxAmount = $subtotal * $taxRate;
        $totalAmount = $subtotal + $taxAmount + $shippingCost;

        // Generate unique order number
        $orderNumber = 'AGR-' . strtoupper(Str::random(8));

        // Create order
        $order = AgricultureOrder::create([
            'order_number' => $orderNumber,
            'user_id' => $request->user()->id,
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
                $price = $item['price'] ?? $product->getPriceForUser($request->user());
                
                AgricultureOrderItem::create([
                    'agriculture_order_id' => $order->id,
                    'agriculture_product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $price * $item['quantity']
                ]);

                // Update product stock
                if ($product->manage_stock) {
                    $product->decrement('stock_quantity', $item['quantity']);
                    
                    if ($product->stock_quantity <= 0) {
                        $product->update(['in_stock' => false]);
                    }
                }
            }
        }

        // Clear cart
        Session::forget('cart');

        $order->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully!',
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
     */
    private function transformOrder($order)
    {
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
            'notes' => $order->notes,
            'items' => $order->items->map(function ($item) {
                $image = $item->product->primary_image 
                    ?? $item->product->featured_image 
                    ?? (is_array($item->product->gallery_images) && count($item->product->gallery_images) ? $item->product->gallery_images[0] : null);
                
                return [
                    'product_id' => $item->agriculture_product_id,
                    'product_name' => $item->product_name,
                    'product_sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'price' => (float) $item->price,
                    'total' => (float) $item->total,
                    'image' => $image ? asset('storage/' . $image) : null,
                ];
            }),
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
}

