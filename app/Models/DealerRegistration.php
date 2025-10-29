<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DealerRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'gst_number',
        'pan_number',
        'business_address',
        'business_city',
        'business_state',
        'business_pincode',
        'business_country',
        'contact_person',
        'contact_email',
        'contact_phone',
        'alternate_phone',
        'company_website',
        'business_description',
        'business_type',
        'years_in_business',
        'annual_turnover',
        'business_documents',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
        'admin_notes',
        'additional_info',
        'terms_accepted',
        'terms_accepted_at'
    ];

    protected $casts = [
        'business_documents' => 'array',
        'additional_info' => 'array',
        'terms_accepted' => 'boolean',
        'reviewed_at' => 'datetime',
        'terms_accepted_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scopes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Helper Methods
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function approve($reviewedBy, $adminNotes = null)
    {
        $this->update([
            'status' => 'approved',
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'admin_notes' => $adminNotes
        ]);

        // Update user as approved dealer
        $this->user->update([
            'is_dealer_approved' => true,
            'dealer_approved_at' => now(),
            'approved_by' => $reviewedBy
        ]);
    }

    public function reject($reviewedBy, $rejectionReason, $adminNotes = null)
    {
        $this->update([
            'status' => 'rejected',
            'reviewed_by' => $reviewedBy,
            'reviewed_at' => now(),
            'rejection_reason' => $rejectionReason,
            'admin_notes' => $adminNotes
        ]);

        // Update user rejection reason
        $this->user->update([
            'dealer_rejection_reason' => $rejectionReason
        ]);
    }
}