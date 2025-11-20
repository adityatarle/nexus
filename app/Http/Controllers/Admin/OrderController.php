<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgricultureOrder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = AgricultureOrder::with('items.product');
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }
        
        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }
        
        $orders = $query->latest()->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(AgricultureOrder $order)
    {
        $order->load('items.product');
        
        // Verify and recalculate subtotal from order items to ensure accuracy
        $calculatedSubtotal = $order->items->sum('total');
        $calculatedSubtotal = round($calculatedSubtotal, 2);
        
        // Check if there's a discrepancy
        $subtotalDifference = abs($order->subtotal - $calculatedSubtotal);
        if ($subtotalDifference > 0.01) { // Allow for small floating point differences
            // Recalculate tax and total based on actual order items
            $taxRate = 0.08; // 8% tax
            $calculatedTaxAmount = round($calculatedSubtotal * $taxRate, 2);
            $calculatedTotalAmount = round($calculatedSubtotal + $calculatedTaxAmount + ($order->shipping_amount ?? 25), 2);
            
            // Update order with recalculated values
            $order->update([
                'subtotal' => $calculatedSubtotal,
                'tax_amount' => $calculatedTaxAmount,
                'total_amount' => $calculatedTotalAmount
            ]);
        }
        
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, AgricultureOrder $order)
    {
        $request->validate([
            'order_status' => 'required|in:inquiry,pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:not_required,pending,paid,failed,refunded'
        ]);

        $oldOrderStatus = $order->order_status;
        $oldPaymentStatus = $order->payment_status;

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status
        ]);

        $messages = [];
        if ($oldOrderStatus !== $request->order_status) {
            $messages[] = 'Order status updated successfully!';
        }
        if ($oldPaymentStatus !== $request->payment_status) {
            $messages[] = 'Payment status updated successfully!';
        }

        return redirect()->back()->with('success', !empty($messages) ? implode(' ', $messages) : 'Status updated successfully!');
    }
}
