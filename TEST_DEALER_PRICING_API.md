# Troubleshooting: Dealer Price Shows as NULL

## Problem
Dealer price is showing as `null` in API response even for approved dealers.

---

## Root Cause Analysis

The issue is that dealer prices must be **set in the database** for the products. If `dealer_price` column is `NULL` in the database, the API will return `null` even for approved dealers.

---

## Step 1: Check if Products Have Dealer Prices

Run this command to check:

```bash
php artisan tinker --execute="echo 'Products without dealer price: ' . App\Models\AgricultureProduct::whereNull('dealer_price')->count();"
```

If it returns a number > 0, products don't have dealer prices set.

---

## Step 2: Add Dealer Prices to All Products

Run this seeder to add dealer prices:

```bash
php artisan db:seed --class=AddDealerPricingSeeder
```

This will:
- Add dealer price (25% off retail) to all products
- Add bulk pricing tiers
- Set dealer-specific fields

---

## Step 3: Verify User is Approved

Check if the dealer user is approved:

```bash
php artisan tinker --execute="$user = App\Models\User::where('email', 'YOUR_DEALER_EMAIL')->first(); echo 'Is Dealer: ' . ($user->isDealer() ? 'Yes' : 'No') . PHP_EOL; echo 'Is Approved: ' . ($user->is_dealer_approved ? 'Yes' : 'No') . PHP_EOL; echo 'Can Access Dealer Pricing: ' . ($user->canAccessDealerPricing() ? 'Yes' : 'No') . PHP_EOL;"
```

Replace `YOUR_DEALER_EMAIL` with the dealer's email.

**Expected Output:**
```
Is Dealer: Yes
Is Approved: Yes
Can Access Dealer Pricing: Yes
```

If "Is Approved" or "Can Access Dealer Pricing" is "No", the dealer registration needs admin approval.

---

## Step 4: Test API Response

### Test 1: Get User Info

```bash
curl -X GET "https://nexus.heuristictechpark.com/api/v1/user" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response (Approved Dealer):**
```json
{
  "success": true,
  "data": {
    "user": {
      "is_dealer": true,
      "is_approved_dealer": true,
      "can_access_dealer_pricing": true
    }
  }
}
```

If `can_access_dealer_pricing` is `false`, admin needs to approve the dealer.

### Test 2: Get Products

```bash
curl -X GET "https://nexus.heuristictechpark.com/api/v1/products" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response (Approved Dealer):**
```json
{
  "success": true,
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Product Name",
        "price": 7500.00,           // Dealer price (if approved)
        "original_price": 10000.00,  // Retail price
        "dealer_price": 7500.00,     // Visible to approved dealers
        "dealer_sale_price": null
      }
    ]
  }
}
```

**If dealer_price is NULL:**
- Products don't have dealer pricing in database
- Run `AddDealerPricingSeeder`

**If price shows retail instead of dealer:**
- User is not approved
- Check `can_access_dealer_pricing` in user response
- Admin needs to approve dealer registration

---

## Step 5: Approve Dealer (If Pending)

If dealer is not approved, admin needs to approve:

1. Login to admin panel: `/admin/login`
2. Go to "Dealer Management"
3. Find the dealer registration
4. Click "Approve"

**Or approve via command:**
```bash
php artisan tinker --execute="$user = App\Models\User::where('email', 'DEALER_EMAIL')->first(); $user->update(['is_dealer_approved' => true, 'dealer_approved_at' => now()]); echo 'Dealer approved!';"
```

---

## Common Issues & Solutions

### Issue 1: dealer_price is null in response

**Cause:** Products don't have dealer prices in database

**Solution:**
```bash
php artisan db:seed --class=AddDealerPricingSeeder
```

### Issue 2: API returns retail price instead of dealer price

**Cause:** User is not approved or token not sent

**Solutions:**
- Check `can_access_dealer_pricing` is `true`
- Verify token is sent: `Authorization: Bearer {token}`
- Approve dealer registration in admin panel

### Issue 3: can_access_dealer_pricing is false

**Cause:** Dealer not approved by admin

**Solution:**
- Admin must approve dealer registration
- Or use tinker command above to approve manually

### Issue 4: Token not working

**Cause:** Token expired or invalid

**Solution:**
- Login again to get fresh token
- Use the new token in API calls

---

## Complete Test Flow

### 1. Add Dealer Prices to Products
```bash
php artisan db:seed --class=AddDealerPricingSeeder
```

### 2. Approve Dealer (Manual Method)
```bash
php artisan tinker
```
Then run:
```php
$user = App\Models\User::where('email', 'dealer@example.com')->first();
$user->update([
    'is_dealer_approved' => true,
    'dealer_approved_at' => now()
]);
echo "Dealer approved!";
exit;
```

### 3. Test API
```bash
# Login
curl -X POST "https://nexus.heuristictechpark.com/api/v1/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"dealer@example.com","password":"password123"}'

# Copy token from response, then:
curl -X GET "https://nexus.heuristictechpark.com/api/v1/products" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### 4. Verify Response
- `dealer_price` should NOT be null
- `price` should show dealer price (lower than original_price)
- `original_price` should show retail price

---

## Quick Fix Command

Run all fixes at once:

```bash
# 1. Add dealer prices
php artisan db:seed --class=AddDealerPricingSeeder

# 2. Approve a specific dealer
php artisan tinker --execute="App\Models\User::where('email', 'DEALER_EMAIL')->first()->update(['is_dealer_approved' => true, 'dealer_approved_at' => now()]); echo 'Done!';"

# 3. Clear caches
php artisan optimize:clear
```

Replace `DEALER_EMAIL` with the actual dealer email.

---

## Checklist

Before contacting support, verify:

- [ ] Products have `dealer_price` set (not NULL)
- [ ] User `is_dealer_approved` is `true`
- [ ] User `can_access_dealer_pricing` returns `true`
- [ ] API requests include `Authorization: Bearer {token}` header
- [ ] Token is valid (not expired)
- [ ] Products API returns `dealer_price` (not null)
- [ ] Products API returns dealer price in `price` field

---

**Last Updated:** October 30, 2025

