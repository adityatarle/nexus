# Flutter Developer Guide: Dealer Registration & Pricing APIs

## Table of Contents
1. [Overview](#overview)
2. [Dealer Registration Flow](#dealer-registration-flow)
3. [API Endpoints](#api-endpoints)
4. [Pricing Display Logic](#pricing-display-logic)
5. [Flutter Implementation Examples](#flutter-implementation-examples)
6. [Complete Flow Example](#complete-flow-example)

---

## Overview

The Nexus Agriculture API supports two user types:
- **Customer**: Regular users who see standard retail prices
- **Dealer**: Business users who can see dealer pricing (after approval)

### Key Concepts:
1. Users register with `role: "dealer"` to become dealers
2. After registration, dealers must submit a **Dealer Registration Form** with business details
3. Admin reviews and approves the registration
4. Once approved, dealers see **dealer pricing** instead of retail pricing
5. Product API automatically returns appropriate prices based on user's dealer status

---

## Dealer Registration Flow

### Step 1: User Registration (as Dealer)
First, register a user account with dealer role:

**Endpoint:** `POST /api/v1/register`

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@dealer.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "dealer",
  "phone": "+1234567890"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@dealer.com",
      "role": "dealer",
      "is_dealer": true,
      "is_approved_dealer": false,
      "can_access_dealer_pricing": false
    },
    "token": "1|xxxxxxxxxxxxx"
  }
}
```

**Note:** At this point, the user is a dealer but NOT approved yet, so they cannot see dealer pricing.

---

### Step 2: Submit Dealer Registration Form
After logging in with the token, submit business registration details:

**Endpoint:** `POST /api/v1/dealer/register`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Form Data:**
```
business_name: "ABC Trading Company"
gst_number: "27ABCDE1234F1Z5"
pan_number: "ABCDE1234F"
business_address: "123 Business Street"
business_city: "Mumbai"
business_state: "Maharashtra"
business_pincode: "400001"
business_country: "India"
contact_person: "John Doe"
contact_email: "contact@abctrading.com"
contact_phone: "+919876543210"
alternate_phone: "+919876543211" (optional)
company_website: "https://abctrading.com" (optional)
business_description: "We are a leading agricultural equipment supplier"
business_type: "Private Limited" (Individual|Partnership|Private Limited|Public Limited|LLP|Other)
years_in_business: 10 (optional)
annual_turnover: "5 Crores" (optional)
gst_certificate: [file] (optional, max 5MB, pdf/jpg/png)
pan_certificate: [file] (optional, max 5MB, pdf/jpg/png)
business_license: [file] (optional, max 5MB, pdf/jpg/png)
terms_accepted: true
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Dealer registration submitted successfully. Your application is under review.",
  "data": {
    "registration": {
      "id": 1,
      "business_name": "ABC Trading Company",
      "gst_number": "27ABCDE1234F1Z5",
      "status": "pending",
      "created_at": "2025-10-30T10:00:00.000000Z"
    }
  }
}
```

**Response (Already Submitted):**
```json
{
  "success": false,
  "message": "Dealer registration already submitted",
  "data": {
    "status": "pending",
    "registration": { ... }
  }
}
```

---

### Step 3: Check Registration Status
Check if dealer registration is approved:

**Endpoint:** `GET /api/v1/dealer/status`

**Headers:**
```
Authorization: Bearer {token}
```

**Response (Pending):**
```json
{
  "success": true,
  "data": {
    "is_registered": true,
    "is_approved": false,
    "status": "pending",
    "message": "Your dealer registration is under review. We will notify you once it is approved.",
    "registration": {
      "id": 1,
      "business_name": "ABC Trading Company",
      "status": "pending",
      ...
    }
  }
}
```

**Response (Approved):**
```json
{
  "success": true,
  "data": {
    "is_registered": true,
    "is_approved": true,
    "status": "approved",
    "message": "Your dealer registration has been approved. You can now access dealer pricing.",
    "registration": { ... }
  }
}
```

**Response (Not Submitted):**
```json
{
  "success": true,
  "data": {
    "is_registered": false,
    "is_approved": false,
    "status": "not_submitted",
    "message": "Dealer registration not submitted yet"
  }
}
```

---

## API Endpoints

### Authentication Endpoints

#### 1. Register User
```
POST /api/v1/register
```
- Set `role: "dealer"` to register as dealer
- Returns token for authentication

#### 2. Login
```
POST /api/v1/login
Body: { "email": "...", "password": "..." }
```
- Returns token for authentication

#### 3. Get Current User
```
GET /api/v1/user
Headers: Authorization: Bearer {token}
```
**Response includes dealer info:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "john@dealer.com",
      "role": "dealer",
      "is_dealer": true,
      "is_approved_dealer": false,  // true after admin approval
      "can_access_dealer_pricing": false,  // true after approval
      "dealer_registration": {
        "status": "pending",  // pending|approved|rejected|not_submitted
        "is_approved": false,
        "is_pending": true
      }
    }
  }
}
```

### Dealer Endpoints

#### 4. Submit Dealer Registration
```
POST /api/v1/dealer/register
Headers: Authorization: Bearer {token}
Content-Type: multipart/form-data
```

#### 5. Check Dealer Status
```
GET /api/v1/dealer/status
Headers: Authorization: Bearer {token}
```

### Product Endpoints

#### 6. Get Products (with pricing)
```
GET /api/v1/products
Headers: Authorization: Bearer {token} (optional - for dealer pricing)
```

**Product Response Structure:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Tractor Model X",
        "price": 50000.00,  // Current price user should pay
        "original_price": 50000.00,  // Original retail price
        "sale_price": null,  // Retail sale price (if any)
        "dealer_price": 40000.00,  // Only visible if can_access_dealer_pricing = true
        "dealer_sale_price": null,  // Only visible if approved dealer
        "discount_percentage": 0,
        ...
      }
    ]
  }
}
```

**Pricing Logic:**
- If `can_access_dealer_pricing = true`:
  - `price` = `dealer_sale_price ?? dealer_price ?? original_price`
  - `dealer_price` and `dealer_sale_price` are visible
- If `can_access_dealer_pricing = false`:
  - `price` = `sale_price ?? original_price`
  - `dealer_price` and `dealer_sale_price` are `null`

---

## Pricing Display Logic

### How Prices Work:

1. **Regular Customer:**
   - Sees: `price` (retail price)
   - May see: `sale_price` if product is on sale
   - `dealer_price` is always `null` (hidden)

2. **Dealer (Pending):**
   - Sees: `price` (retail price)
   - `dealer_price` is `null` (not approved yet)

3. **Dealer (Approved):**
   - Sees: `price` (dealer price, automatically calculated)
   - Can see: `dealer_price` and `dealer_sale_price`
   - Also sees: `original_price` (retail price for comparison)

### Flutter Display Logic:

```dart
Widget buildPrice(Product product, User? user) {
  bool isApprovedDealer = user?.canAccessDealerPricing ?? false;
  
  if (isApprovedDealer && product.dealerPrice != null) {
    // Show dealer pricing
    return Column(
      children: [
        Text(
          'Dealer Price: ₹${product.price}',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            color: Colors.green,
          ),
        ),
        if (product.originalPrice != null)
          Text(
            'Retail Price: ₹${product.originalPrice}',
            style: TextStyle(
              decoration: TextDecoration.lineThrough,
              color: Colors.grey,
            ),
          ),
        Text(
          'You Save: ₹${(product.originalPrice ?? 0) - product.price}',
          style: TextStyle(color: Colors.orange),
        ),
      ],
    );
  } else {
    // Show regular pricing
    return Column(
      children: [
        if (product.salePrice != null)
          Text(
            'Sale: ₹${product.salePrice}',
            style: TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.bold,
              color: Colors.red,
            ),
          ),
        Text(
          product.salePrice != null
              ? 'Regular: ₹${product.price}'
              : 'Price: ₹${product.price}',
          style: TextStyle(
            fontSize: 18,
            fontWeight: FontWeight.bold,
            decoration: product.salePrice != null
                ? TextDecoration.lineThrough
                : TextDecoration.none,
          ),
        ),
      ],
    );
  }
}
```

---

## Flutter Implementation Examples

### 1. Register as Dealer

```dart
Future<Map<String, dynamic>> registerAsDealer({
  required String name,
  required String email,
  required String password,
  required String phone,
}) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/v1/register'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'name': name,
      'email': email,
      'password': password,
      'password_confirmation': password,
      'role': 'dealer',  // Important: Set role as dealer
      'phone': phone,
    }),
  );

  final data = jsonDecode(response.body);
  if (data['success']) {
    // Save token
    String token = data['data']['token'];
    await saveToken(token);
    
    // Check dealer status
    User user = User.fromJson(data['data']['user']);
    return {
      'success': true,
      'token': token,
      'user': user,
    };
  } else {
    throw Exception(data['message'] ?? 'Registration failed');
  }
}
```

### 2. Submit Dealer Registration Form

```dart
Future<Map<String, dynamic>> submitDealerRegistration({
  required String token,
  required Map<String, dynamic> businessData,
  List<String>? documentPaths,  // File paths for documents
}) async {
  var request = http.MultipartRequest(
    'POST',
    Uri.parse('$baseUrl/api/v1/dealer/register'),
  );

  // Add headers
  request.headers['Authorization'] = 'Bearer $token';

  // Add form fields
  request.fields['business_name'] = businessData['business_name'];
  request.fields['gst_number'] = businessData['gst_number'];
  request.fields['pan_number'] = businessData['pan_number'];
  request.fields['business_address'] = businessData['business_address'];
  request.fields['business_city'] = businessData['business_city'];
  request.fields['business_state'] = businessData['business_state'];
  request.fields['business_pincode'] = businessData['business_pincode'];
  request.fields['contact_person'] = businessData['contact_person'];
  request.fields['contact_email'] = businessData['contact_email'];
  request.fields['contact_phone'] = businessData['contact_phone'];
  request.fields['business_description'] = businessData['business_description'];
  request.fields['business_type'] = businessData['business_type'];
  request.fields['terms_accepted'] = 'true';

  // Add optional fields
  if (businessData['alternate_phone'] != null) {
    request.fields['alternate_phone'] = businessData['alternate_phone'];
  }
  if (businessData['company_website'] != null) {
    request.fields['company_website'] = businessData['company_website'];
  }
  if (businessData['years_in_business'] != null) {
    request.fields['years_in_business'] = businessData['years_in_business'].toString();
  }

  // Add document files
  if (documentPaths != null) {
    // Assuming documentPaths is ordered: [gst, pan, license]
    if (documentPaths.length > 0 && documentPaths[0].isNotEmpty) {
      request.files.add(
        await http.MultipartFile.fromPath(
          'gst_certificate',
          documentPaths[0],
        ),
      );
    }
    if (documentPaths.length > 1 && documentPaths[1].isNotEmpty) {
      request.files.add(
        await http.MultipartFile.fromPath(
          'pan_certificate',
          documentPaths[1],
        ),
      );
    }
    if (documentPaths.length > 2 && documentPaths[2].isNotEmpty) {
      request.files.add(
        await http.MultipartFile.fromPath(
          'business_license',
          documentPaths[2],
        ),
      );
    }
  }

  // Send request
  var streamedResponse = await request.send();
  var response = await http.Response.fromStream(streamedResponse);
  var data = jsonDecode(response.body);

  if (data['success']) {
    return {
      'success': true,
      'message': data['message'],
      'registration': data['data']['registration'],
    };
  } else {
    throw Exception(data['message'] ?? 'Registration failed');
  }
}
```

### 3. Check Dealer Status

```dart
Future<Map<String, dynamic>> checkDealerStatus(String token) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/dealer/status'),
    headers: {
      'Authorization': 'Bearer $token',
    },
  );

  final data = jsonDecode(response.body);
  if (data['success']) {
    return {
      'is_registered': data['data']['is_registered'],
      'is_approved': data['data']['is_approved'],
      'status': data['data']['status'],  // pending|approved|rejected|not_submitted
      'message': data['data']['message'],
      'registration': data['data']['registration'],
    };
  } else {
    throw Exception(data['message'] ?? 'Failed to check status');
  }
}
```

### 4. Get User Info (Check Dealer Status)

```dart
Future<User> getCurrentUser(String token) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/user'),
    headers: {
      'Authorization': 'Bearer $token',
    },
  );

  final data = jsonDecode(response.body);
  if (data['success']) {
    return User.fromJson(data['data']['user']);
  } else {
    throw Exception('Failed to get user info');
  }
}

class User {
  final int id;
  final String name;
  final String email;
  final String role;
  final bool isDealer;
  final bool isApprovedDealer;
  final bool canAccessDealerPricing;
  final DealerRegistration? dealerRegistration;

  User({
    required this.id,
    required this.name,
    required this.email,
    required this.role,
    required this.isDealer,
    required this.isApprovedDealer,
    required this.canAccessDealerPricing,
    this.dealerRegistration,
  });

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      name: json['name'],
      email: json['email'],
      role: json['role'],
      isDealer: json['is_dealer'] ?? false,
      isApprovedDealer: json['is_approved_dealer'] ?? false,
      canAccessDealerPricing: json['can_access_dealer_pricing'] ?? false,
      dealerRegistration: json['dealer_registration'] != null
          ? DealerRegistration.fromJson(json['dealer_registration'])
          : null,
    );
  }
}

class DealerRegistration {
  final String status;
  final bool isApproved;
  final bool isPending;

  DealerRegistration({
    required this.status,
    required this.isApproved,
    required this.isPending,
  });

  factory DealerRegistration.fromJson(Map<String, dynamic> json) {
    return DealerRegistration(
      status: json['status'],
      isApproved: json['is_approved'] ?? false,
      isPending: json['is_pending'] ?? false,
    );
  }
}
```

### 5. Fetch Products with Dealer Pricing

```dart
Future<List<Product>> getProducts({
  String? token,  // Include token to get dealer pricing if approved
}) async {
  final headers = <String, String>{
    'Content-Type': 'application/json',
  };
  
  if (token != null) {
    headers['Authorization'] = 'Bearer $token';
  }

  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/products'),
    headers: headers,
  );

  final data = jsonDecode(response.body);
  if (data['success']) {
    List<Product> products = (data['data']['data'] as List)
        .map((json) => Product.fromJson(json))
        .toList();
    return products;
  } else {
    throw Exception('Failed to fetch products');
  }
}

class Product {
  final int id;
  final String name;
  final double price;  // Current price (dealer or retail)
  final double? originalPrice;  // Retail price
  final double? salePrice;  // Retail sale price
  final double? dealerPrice;  // Only if approved dealer
  final double? dealerSalePrice;  // Only if approved dealer

  Product({
    required this.id,
    required this.name,
    required this.price,
    this.originalPrice,
    this.salePrice,
    this.dealerPrice,
    this.dealerSalePrice,
  });

  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'],
      price: (json['price'] ?? 0).toDouble(),
      originalPrice: json['original_price'] != null
          ? (json['original_price'] as num).toDouble()
          : null,
      salePrice: json['sale_price'] != null
          ? (json['sale_price'] as num).toDouble()
          : null,
      dealerPrice: json['dealer_price'] != null
          ? (json['dealer_price'] as num).toDouble()
          : null,
      dealerSalePrice: json['dealer_sale_price'] != null
          ? (json['dealer_sale_price'] as num).toDouble()
          : null,
    );
  }
}
```

### 6. Display Price in UI

```dart
Widget buildProductPrice(Product product, User? user) {
  bool showDealerPrice = user?.canAccessDealerPricing ?? false;
  
  if (showDealerPrice && product.dealerPrice != null) {
    // Approved dealer - show dealer pricing
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(
          '₹${product.price.toStringAsFixed(2)}',
          style: TextStyle(
            fontSize: 24,
            fontWeight: FontWeight.bold,
            color: Colors.green,
          ),
        ),
        if (product.originalPrice != null && product.originalPrice! > product.price)
          Row(
            children: [
              Text(
                'MRP: ₹${product.originalPrice!.toStringAsFixed(2)}',
                style: TextStyle(
                  decoration: TextDecoration.lineThrough,
                  color: Colors.grey,
                  fontSize: 14,
                ),
              ),
              SizedBox(width: 8),
              Text(
                'Save ₹${(product.originalPrice! - product.price).toStringAsFixed(2)}',
                style: TextStyle(
                  color: Colors.orange,
                  fontSize: 14,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ],
          ),
        Container(
          padding: EdgeInsets.symmetric(horizontal: 8, vertical: 4),
          decoration: BoxDecoration(
            color: Colors.green.shade100,
            borderRadius: BorderRadius.circular(4),
          ),
          child: Text(
            'Dealer Price',
            style: TextStyle(
              color: Colors.green.shade700,
              fontSize: 12,
              fontWeight: FontWeight.bold,
            ),
          ),
        ),
      ],
    );
  } else {
    // Regular customer or pending dealer
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        if (product.salePrice != null && product.salePrice! < product.price)
          Text(
            '₹${product.salePrice!.toStringAsFixed(2)}',
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: Colors.red,
            ),
          )
        else
          Text(
            '₹${product.price.toStringAsFixed(2)}',
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
            ),
          ),
        if (product.salePrice != null && product.salePrice! < product.price)
          Text(
            'MRP: ₹${product.price.toStringAsFixed(2)}',
            style: TextStyle(
              decoration: TextDecoration.lineThrough,
              color: Colors.grey,
              fontSize: 14,
            ),
          ),
      ],
    );
  }
}
```

---

## Complete Flow Example

### Scenario: User wants to become a dealer and see dealer prices

```dart
class DealerFlowExample {
  final String baseUrl = 'https://nexus.heuristictechpark.com';
  
  // Step 1: Register as dealer
  Future<void> step1_RegisterAsDealer() async {
    try {
      var result = await registerAsDealer(
        name: 'John Doe',
        email: 'john@dealer.com',
        password: 'password123',
        phone: '+919876543210',
      );
      
      print('Registered! Token: ${result['token']}');
      print('Is Approved: ${result['user'].isApprovedDealer}');  // false
      print('Can Access Dealer Pricing: ${result['user'].canAccessDealerPricing}');  // false
      
      // User needs to submit dealer registration form
    } catch (e) {
      print('Error: $e');
    }
  }
  
  // Step 2: Submit dealer registration form
  Future<void> step2_SubmitDealerForm(String token) async {
    try {
      var result = await submitDealerRegistration(
        token: token,
        businessData: {
          'business_name': 'ABC Trading Company',
          'gst_number': '27ABCDE1234F1Z5',
          'pan_number': 'ABCDE1234F',
          'business_address': '123 Business Street',
          'business_city': 'Mumbai',
          'business_state': 'Maharashtra',
          'business_pincode': '400001',
          'contact_person': 'John Doe',
          'contact_email': 'contact@abctrading.com',
          'contact_phone': '+919876543210',
          'business_description': 'We supply agricultural equipment',
          'business_type': 'Private Limited',
        },
        documentPaths: [
          '/path/to/gst_certificate.pdf',
          '/path/to/pan_certificate.pdf',
          '/path/to/business_license.pdf',
        ],
      );
      
      print('Form submitted! Status: ${result['registration']['status']}');  // "pending"
      
      // Now wait for admin approval
    } catch (e) {
      print('Error: $e');
    }
  }
  
  // Step 3: Check status periodically
  Future<void> step3_CheckStatus(String token) async {
    try {
      var status = await checkDealerStatus(token);
      
      if (status['status'] == 'pending') {
        print('Status: Under review');
        print('Message: ${status['message']}');
      } else if (status['status'] == 'approved') {
        print('Status: APPROVED! 🎉');
        print('You can now see dealer prices!');
        
        // Refresh user info to get updated can_access_dealer_pricing
        User user = await getCurrentUser(token);
        print('Can Access Dealer Pricing: ${user.canAccessDealerPricing}');  // true
        
      } else if (status['status'] == 'rejected') {
        print('Status: REJECTED');
        print('Reason: ${status['registration']['rejection_reason']}');
      }
    } catch (e) {
      print('Error: $e');
    }
  }
  
  // Step 4: Fetch products with dealer pricing
  Future<void> step4_FetchProducts(String token) async {
    try {
      // First get user to check if approved
      User user = await getCurrentUser(token);
      
      // Fetch products (prices will be dealer prices if approved)
      List<Product> products = await getProducts(token: token);
      
      for (var product in products) {
        print('Product: ${product.name}');
        
        if (user.canAccessDealerPricing && product.dealerPrice != null) {
          print('Dealer Price: ₹${product.price}');
          print('Retail Price: ₹${product.originalPrice}');
          print('You Save: ₹${(product.originalPrice ?? 0) - product.price}');
        } else {
          print('Price: ₹${product.price}');
          print('(Dealer price not available - pending approval)');
        }
      }
    } catch (e) {
      print('Error: $e');
    }
  }
}
```

---

## Summary Checklist for Flutter Developer

### Registration Flow:
- [ ] User registers with `role: "dealer"` via `/api/v1/register`
- [ ] After login, check user's `can_access_dealer_pricing` (should be `false`)
- [ ] Show dealer registration form if `dealer_registration.status == "not_submitted"`
- [ ] Submit form via `/api/v1/dealer/register` (multipart/form-data)
- [ ] Show "pending" status and message
- [ ] Periodically check status via `/api/v1/dealer/status` or `/api/v1/user`

### Pricing Display:
- [ ] Always include `Authorization: Bearer {token}` header when fetching products
- [ ] Check `user.can_access_dealer_pricing` to determine which prices to show
- [ ] If `true`: Show `price` as dealer price, show `original_price` as retail (strikethrough)
- [ ] If `false`: Show `price` as retail price, hide dealer price fields
- [ ] Show "Dealer Price" badge for approved dealers
- [ ] Show savings amount for dealers

### Key Fields to Check:
- `user.is_dealer`: User registered as dealer?
- `user.is_approved_dealer`: Admin approved the registration?
- `user.can_access_dealer_pricing`: Can see dealer prices? (true = show dealer prices)
- `user.dealer_registration.status`: "not_submitted" | "pending" | "approved" | "rejected"
- `product.price`: Current price (dealer or retail, automatically calculated)
- `product.dealer_price`: Only visible if `can_access_dealer_pricing = true`
- `product.original_price`: Retail price (visible to dealers for comparison)

---

## Testing

### Test Scenarios:

1. **Register as Customer:**
   - `role: "customer"` → No dealer features available

2. **Register as Dealer (not approved):**
   - `role: "dealer"` → Can submit registration form
   - Before approval → Sees retail prices
   - After approval → Sees dealer prices automatically

3. **Dealer Registration Statuses:**
   - `not_submitted`: Show registration form
   - `pending`: Show "Under review" message
   - `approved`: Enable dealer pricing
   - `rejected`: Show rejection reason

---

## Base URL

```
Production: https://nexus.heuristictechpark.com
Development: http://localhost/nexus/nexus/public
```

---

## Support

For issues or questions, contact the backend development team or refer to the API documentation.

**Last Updated:** October 30, 2025

