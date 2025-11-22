<?php

namespace App\Http\Controllers;

use App\Models\AgricultureOrder;
use App\Models\AgricultureOrderItem;
use App\Models\AgricultureProduct;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'customer_phone' => 'required|string|max:20',
            'billing_address' => 'required|string',
            'shipping_address' => 'nullable|string',
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
        $user = Auth::user(); // Get authenticated user for dealer pricing

        foreach ($cart as $item) {
            $product = AgricultureProduct::find($item['product_id']);
            if ($product) {
                // Use getPriceForUser to ensure consistent pricing (handles dealer pricing)
                $price = $item['price'] ?? $product->getPriceForUser($user);
                $subtotal += $price * $item['quantity'];
            }
        }

        $taxAmount = round($subtotal * $taxRate, 2);
        $totalAmount = round($subtotal + $taxAmount + $shippingCost, 2);

        // Generate unique order number starting from GL-1001
        $orderNumber = AgricultureOrder::generateOrderNumber();

        // Create order as inquiry (no payment required)
        $order = AgricultureOrder::create([
            'order_number' => $orderNumber,
            'user_id' => Auth::id(), // Associate order with logged-in user if available
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
        foreach ($cart as $item) {
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

        // Clear cart
        session()->forget('cart');
        session()->forget('cart_total');

        // Redirect to inquiry confirmation
        return redirect()->route('agriculture.checkout.success', $order->order_number)
            ->with('success', 'Thank you! We have received your inquiry.');
    }

    public function success($orderNumber)
    {
        $order = AgricultureOrder::where('order_number', $orderNumber)->firstOrFail();
        $order->load('items.product');

        return view('agriculture.checkout-success', compact('order'));
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
}
