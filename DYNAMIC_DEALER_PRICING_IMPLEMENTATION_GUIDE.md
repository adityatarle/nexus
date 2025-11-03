# Dynamic Dealer Pricing Implementation Guide

## Overview

This guide explains how to implement **automatic price switching** in your application:
- **Before Approval**: Dealer sees regular retail prices
- **After Admin Approval**: Dealer automatically sees dealer prices

---

## How It Works (Backend is Already Set Up)

✅ **Backend automatically handles pricing** - The API returns the correct prices based on user's approval status.

### Key Points:
1. The API checks `user.can_access_dealer_pricing` automatically
2. Product `price` field is **automatically calculated** as dealer price if approved
3. You just need to **refresh user status** and **re-fetch products** after approval

---

## Implementation Steps

### Step 1: Always Include Authentication Token

**⚠️ CRITICAL:** Always send the `Authorization: Bearer {token}` header when fetching products to get user-specific pricing.

```dart
// ✅ CORRECT - Include token
GET /api/v1/products
Headers: Authorization: Bearer {token}

// ❌ WRONG - No token = always retail prices
GET /api/v1/products
```

---

### Step 2: Check User Status Regularly

Check user status to detect when admin approves the dealer registration:

```dart
// Check user status (call this periodically or on app open)
Future<User> checkUserStatus(String token) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/user'),
    headers: {
      'Authorization': 'Bearer $token',
    },
  );
  
  final data = jsonDecode(response.body);
  return User.fromJson(data['data']['user']);
}
```

**Key Field to Watch:**
```dart
user.canAccessDealerPricing  // true = approved, false = not approved
```

---

### Step 3: Implement Status Polling (Optional but Recommended)

Poll user status periodically to detect approval:

```dart
class DealerStatusPoller {
  Timer? _timer;
  final String token;
  final Function(bool isApproved) onStatusChanged;
  
  DealerStatusPoller({
    required this.token,
    required this.onStatusChanged,
  });
  
  void startPolling() {
    // Check immediately
    _checkStatus();
    
    // Then check every 30 seconds
    _timer = Timer.periodic(Duration(seconds: 30), (timer) {
      _checkStatus();
    });
  }
  
  Future<void> _checkStatus() async {
    try {
      final user = await checkUserStatus(token);
      onStatusChanged(user.canAccessDealerPricing);
      
      // Stop polling if approved
      if (user.canAccessDealerPricing) {
        stopPolling();
      }
    } catch (e) {
      print('Error checking status: $e');
    }
  }
  
  void stopPolling() {
    _timer?.cancel();
    _timer = null;
  }
}
```

**Usage:**
```dart
final poller = DealerStatusPoller(
  token: userToken,
  onStatusChanged: (isApproved) {
    if (isApproved) {
      // Refresh products to show dealer prices
      refreshProducts();
      showSnackBar('Dealer account approved! You can now see dealer prices.');
    }
  },
);

// Start polling when dealer registration is pending
if (user.dealerRegistration?.status == 'pending') {
  poller.startPolling();
}
```

---

### Step 4: Refresh Products After Approval

When `can_access_dealer_pricing` becomes `true`, refresh your products list:

```dart
Future<void> refreshProducts() async {
  // Re-fetch products (API will now return dealer prices)
  final products = await getProducts(token: currentToken);
  
  // Update your state/provider
  productListController.updateProducts(products);
  
  // Show notification
  showSnackBar('Prices updated! You now see dealer pricing.');
}
```

---

### Step 5: Display Prices Based on Approval Status

Use the `can_access_dealer_pricing` flag to determine what to display:

```dart
Widget buildProductCard(Product product, User? user) {
  final isApprovedDealer = user?.canAccessDealerPricing ?? false;
  
  return Card(
    child: Column(
      children: [
        Text(product.name),
        
        // Price Display
        if (isApprovedDealer && product.dealerPrice != null)
          _buildDealerPriceView(product)  // Show dealer pricing
        else
          _buildRegularPriceView(product),  // Show retail pricing
          
        // Status Badge
        if (user?.isDealer == true && !isApprovedDealer)
          Chip(
            label: Text('Dealer Account Pending'),
            backgroundColor: Colors.orange.shade100,
          ),
          
        if (isApprovedDealer)
          Chip(
            label: Text('Dealer Price'),
            backgroundColor: Colors.green.shade100,
          ),
      ],
    ),
  );
}

Widget _buildDealerPriceView(Product product) {
  return Column(
    children: [
      Text(
        '₹${product.price.toStringAsFixed(2)}',
        style: TextStyle(
          fontSize: 20,
          fontWeight: FontWeight.bold,
          color: Colors.green,
        ),
      ),
      if (product.originalPrice != null && product.originalPrice! > product.price)
        Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              'MRP: ₹${product.originalPrice!.toStringAsFixed(2)}',
              style: TextStyle(
                decoration: TextDecoration.lineThrough,
                color: Colors.grey,
              ),
            ),
            SizedBox(width: 8),
            Text(
              'Save ₹${(product.originalPrice! - product.price).toStringAsFixed(2)}',
              style: TextStyle(color: Colors.orange),
            ),
          ],
        ),
    ],
  );
}

Widget _buildRegularPriceView(Product product) {
  return Column(
    children: [
      if (product.salePrice != null && product.salePrice! < product.price)
        Text(
          '₹${product.salePrice!.toStringAsFixed(2)}',
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
            color: Colors.red,
          ),
        )
      else
        Text(
          '₹${product.price.toStringAsFixed(2)}',
          style: TextStyle(
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
      if (product.salePrice != null)
        Text(
          'MRP: ₹${product.price.toStringAsFixed(2)}',
          style: TextStyle(
            decoration: TextDecoration.lineThrough,
            color: Colors.grey,
          ),
        ),
    ],
  );
}
```

---

## Complete Implementation Example

### State Management (Provider/Bloc Example)

```dart
class ProductProvider extends ChangeNotifier {
  List<Product> _products = [];
  User? _currentUser;
  bool _isLoading = false;
  
  List<Product> get products => _products;
  User? get currentUser => _currentUser;
  bool get isLoading => _isLoading;
  bool get isApprovedDealer => _currentUser?.canAccessDealerPricing ?? false;
  
  // Check user status
  Future<void> checkUserStatus(String token) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/api/v1/user'),
        headers: {'Authorization': 'Bearer $token'},
      );
      
      final data = jsonDecode(response.body);
      final wasApproved = isApprovedDealer;
      _currentUser = User.fromJson(data['data']['user']);
      
      // If status changed from pending to approved, refresh products
      if (!wasApproved && isApprovedDealer) {
        await refreshProducts(token);
        notifyListeners();
        // Show notification
        _showApprovalNotification();
      } else {
        notifyListeners();
      }
    } catch (e) {
      print('Error checking user status: $e');
    }
  }
  
  // Fetch products (with automatic pricing)
  Future<void> loadProducts(String? token) async {
    _isLoading = true;
    notifyListeners();
    
    try {
      final headers = <String, String>{};
      if (token != null) {
        headers['Authorization'] = 'Bearer $token';
      }
      
      final response = await http.get(
        Uri.parse('$baseUrl/api/v1/products'),
        headers: headers,
      );
      
      final data = jsonDecode(response.body);
      _products = (data['data']['data'] as List)
          .map((json) => Product.fromJson(json))
          .toList();
      
      _isLoading = false;
      notifyListeners();
    } catch (e) {
      _isLoading = false;
      notifyListeners();
      throw e;
    }
  }
  
  // Refresh products (called after approval)
  Future<void> refreshProducts(String token) async {
    await loadProducts(token);
  }
  
  void _showApprovalNotification() {
    // Show snackbar or dialog
    // "Your dealer account has been approved! You now see dealer prices."
  }
}
```

### Usage in Widget

```dart
class ProductsScreen extends StatefulWidget {
  @override
  _ProductsScreenState createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  Timer? _statusCheckTimer;
  
  @override
  void initState() {
    super.initState();
    _initialize();
  }
  
  Future<void> _initialize() async {
    final token = await getStoredToken();
    
    // Load initial products
    context.read<ProductProvider>().loadProducts(token);
    
    // Check user status
    context.read<ProductProvider>().checkUserStatus(token);
    
    // Poll status if dealer registration is pending
    final user = context.read<ProductProvider>().currentUser;
    if (user?.isDealer == true && !user!.canAccessDealerPricing) {
      _startStatusPolling(token);
    }
  }
  
  void _startStatusPolling(String token) {
    _statusCheckTimer = Timer.periodic(Duration(seconds: 30), (timer) async {
      await context.read<ProductProvider>().checkUserStatus(token);
      
      // Stop polling if approved
      if (context.read<ProductProvider>().isApprovedDealer) {
        timer.cancel();
        // Refresh products to show dealer prices
        await context.read<ProductProvider>().refreshProducts(token);
      }
    });
  }
  
  @override
  void dispose() {
    _statusCheckTimer?.cancel();
    super.dispose();
  }
  
  @override
  Widget build(BuildContext context) {
    return Consumer<ProductProvider>(
      builder: (context, provider, child) {
        if (provider.isLoading) {
          return CircularProgressIndicator();
        }
        
        return ListView.builder(
          itemCount: provider.products.length,
          itemBuilder: (context, index) {
            final product = provider.products[index];
            return ProductCard(
              product: product,
              user: provider.currentUser,
            );
          },
        );
      },
    );
  }
}
```

---

## API Response Examples

### Before Approval (Dealer Pending):

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 10,
      "role": "dealer",
      "is_dealer": true,
      "is_approved_dealer": false,
      "can_access_dealer_pricing": false,
      "dealer_registration": {
        "status": "pending"
      }
    }
  }
}
```

**Product Response:**
```json
{
  "id": 1,
  "name": "Tractor",
  "price": 100000.00,  // Retail price
  "original_price": 100000.00,
  "dealer_price": null,  // Hidden (null)
  "dealer_sale_price": null
}
```

### After Approval (Dealer Approved):

```json
{
  "success": true,
  "data": {
    "user": {
      "id": 10,
      "role": "dealer",
      "is_dealer": true,
      "is_approved_dealer": true,
      "can_access_dealer_pricing": true,  // ✅ Changed to true
      "dealer_registration": {
        "status": "approved"
      }
    }
  }
}
```

**Product Response:**
```json
{
  "id": 1,
  "name": "Tractor",
  "price": 75000.00,  // ✅ Automatically dealer price
  "original_price": 100000.00,  // Retail price for comparison
  "dealer_price": 75000.00,  // ✅ Now visible
  "dealer_sale_price": null
}
```

---

## Key Implementation Checklist

### ✅ Required Steps:

1. **Always send Auth Token**
   - [ ] Include `Authorization: Bearer {token}` header in product API calls
   - [ ] Without token, API always returns retail prices

2. **Check User Status**
   - [ ] Call `/api/v1/user` to get current approval status
   - [ ] Check `can_access_dealer_pricing` field
   - [ ] Store user status in your app state

3. **Poll Status When Pending**
   - [ ] If `dealer_registration.status == "pending"`, poll every 30 seconds
   - [ ] Stop polling when `can_access_dealer_pricing == true`

4. **Refresh Products After Approval**
   - [ ] When status changes to approved, refresh product list
   - [ ] Products API will automatically return dealer prices

5. **Display Prices Correctly**
   - [ ] Show dealer badge if `can_access_dealer_pricing == true`
   - [ ] Display `price` as dealer price when approved
   - [ ] Show `original_price` as retail (strikethrough) for comparison

6. **Handle UI Updates**
   - [ ] Show "Pending Approval" message when status is pending
   - [ ] Show notification when approved
   - [ ] Update prices immediately after approval

---

## Testing Flow

### Test Scenario: Dealer Registration & Price Switch

1. **Register as Dealer**
   ```dart
   POST /api/v1/register
   { "role": "dealer", ... }
   ```

2. **Login & Get Token**
   ```dart
   POST /api/v1/login
   → Save token
   ```

3. **Check User Status**
   ```dart
   GET /api/v1/user
   → can_access_dealer_pricing: false
   → dealer_registration.status: "not_submitted"
   ```

4. **Submit Dealer Registration**
   ```dart
   POST /api/v1/dealer/register
   → Status: "pending"
   ```

5. **Fetch Products (Before Approval)**
   ```dart
   GET /api/v1/products (with token)
   → price: 100000 (retail)
   → dealer_price: null
   ```

6. **Admin Approves (In Admin Panel)**
   - Admin clicks "Approve" on dealer registration
   - User's `is_dealer_approved` becomes `true`

7. **App Polls Status (Every 30 seconds)**
   ```dart
   GET /api/v1/user
   → can_access_dealer_pricing: true ✅
   ```

8. **App Refreshes Products**
   ```dart
   GET /api/v1/products (with token)
   → price: 75000 (dealer price) ✅
   → original_price: 100000 (retail)
   → dealer_price: 75000 ✅
   ```

9. **UI Updates**
   - Products show dealer prices
   - "Dealer Price" badge displayed
   - Savings amount shown

---

## Common Issues & Solutions

### Issue 1: Still seeing retail prices after approval

**Solution:**
- Make sure you're sending `Authorization: Bearer {token}` header
- Re-fetch products after approval status changes
- Check that `can_access_dealer_pricing` is `true` in user response

### Issue 2: Status not updating automatically

**Solution:**
- Implement polling every 30 seconds when pending
- Or add "Refresh" button for manual check
- Or check status on app resume/foreground

### Issue 3: Products not refreshing

**Solution:**
- Call product API again after detecting approval
- Clear product cache if using caching
- Update state management (Provider/Bloc) to refresh UI

---

## Summary

✅ **Backend is already set up** - API automatically returns correct prices based on user approval status

✅ **What you need to do:**
1. Always send auth token with product requests
2. Poll user status when dealer registration is pending
3. Refresh products when `can_access_dealer_pricing` becomes `true`
4. Display prices based on approval status

**The magic happens automatically** - API returns dealer prices when user is approved, you just need to refresh the product list!

---

**Last Updated:** October 30, 2025


