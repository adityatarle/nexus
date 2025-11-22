<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = User::customers()->withCount('agricultureOrders');

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('phone', 'like', "%{$searchTerm}%");
            });
        }

        $customers = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total_customers' => User::customers()->count(),
            'new_this_month' => User::customers()->whereMonth('created_at', now()->month)->count(),
            'total_orders' => \App\Models\AgricultureOrder::whereIn('user_id', User::customers()->pluck('id'))->count(),
            'total_revenue' => \App\Models\AgricultureOrder::whereIn('user_id', User::customers()->pluck('id'))->sum('total_amount'),
        ];

        return view('admin.customers.index', compact('customers', 'stats'));
    }

    /**
     * Display the specified customer
     */
    public function show(User $user)
    {
        if (!$user->isCustomer()) {
            return redirect()->back()->with('error', 'This user is not a customer.');
        }

        $user->load('agricultureOrders.items.product');
        
        $orders = $user->agricultureOrders()
            ->with('items.product')
            ->latest()
            ->paginate(10);

        $stats = [
            'total_orders' => $user->agricultureOrders()->count(),
            'total_spent' => $user->agricultureOrders()->sum('total_amount'),
            'average_order_value' => $user->agricultureOrders()->avg('total_amount'),
            'last_order_date' => $user->agricultureOrders()->latest()->first()?->created_at,
        ];

        return view('admin.customers.show', compact('user', 'orders', 'stats'));
    }

    /**
     * Reset customer password
     */
    public function resetPassword(Request $request, User $user)
    {
        if (!$user->isCustomer()) {
            return redirect()->back()->with('error', 'This user is not a customer.');
        }

        $request->validate([
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.confirmed' => 'The password confirmation does not match.',
        ]);

        $user->update([
            'password' => \Hash::make($request->new_password),
            'viewable_password' => $request->new_password, // Store plain text for admin viewing
        ]);

        return redirect()->back()
            ->with('success', 'Password has been reset successfully for ' . $user->name . '.');
    }
}


















