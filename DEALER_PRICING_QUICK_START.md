# Dealer Pricing - Quick Start Guide

## How It Works (Simplified)

```
1. User registers as dealer → CanAccessDealerPricing = FALSE
   ↓
2. User submits dealer registration form → Status = "pending"
   ↓
3. User sees RETAIL prices (product.price = retail price)
   ↓
4. Admin approves dealer → is_dealer_approved = TRUE
   ↓
5. User's canAccessDealerPricing becomes TRUE ✅
   ↓
6. User refreshes products → product.price = DEALER PRICE ✅
```

---

## Backend (Already Done ✅)

The backend **automatically** handles pricing:
- Product API checks `user.canAccessDealerPricing()`
- If `true` → Returns dealer price in `product.price`
- If `false` → Returns retail price in `product.price`

**No changes needed on backend!**

---

## Frontend (What You Need to Do)

### 1. Always Send Auth Token ✅

```dart
// When fetching products
GET /api/v1/products
Headers: Authorization: Bearer {token}  // REQUIRED!
```

### 2. Poll User Status When Pending 🔄

```dart
// Check every 30 seconds if pending
if (user.dealerRegistration.status == "pending") {
  Timer.periodic(Duration(seconds: 30), (timer) async {
    final user = await getUserStatus(token);
    if (user.canAccessDealerPricing) {
      timer.cancel();
      refreshProducts(); // ✅ Re-fetch products
    }
  });
}
```

### 3. Refresh Products After Approval 🔄

```dart
// When canAccessDealerPricing becomes true
if (user.canAccessDealerPricing) {
  await fetchProducts(token); // API now returns dealer prices!
}
```

### 4. Display Based on Status 🎨

```dart
if (user.canAccessDealerPricing) {
  // Show dealer price
  Text('₹${product.price}'); // This is dealer price
  Text('MRP: ₹${product.originalPrice}', 
       style: TextStyle(decoration: TextDecoration.lineThrough));
} else {
  // Show retail price
  Text('₹${product.price}'); // This is retail price
}
```

---

## Key Fields

| Field | Meaning | When to Use |
|-------|---------|-------------|
| `user.can_access_dealer_pricing` | Can see dealer prices? | Check before displaying prices |
| `product.price` | Current price (dealer or retail) | Always display this |
| `product.original_price` | Retail price | Show as comparison when dealer |
| `product.dealer_price` | Dealer price | Only visible if approved |

---

## Testing

1. ✅ Register as dealer
2. ✅ Submit registration form
3. ✅ Fetch products → See retail prices
4. ✅ Admin approves (in admin panel)
5. ✅ Poll user status → `can_access_dealer_pricing` becomes `true`
6. ✅ Refresh products → See dealer prices

---

## Example API Flow

**Before Approval:**
```
GET /api/v1/user
→ can_access_dealer_pricing: false

GET /api/v1/products
→ price: 100000 (retail)
→ dealer_price: null
```

**After Approval:**
```
GET /api/v1/user
→ can_access_dealer_pricing: true ✅

GET /api/v1/products
→ price: 75000 (dealer) ✅
→ original_price: 100000 (retail)
→ dealer_price: 75000 ✅
```

---

## Summary

✅ **Backend**: Already working - API auto-returns correct prices
✅ **Frontend**: Poll status + Refresh products when approved
✅ **Display**: Use `can_access_dealer_pricing` to determine what to show

**It's automatic!** Just refresh products after approval! 🎉


