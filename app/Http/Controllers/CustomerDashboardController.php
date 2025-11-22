<?php

namespace App\Http\Controllers;

use App\Models\AgricultureOrder;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomerDashboardController extends Controller
{
    /**
     * Show customer dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        $stats = [
            'total_orders' => $user->agricultureOrders()->count(),
            'pending_orders' => $user->agricultureOrders()->where('order_status', 'pending')->count(),
            'total_spent' => $user->agricultureOrders()->sum('total_amount'),
            'unread_notifications' => $user->notifications()->unread()->count(),
        ];

        $recentOrders = $user->agricultureOrders()
            ->with('items.product')
            ->latest()
            ->take(5)
            ->get();

        $notifications = $user->notifications()
            ->latest()
            ->take(5)
            ->get();

        return view('customer.dashboard', compact('stats', 'recentOrders', 'notifications'));
    }

    /**
     * Show customer orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        $query = $user->agricultureOrders()->with('items.product');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->search}%");
        }

        $orders = $query->latest()->paginate(10);

        return view('customer.orders', compact('orders'));
    }

    /**
     * Show order details
     */
    public function orderShow($orderNumber)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        $order = AgricultureOrder::with('items.product')
            ->where('user_id', $user->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('customer.order-details', compact('order'));
    }

    /**
     * Show customer profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        // Get recent orders for the profile page
        $recentOrders = $user->agricultureOrders()
            ->with('items.product')
            ->latest()
            ->take(10)
            ->get();

        return view('customer.profile', compact('user', 'recentOrders'));
    }

    /**
     * Update customer profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update customer password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'viewable_password' => $request->password, // Store plain text for admin viewing
        ]);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }

    /**
     * Show customer notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isCustomer()) {
            return redirect()->route('auth.login')->with('error', 'Please login as a customer.');
        }

        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        return view('customer.notifications', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markNotificationRead($id)
    {
        $user = Auth::user();
        
        $notification = Notification::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $notification->markAsRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        $user = Auth::user();
        
        $user->notifications()->unread()->update([
            'is_read' => true,
            'read_at' => now(),
        ]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}













