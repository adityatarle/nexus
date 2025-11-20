<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgricultureOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'agriculture_order_id',
        'agriculture_product_id',
        'product_name',
        'product_sku',
        'quantity',
        'price',
        'original_price',
        'discount_amount',
        'offer_id',
        'offer_details',
        'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'offer_details' => 'array'
    ];

    public function order()
    {
        return $this->belongsTo(AgricultureOrder::class, 'agriculture_order_id');
    }

    public function product()
    {
        return $this->belongsTo(AgricultureProduct::class, 'agriculture_product_id');
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class, 'offer_id');
    }
}