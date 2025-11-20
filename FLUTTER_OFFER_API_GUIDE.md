# Flutter Offer API Guide

Complete guide for integrating Offers API in your Flutter application.

## Base URL
```
https://your-domain.com/api/v1
```

## Authentication
Most offer endpoints are public, but some features require authentication for personalized offers (dealers vs customers).

Include Bearer token in headers when authenticated:
```dart
headers: {
  'Authorization': 'Bearer $token',
  'Accept': 'application/json',
}
```

---

## API Endpoints

### 1. Get All Active Offers

**Endpoint:** `GET /offers`

**Description:** Get all active and valid offers available to the user.

**Query Parameters:**
- `type` (optional): Filter by offer type (`general`, `product`, `category`, `subcategory`)
- `featured` (optional): Filter featured offers only (`true`/`false`)

**Example Request:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/offers?featured=true'),
  headers: {
    'Accept': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  },
);
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Summer Sale - 20% Off",
      "slug": "summer-sale-20-off",
      "description": "Get 20% off on all products",
      "banner_image": "https://your-domain.com/storage/offers/banner.jpg",
      "offer_type": "general",
      "discount_type": "percentage",
      "discount_value": 20.0,
      "min_purchase_amount": 1000.0,
      "min_quantity": null,
      "start_date": "2025-11-01T00:00:00Z",
      "end_date": "2025-11-30T23:59:59Z",
      "max_uses": 1000,
      "max_uses_per_user": 1,
      "used_count": 150,
      "is_featured": true,
      "terms_conditions": "Valid until end of month",
      "for_customers": true,
      "for_dealers": false,
      "days_remaining": 17,
      "is_valid": true
    }
  ],
  "count": 1
}
```

**Dart Model:**
```dart
class Offer {
  final int id;
  final String title;
  final String slug;
  final String? description;
  final String? bannerImage;
  final String offerType; // 'general', 'product', 'category', 'subcategory'
  final String discountType; // 'percentage', 'fixed'
  final double discountValue;
  final double? minPurchaseAmount;
  final int? minQuantity;
  final DateTime startDate;
  final DateTime endDate;
  final int? maxUses;
  final int? maxUsesPerUser;
  final int usedCount;
  final bool isFeatured;
  final String? termsConditions;
  final bool forCustomers;
  final bool forDealers;
  final int daysRemaining;
  final bool isValid;
  final ProductInfo? product;
  final CategoryInfo? category;
  final SubcategoryInfo? subcategory;

  Offer({
    required this.id,
    required this.title,
    required this.slug,
    this.description,
    this.bannerImage,
    required this.offerType,
    required this.discountType,
    required this.discountValue,
    this.minPurchaseAmount,
    this.minQuantity,
    required this.startDate,
    required this.endDate,
    this.maxUses,
    this.maxUsesPerUser,
    required this.usedCount,
    required this.isFeatured,
    this.termsConditions,
    required this.forCustomers,
    required this.forDealers,
    required this.daysRemaining,
    required this.isValid,
    this.product,
    this.category,
    this.subcategory,
  });

  factory Offer.fromJson(Map<String, dynamic> json) {
    return Offer(
      id: json['id'],
      title: json['title'],
      slug: json['slug'],
      description: json['description'],
      bannerImage: json['banner_image'],
      offerType: json['offer_type'],
      discountType: json['discount_type'],
      discountValue: (json['discount_value'] as num).toDouble(),
      minPurchaseAmount: json['min_purchase_amount'] != null 
          ? (json['min_purchase_amount'] as num).toDouble() 
          : null,
      minQuantity: json['min_quantity'],
      startDate: DateTime.parse(json['start_date']),
      endDate: DateTime.parse(json['end_date']),
      maxUses: json['max_uses'],
      maxUsesPerUser: json['max_uses_per_user'],
      usedCount: json['used_count'],
      isFeatured: json['is_featured'],
      termsConditions: json['terms_conditions'],
      forCustomers: json['for_customers'],
      forDealers: json['for_dealers'],
      daysRemaining: json['days_remaining'],
      isValid: json['is_valid'],
      product: json['product'] != null 
          ? ProductInfo.fromJson(json['product']) 
          : null,
      category: json['category'] != null 
          ? CategoryInfo.fromJson(json['category']) 
          : null,
      subcategory: json['subcategory'] != null 
          ? SubcategoryInfo.fromJson(json['subcategory']) 
          : null,
    );
  }

  // Helper method to calculate discount
  double calculateDiscount(double amount) {
    if (discountType == 'percentage') {
      return (amount * discountValue) / 100;
    } else {
      return discountValue > amount ? amount : discountValue;
    }
  }

  // Helper method to get discounted price
  double getDiscountedPrice(double originalPrice) {
    final discount = calculateDiscount(originalPrice);
    return (originalPrice - discount).clamp(0.0, double.infinity);
  }
}

class ProductInfo {
  final int id;
  final String name;
  final String slug;
  final String image;
  final double price;
  final double currentPrice;

  ProductInfo({
    required this.id,
    required this.name,
    required this.slug,
    required this.image,
    required this.price,
    required this.currentPrice,
  });

  factory ProductInfo.fromJson(Map<String, dynamic> json) {
    return ProductInfo(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      image: json['image'],
      price: (json['price'] as num).toDouble(),
      currentPrice: (json['current_price'] as num).toDouble(),
    );
  }
}

class CategoryInfo {
  final int id;
  final String name;
  final String slug;
  final String? image;

  CategoryInfo({
    required this.id,
    required this.name,
    required this.slug,
    this.image,
  });

  factory CategoryInfo.fromJson(Map<String, dynamic> json) {
    return CategoryInfo(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      image: json['image'],
    );
  }
}

class SubcategoryInfo {
  final int id;
  final String name;
  final String slug;
  final String? image;

  SubcategoryInfo({
    required this.id,
    required this.name,
    required this.slug,
    this.image,
  });

  factory SubcategoryInfo.fromJson(Map<String, dynamic> json) {
    return SubcategoryInfo(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      image: json['image'],
    );
  }
}
```

---

### 2. Get Single Offer

**Endpoint:** `GET /offers/{id}`

**Description:** Get details of a specific offer.

**Example Request:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/offers/1'),
  headers: {
    'Accept': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  },
);
```

**Response:** Same structure as offer object in list endpoint.

---

### 3. Get Offers for Specific Product

**Endpoint:** `GET /offers/product/{productId}`

**Description:** Get all applicable offers for a specific product.

**Example Request:**
```dart
final response = await http.get(
  Uri.parse('$baseUrl/offers/product/24'),
  headers: {
    'Accept': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  },
);
```

**Response:** Array of offers applicable to the product.

---

### 4. Calculate Discount

**Endpoint:** `POST /offers/calculate-discount`

**Description:** Calculate the best discount available for a product/order.

**Request Body:**
```json
{
  "product_id": 24,
  "quantity": 2,
  "amount": 2000.0
}
```

**Example Request:**
```dart
final response = await http.post(
  Uri.parse('$baseUrl/offers/calculate-discount'),
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  },
  body: jsonEncode({
    'product_id': 24,
    'quantity': 2,
    'amount': 2000.0,
  }),
);
```

**Response:**
```json
{
  "success": true,
  "data": {
    "original_amount": 2000.0,
    "discount_amount": 400.0,
    "final_amount": 1600.0,
    "discount_percentage": 20.0,
    "best_offer": {
      "id": 1,
      "title": "Summer Sale - 20% Off",
      // ... full offer object
    }
  }
}
```

**Dart Model:**
```dart
class DiscountCalculation {
  final double originalAmount;
  final double discountAmount;
  final double finalAmount;
  final double discountPercentage;
  final Offer? bestOffer;

  DiscountCalculation({
    required this.originalAmount,
    required this.discountAmount,
    required this.finalAmount,
    required this.discountPercentage,
    this.bestOffer,
  });

  factory DiscountCalculation.fromJson(Map<String, dynamic> json) {
    return DiscountCalculation(
      originalAmount: (json['original_amount'] as num).toDouble(),
      discountAmount: (json['discount_amount'] as num).toDouble(),
      finalAmount: (json['final_amount'] as num).toDouble(),
      discountPercentage: (json['discount_percentage'] as num).toDouble(),
      bestOffer: json['best_offer'] != null 
          ? Offer.fromJson(json['best_offer']) 
          : null,
    );
  }
}
```

---

## Complete Flutter Service Example

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

class OfferService {
  final String baseUrl;
  final String? token;

  OfferService({required this.baseUrl, this.token});

  Map<String, String> get _headers => {
    'Accept': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  };

  // Get all offers
  Future<List<Offer>> getOffers({
    String? type,
    bool? featured,
  }) async {
    final queryParams = <String, String>{};
    if (type != null) queryParams['type'] = type;
    if (featured != null) queryParams['featured'] = featured.toString();

    final uri = Uri.parse('$baseUrl/offers').replace(
      queryParameters: queryParams.isNotEmpty ? queryParams : null,
    );

    final response = await http.get(uri, headers: _headers);

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        final offers = (data['data'] as List)
            .map((json) => Offer.fromJson(json))
            .toList();
        return offers;
      }
    }
    throw Exception('Failed to load offers');
  }

  // Get single offer
  Future<Offer> getOffer(int id) async {
    final response = await http.get(
      Uri.parse('$baseUrl/offers/$id'),
      headers: _headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return Offer.fromJson(data['data']);
      }
    }
    throw Exception('Failed to load offer');
  }

  // Get offers for product
  Future<List<Offer>> getOffersForProduct(int productId) async {
    final response = await http.get(
      Uri.parse('$baseUrl/offers/product/$productId'),
      headers: _headers,
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return (data['data'] as List)
            .map((json) => Offer.fromJson(json))
            .toList();
      }
    }
    throw Exception('Failed to load offers for product');
  }

  // Calculate discount
  Future<DiscountCalculation> calculateDiscount({
    required int productId,
    int? quantity,
    double? amount,
  }) async {
    final body = {
      'product_id': productId,
      if (quantity != null) 'quantity': quantity,
      if (amount != null) 'amount': amount,
    };

    final response = await http.post(
      Uri.parse('$baseUrl/offers/calculate-discount'),
      headers: {
        ..._headers,
        'Content-Type': 'application/json',
      },
      body: jsonEncode(body),
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return DiscountCalculation.fromJson(data['data']);
      }
    }
    throw Exception('Failed to calculate discount');
  }
}
```

---

## UI Implementation Example

```dart
import 'package:flutter/material.dart';

class OffersScreen extends StatefulWidget {
  @override
  _OffersScreenState createState() => _OffersScreenState();
}

class _OffersScreenState extends State<OffersScreen> {
  final OfferService _offerService = OfferService(
    baseUrl: 'https://your-domain.com/api/v1',
    token: 'your-auth-token', // Optional
  );

  List<Offer> _offers = [];
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadOffers();
  }

  Future<void> _loadOffers() async {
    try {
      final offers = await _offerService.getOffers(featured: true);
      setState(() {
        _offers = offers;
        _isLoading = false;
      });
    } catch (e) {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text('Error loading offers: $e')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return Center(child: CircularProgressIndicator());
    }

    return ListView.builder(
      itemCount: _offers.length,
      itemBuilder: (context, index) {
        final offer = _offers[index];
        return OfferCard(offer: offer);
      },
    );
  }
}

class OfferCard extends StatelessWidget {
  final Offer offer;

  const OfferCard({required this.offer});

  @override
  Widget build(BuildContext context) {
    return Card(
      margin: EdgeInsets.all(8),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          if (offer.bannerImage != null)
            Image.network(
              offer.bannerImage!,
              width: double.infinity,
              height: 200,
              fit: BoxFit.cover,
            ),
          Padding(
            padding: EdgeInsets.all(16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    Expanded(
                      child: Text(
                        offer.title,
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                    if (offer.isFeatured)
                      Chip(
                        label: Text('Featured'),
                        backgroundColor: Colors.amber,
                      ),
                  ],
                ),
                if (offer.description != null)
                  SizedBox(height: 8),
                if (offer.description != null)
                  Text(offer.description!),
                SizedBox(height: 12),
                Row(
                  children: [
                    Container(
                      padding: EdgeInsets.symmetric(
                        horizontal: 12,
                        vertical: 6,
                      ),
                      decoration: BoxDecoration(
                        color: Colors.green,
                        borderRadius: BorderRadius.circular(4),
                      ),
                      child: Text(
                        offer.discountType == 'percentage'
                            ? '${offer.discountValue.toInt()}% OFF'
                            : '₹${offer.discountValue.toInt()} OFF',
                        style: TextStyle(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                    ),
                    SizedBox(width: 8),
                    Text(
                      '${offer.daysRemaining} days left',
                      style: TextStyle(color: Colors.grey),
                    ),
                  ],
                ),
                if (offer.termsConditions != null) ...[
                  SizedBox(height: 8),
                  Text(
                    'Terms: ${offer.termsConditions}',
                    style: TextStyle(
                      fontSize: 12,
                      color: Colors.grey[600],
                    ),
                  ),
                ],
              ],
            ),
          ),
        ],
      ),
    );
  }
}
```

---

## Error Handling

All endpoints return standard error responses:

```json
{
  "success": false,
  "message": "Error message here"
}
```

**Status Codes:**
- `200`: Success
- `404`: Offer not found
- `403`: Offer not available for your account type
- `422`: Validation error
- `500`: Server error

---

## Best Practices

1. **Cache Offers:** Cache offers locally to reduce API calls
2. **Check Validity:** Always check `is_valid` and `days_remaining` before displaying
3. **User Type:** Filter offers based on user type (customer/dealer)
4. **Calculate Discounts:** Use the calculate-discount endpoint for accurate pricing
5. **Handle Expiry:** Update UI when offers expire
6. **Image Loading:** Use cached network images for banner images

---

## Offer Types Explained

- **general**: Applies to all products
- **product**: Applies to a specific product
- **category**: Applies to all products in a category
- **subcategory**: Applies to all products in a subcategory

---

## Discount Types

- **percentage**: Discount is a percentage (e.g., 20% = 20.0)
- **fixed**: Discount is a fixed amount in ₹ (e.g., ₹100 = 100.0)

---

## Priority System

Offers with higher `priority` values are applied first. If multiple offers apply, the one with the highest discount amount is selected.

---

For more information, contact the development team or refer to the main API documentation.


