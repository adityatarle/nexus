<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DealerRegistration;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DealerController extends Controller
{
    /**
     * Submit dealer registration
     */
    public function register(Request $request)
    {
        $user = $request->user();

        // Check if user is already registered as dealer
        if (!$user->isDealer()) {
            return response()->json([
                'success' => false,
                'message' => 'User account is not registered as dealer. Please register with role=dealer first.'
            ], 400);
        }

        // Check if already submitted
        if ($user->dealerRegistration) {
            $status = $user->dealerRegistration->status;
            return response()->json([
                'success' => false,
                'message' => 'Dealer registration already submitted',
                'data' => [
                    'status' => $status,
                    'registration' => $this->formatRegistration($user->dealerRegistration)
                ]
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'business_name' => 'required|string|max:255',
            'gst_number' => 'required|string|unique:dealer_registrations,gst_number',
            'pan_number' => 'required|string|max:20',
            'business_address' => 'required|string',
            'business_city' => 'required|string|max:100',
            'business_state' => 'required|string|max:100',
            'business_pincode' => 'required|string|max:10',
            'business_country' => 'nullable|string|max:100',
            'contact_person' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'company_website' => 'nullable|url|max:255',
            'business_description' => 'required|string',
            'business_type' => 'required|in:Individual,Partnership,Private Limited,Public Limited,LLP,Other',
            'years_in_business' => 'nullable|integer|min:0',
            'annual_turnover' => 'nullable|string|max:100',
            'gst_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'pan_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'business_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'terms_accepted' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
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
            'gst_number' => $request->gst_number,
            'pan_number' => $request->pan_number,
            'business_address' => $request->business_address,
            'business_city' => $request->business_city,
            'business_state' => $request->business_state,
            'business_pincode' => $request->business_pincode,
            'business_country' => $request->business_country ?? 'India',
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
            'status' => 'pending',
        ]);

        // Create notification
        Notification::create([
            'user_id' => $user->id,
            'title' => 'Dealer Registration Submitted',
            'message' => 'Your dealer registration has been submitted successfully. We will review it and notify you once approved.',
            'type' => 'info',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dealer registration submitted successfully. Your application is under review.',
            'data' => [
                'registration' => $this->formatRegistration($registration)
            ]
        ], 201);
    }

    /**
     * Get dealer registration status
     */
    public function status(Request $request)
    {
        $user = $request->user();

        if (!$user->isDealer()) {
            return response()->json([
                'success' => false,
                'message' => 'User is not registered as dealer'
            ], 400);
        }

        $registration = $user->dealerRegistration;

        if (!$registration) {
            return response()->json([
                'success' => true,
                'data' => [
                    'is_registered' => false,
                    'is_approved' => false,
                    'status' => 'not_submitted',
                    'message' => 'Dealer registration not submitted yet'
                ]
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'is_registered' => true,
                'is_approved' => $registration->isApproved(),
                'status' => $registration->status,
                'registration' => $this->formatRegistration($registration),
                'message' => $this->getStatusMessage($registration->status)
            ]
        ]);
    }

    /**
     * Format registration for API response
     */
    private function formatRegistration($registration)
    {
        $formatted = [
            'id' => $registration->id,
            'business_name' => $registration->business_name,
            'gst_number' => $registration->gst_number,
            'pan_number' => $registration->pan_number,
            'status' => $registration->status,
            'business_city' => $registration->business_city,
            'business_state' => $registration->business_state,
            'contact_person' => $registration->contact_person,
            'contact_email' => $registration->contact_email,
            'contact_phone' => $registration->contact_phone,
            'reviewed_at' => $registration->reviewed_at?->toISOString(),
            'created_at' => $registration->created_at->toISOString(),
        ];

        if ($registration->status === 'rejected' && $registration->rejection_reason) {
            $formatted['rejection_reason'] = $registration->rejection_reason;
        }

        // Add document URLs if they exist
        if ($registration->business_documents && is_array($registration->business_documents)) {
            $formatted['documents'] = [];
            foreach ($registration->business_documents as $key => $path) {
                if ($path) {
                    $formatted['documents'][$key] = Storage::url($path);
                }
            }
        }

        return $formatted;
    }

    /**
     * Get status message
     */
    private function getStatusMessage($status)
    {
        return match($status) {
            'pending' => 'Your dealer registration is under review. We will notify you once it is approved.',
            'approved' => 'Your dealer registration has been approved. You can now access dealer pricing.',
            'rejected' => 'Your dealer registration was rejected. Please check the rejection reason and resubmit.',
            default => 'Unknown status'
        };
    }
}

