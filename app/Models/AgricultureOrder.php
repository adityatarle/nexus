<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'customer_email',
        'customer_name',
        'customer_phone',
        'billing_address',
        'shipping_address',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'notes'
    ];

    protected $casts = [
        'billing_address' => 'array',
        'shipping_address' => 'array',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(AgricultureOrderItem::class, 'agriculture_order_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('order_status', $status);
    }

    public function scopeByPaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Generate next GL order number starting from GL-1001
     * 
     * @return string
     */
    public static function generateOrderNumber()
    {
        // Find the last order with GL- prefix
        $lastOrder = self::where('order_number', 'like', 'GL-%')
            ->orderByRaw('CAST(SUBSTRING(order_number, 4) AS UNSIGNED) DESC')
            ->first();

        if ($lastOrder && preg_match('/^GL-(\d+)$/', $lastOrder->order_number, $matches)) {
            // Extract the number part after "GL-"
            $lastNumber = (int) $matches[1];
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from GL-1001 if no GL orders exist
            $nextNumber = 1001;
        }

        return 'GL-' . $nextNumber;
    }
}