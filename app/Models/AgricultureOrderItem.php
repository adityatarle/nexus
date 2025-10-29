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
        'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    public function order()
    {
        return $this->belongsTo(AgricultureOrder::class, 'agriculture_order_id');
    }

    public function product()
    {
        return $this->belongsTo(AgricultureProduct::class, 'agriculture_product_id');
    }
}