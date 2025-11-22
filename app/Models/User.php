<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'viewable_password',
        'role',
        'business_name',
        'gst_number',
        'business_address',
        'contact_person',
        'phone',
        'alternate_phone',
        'is_dealer_approved',
        'dealer_approved_at',
        'approved_by',
        'dealer_rejection_reason',
        'company_website',
        'business_description',
        'pan_number'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_dealer_approved' => 'boolean',
            'dealer_approved_at' => 'datetime',
        ];
    }

    /**
     * Relationships
     */
    public function dealerRegistration()
    {
        return $this->hasOne(DealerRegistration::class);
    }

    public function approvedDealers()
    {
        return $this->hasMany(User::class, 'approved_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function agricultureOrders()
    {
        return $this->hasMany(AgricultureOrder::class);
    }

    /**
     * Scopes
     */
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeDealers($query)
    {
        return $query->where('role', 'dealer');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeApprovedDealers($query)
    {
        return $query->where('role', 'dealer')->where('is_dealer_approved', true);
    }

    public function scopePendingDealers($query)
    {
        return $query->where('role', 'dealer')->where('is_dealer_approved', false);
    }

    /**
     * Helper Methods
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDealer()
    {
        return $this->role === 'dealer';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isApprovedDealer()
    {
        return $this->isDealer() && $this->is_dealer_approved;
    }

    public function canAccessDealerPricing()
    {
        return $this->isApprovedDealer();
    }
}
