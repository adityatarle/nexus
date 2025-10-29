<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DealerRegistration;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DealerManagementController extends Controller
{
    /**
     * Show dealer management dashboard
     */
    public function index(Request $request)
    {
        $query = DealerRegistration::with(['user', 'reviewer']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('business_name', 'like', "%{$searchTerm}%")
                  ->orWhere('gst_number', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_person', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_email', 'like', "%{$searchTerm}%");
            });
        }

        $registrations = $query->latest()->paginate(15);

        // Statistics
        $stats = [
            'total_registrations' => DealerRegistration::count(),
            'pending_registrations' => DealerRegistration::pending()->count(),
            'approved_registrations' => DealerRegistration::approved()->count(),
            'rejected_registrations' => DealerRegistration::rejected()->count(),
        ];

        return view('admin.dealers.index', compact('registrations', 'stats'));
    }

    /**
     * Show dealer registration details
     */
    public function show(DealerRegistration $dealerRegistration)
    {
        $dealerRegistration->load(['user', 'reviewer']);
        
        return view('admin.dealers.show', compact('dealerRegistration'));
    }

    /**
     * Approve dealer registration
     */
    public function approve(Request $request, DealerRegistration $dealerRegistration)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        if ($dealerRegistration->isApproved()) {
            return redirect()->back()->with('error', 'This registration is already approved.');
        }

        $adminId = auth()->id(); // Assuming admin is logged in
        $dealerRegistration->approve($adminId, $request->admin_notes);

        // Create notification for dealer
        Notification::createDealerApprovalNotification(
            $dealerRegistration->user_id, 
            $dealerRegistration->business_name
        );

        return redirect()->route('admin.dealers.index')
            ->with('success', 'Dealer registration approved successfully!');
    }

    /**
     * Reject dealer registration
     */
    public function reject(Request $request, DealerRegistration $dealerRegistration)
    {
        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        if ($dealerRegistration->isRejected()) {
            return redirect()->back()->with('error', 'This registration is already rejected.');
        }

        $adminId = auth()->id(); // Assuming admin is logged in
        $dealerRegistration->reject($adminId, $request->rejection_reason, $request->admin_notes);

        // Create notification for dealer
        Notification::createDealerRejectionNotification(
            $dealerRegistration->user_id, 
            $dealerRegistration->business_name,
            $request->rejection_reason
        );

        return redirect()->route('admin.dealers.index')
            ->with('success', 'Dealer registration rejected successfully!');
    }

    /**
     * Show dealer profile and orders
     */
    public function showDealerProfile(User $user)
    {
        if (!$user->isDealer()) {
            return redirect()->back()->with('error', 'This user is not a dealer.');
        }

        $user->load(['dealerRegistration', 'agricultureOrders.items.product']);
        
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

        return view('admin.dealers.profile', compact('user', 'orders', 'stats'));
    }

    /**
     * Revoke dealer status
     */
    public function revokeDealerStatus(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'revocation_reason' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator);
        }

        if (!$user->isDealer()) {
            return redirect()->back()->with('error', 'This user is not a dealer.');
        }

        // Update user status
        $user->update([
            'is_dealer_approved' => false,
            'dealer_rejection_reason' => $request->revocation_reason,
        ]);

        // Create notification for dealer
        Notification::create([
            'user_id' => $user->id,
            'type' => 'dealer_revocation',
            'title' => 'Dealer Status Revoked',
            'message' => "Your dealer status has been revoked. Reason: {$request->revocation_reason}",
            'data' => ['reason' => $request->revocation_reason]
        ]);

        return redirect()->back()
            ->with('success', 'Dealer status revoked successfully!');
    }

    /**
     * Restore dealer status
     */
    public function restoreDealerStatus(User $user)
    {
        if (!$user->isDealer()) {
            return redirect()->back()->with('error', 'This user is not a dealer.');
        }

        $user->update([
            'is_dealer_approved' => true,
            'dealer_approved_at' => now(),
            'approved_by' => auth()->id(),
            'dealer_rejection_reason' => null,
        ]);

        // Create notification for dealer
        Notification::create([
            'user_id' => $user->id,
            'type' => 'dealer_restoration',
            'title' => 'Dealer Status Restored',
            'message' => 'Your dealer status has been restored. You can now access dealer pricing again.',
        ]);

        return redirect()->back()
            ->with('success', 'Dealer status restored successfully!');
    }
}