<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
        'is_read',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Relationships
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Helper Methods
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    /**
     * Static Methods for creating notifications
     */
    public static function createDealerRegistrationNotification($userId, $businessName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'dealer_registration',
            'title' => 'Dealer Registration Submitted',
            'message' => "Your dealer registration for {$businessName} has been submitted and is under review.",
            'data' => ['business_name' => $businessName]
        ]);
    }

    public static function createDealerApprovalNotification($userId, $businessName)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'dealer_approval',
            'title' => 'Dealer Registration Approved',
            'message' => "Congratulations! Your dealer registration for {$businessName} has been approved. You can now access dealer pricing.",
            'data' => ['business_name' => $businessName]
        ]);
    }

    public static function createDealerRejectionNotification($userId, $businessName, $reason)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'dealer_rejection',
            'title' => 'Dealer Registration Rejected',
            'message' => "Your dealer registration for {$businessName} has been rejected. Reason: {$reason}",
            'data' => ['business_name' => $businessName, 'reason' => $reason]
        ]);
    }

    public static function createOrderStatusNotification($userId, $orderNumber, $status)
    {
        return self::create([
            'user_id' => $userId,
            'type' => 'order_status',
            'title' => 'Order Status Update',
            'message' => "Your order #{$orderNumber} status has been updated to: {$status}",
            'data' => ['order_number' => $orderNumber, 'status' => $status]
        ]);
    }
}