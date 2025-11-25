<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\DealerRegistration;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DealerRegistrationController extends Controller
{
    /**
     * Show dealer registration form
     */
    public function showRegistration()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isDealer()) {
            return redirect()->route('auth.register')->with('error', 'Please register as a dealer first.');
        }

        // Check if already registered
        if ($user->dealerRegistration) {
            return redirect()->route('dealer.pending')->with('info', 'You have already submitted a dealer registration.');
        }

        return view('auth.dealer-registration');
    }

    /**
     * Handle dealer registration submission
     */
    public function register(Request $request)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isDealer()) {
            return redirect()->route('auth.register')->with('error', 'Please register as a dealer first.');
        }

        $validator = Validator::make($request->all(), [
            // Business Information
            'business_name' => 'required|string|max:255',
            'gst_number' => 'nullable|string|regex:/^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/|unique:dealer_registrations,gst_number',
            'pan_number' => 'required|string|regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/',
            'business_address' => 'required|string|max:1000',
            'business_city' => 'required|string|max:100',
            'business_state' => 'required|string|max:100',
            'business_pincode' => 'required|string|regex:/^[0-9]{6}$/',
            'business_country' => 'required|string|max:100',
            
            // Contact Information
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'company_website' => 'nullable|url|max:255',
            
            // Business Details
            'business_description' => 'required|string|max:2000',
            'business_type' => 'required|string|in:Individual,Partnership,Company,LLP,HUF',
            'years_in_business' => 'nullable|integer|min:0|max:100',
            'annual_turnover' => 'nullable|string|in:0-10L,10L-50L,50L-1Cr,1Cr-5Cr,5Cr+',
            
            // Documents
            'gst_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'pan_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'business_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            
            // Terms
            'terms_accepted' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file uploads
        $documents = [];
        if ($request->hasFile('gst_certificate')) {
            $documents['gst_certificate'] = $request->file('gst_certificate')->store('dealer-documents', 'public');
        }
        if ($request->hasFile('pan_certificate')) {
            $documents['pan_certificate'] = $request->file('pan_certificate')->store('dealer-documents', 'public');
        }
        if ($request->hasFile('business_license')) {
            $documents['business_license'] = $request->file('business_license')->store('dealer-documents', 'public');
        }

        // Create dealer registration
        $registration = DealerRegistration::create([
            'user_id' => $user->id,
            'business_name' => $request->business_name,
            'gst_number' => $request->gst_number ?? null,
            'pan_number' => $request->pan_number,
            'business_address' => $request->business_address,
            'business_city' => $request->business_city,
            'business_state' => $request->business_state,
            'business_pincode' => $request->business_pincode,
            'business_country' => $request->business_country,
            'contact_person' => $request->contact_person,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'alternate_phone' => $request->alternate_phone,
            'company_website' => $request->company_website,
            'business_description' => $request->business_description,
            'business_type' => $request->business_type,
            'years_in_business' => $request->years_in_business,
            'annual_turnover' => $request->annual_turnover,
            'business_documents' => $documents,
            'terms_accepted' => true,
            'terms_accepted_at' => now(),
        ]);

        // Update user with business information
        $user->update([
            'business_name' => $request->business_name,
            'gst_number' => $request->gst_number ?? null,
            'business_address' => $request->business_address,
            'contact_person' => $request->contact_person,
            'phone' => $request->contact_phone,
            'alternate_phone' => $request->alternate_phone,
            'company_website' => $request->company_website,
            'business_description' => $request->business_description,
            'pan_number' => $request->pan_number,
        ]);

        // Create notification for user
        Notification::createDealerRegistrationNotification($user->id, $request->business_name);

        // Create notification for admin (we'll implement this later)
        // For now, we'll just redirect with success message

        return redirect()->route('dealer.pending')->with('success', 'Dealer registration submitted successfully! Your application is under review.');
    }

    /**
     * Show pending status page
     */
    public function showPending()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isDealer()) {
            return redirect()->route('auth.register')->with('error', 'Please register as a dealer first.');
        }

        $registration = $user->dealerRegistration;
        
        if (!$registration) {
            return redirect()->route('dealer.registration')->with('error', 'Please complete your dealer registration first.');
        }

        return view('auth.dealer-pending', compact('registration'));
    }

    /**
     * Show dealer dashboard (only for approved dealers)
     */
    public function showDashboard()
    {
        $user = Auth::user();
        
        if (!$user || !$user->isApprovedDealer()) {
            return redirect()->route('dealer.pending')->with('error', 'Your dealer registration is not approved yet.');
        }

        // Get dealer statistics
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

        return view('dealer.dashboard', compact('stats', 'recentOrders'));
    }
}