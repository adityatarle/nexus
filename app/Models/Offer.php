<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_image',
        'offer_type',
        'product_id',
        'category_id',
        'subcategory_id',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'min_quantity',
        'start_date',
        'end_date',
        'max_uses',
        'max_uses_per_user',
        'used_count',
        'is_active',
        'is_featured',
        'sort_order',
        'priority',
        'terms_conditions',
        'for_customers',
        'for_dealers',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'for_customers' => 'boolean',
        'for_dealers' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($offer) {
            if (empty($offer->slug)) {
                $offer->slug = Str::slug($offer->title);
            }
        });
    }

    /**
     * Relationships
     */
    public function product()
    {
        return $this->belongsTo(AgricultureProduct::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(AgricultureCategory::class, 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsTo(AgricultureSubcategory::class, 'subcategory_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function($q) {
                $q->whereNull('max_uses')
                  ->orWhereColumn('used_count', '<', 'max_uses');
            });
    }

    public function scopeForCustomers($query)
    {
        return $query->where('for_customers', true);
    }

    public function scopeForDealers($query)
    {
        return $query->where('for_dealers', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('offer_type', $type);
    }

    /**
     * Helper Methods
     */
    public function isValid()
    {
        $now = Carbon::now();
        
        if (!$this->is_active) {
            return false;
        }
        
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }
        
        if ($this->max_uses && $this->used_count >= $this->max_uses) {
            return false;
        }
        
        return true;
    }

    public function isExpired()
    {
        return Carbon::now()->gt($this->end_date);
    }

    public function isUpcoming()
    {
        return Carbon::now()->lt($this->start_date);
    }

    public function calculateDiscount($amount)
    {
        if ($this->discount_type === 'percentage') {
            return ($amount * $this->discount_value) / 100;
        }
        
        return min($this->discount_value, $amount);
    }

    public function getDiscountedPrice($originalPrice)
    {
        $discount = $this->calculateDiscount($originalPrice);
        return max(0, $originalPrice - $discount);
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function canBeUsedBy($user = null)
    {
        if (!$this->isValid()) {
            return false;
        }
        
        if ($user) {
            if ($user->isDealer() && !$this->for_dealers) {
                return false;
            }
            
            if (!$user->isDealer() && !$this->for_customers) {
                return false;
            }
        }
        
        return true;
    }

    public function appliesToProduct($productId)
    {
        if ($this->offer_type === 'product') {
            return $this->product_id == $productId;
        }
        
        if ($this->offer_type === 'category') {
            $product = AgricultureProduct::find($productId);
            return $product && $product->agriculture_category_id == $this->category_id;
        }
        
        if ($this->offer_type === 'subcategory') {
            $product = AgricultureProduct::find($productId);
            return $product && $product->agriculture_subcategory_id == $this->subcategory_id;
        }
        
        if ($this->offer_type === 'general') {
            return true;
        }
        
        return false;
    }
}
