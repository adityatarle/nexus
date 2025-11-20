<?php

namespace App\Http\Controllers;

use App\Models\AgricultureOrder;
use App\Models\AgricultureProduct;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;

class DealerDashboardController extends Controller
{
    /**
     * Show dealer dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $stats = [
            'total_orders' => $user->agricultureOrders()->count(),
            'pending_orders' => $user->agricultureOrders()->whereIn('order_status', ['pending', 'confirmed', 'shipped'])->count(),
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

        return view('dealer.dashboard', compact('stats', 'recentOrders', 'notifications'));
    }

    /**
     * Show dealer orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
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

        return view('dealer.orders', compact('orders'));
    }

    /**
     * Show order details
     */
    public function orderShow($orderNumber)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $order = AgricultureOrder::with('items.product')
            ->where('user_id', $user->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('dealer.order-details', compact('order'));
    }

    /**
     * Download invoice
     */
    public function downloadInvoice($orderNumber)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $order = AgricultureOrder::with('items.product', 'user')
            ->where('user_id', $user->id)
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $pdf = Pdf::loadView('dealer.invoice', compact('order'));
        return $pdf->download("invoice-{$orderNumber}.pdf");
    }

    /**
     * Show dealer product catalog with dealer prices
     */
    public function products(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $query = AgricultureProduct::with('category')->active()->inStock();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('agriculture_category_id', $request->category);
        }

        // Search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('sku', 'like', "%{$searchTerm}%");
            });
        }

        $products = $query->paginate(12);
        $categories = \App\Models\AgricultureCategory::all();

        return view('dealer.products', compact('products', 'categories'));
    }

    /**
     * Show dealer profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $registration = $user->dealerRegistration;

        return view('dealer.profile', compact('user', 'registration'));
    }

    /**
     * Update dealer profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'company_website' => 'nullable|url|max:255',
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
            'alternate_phone' => $request->alternate_phone,
            'company_website' => $request->company_website,
        ]);

        // Also update dealer registration if exists
        if ($user->dealerRegistration) {
            $user->dealerRegistration->update([
                'contact_email' => $request->email,
                'contact_phone' => $request->phone,
                'alternate_phone' => $request->alternate_phone,
                'company_website' => $request->company_website,
            ]);
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Show dealer notifications
     */
    public function notifications()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        $notifications = $user->notifications()
            ->latest()
            ->paginate(15);

        return view('dealer.notifications', compact('notifications'));
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


















