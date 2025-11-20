# Flutter API Integration Guide - Nexus Agriculture

## Table of Contents
1. [Overview](#overview)
2. [Authentication](#authentication)
3. [Order/Inquiry Workflow](#orderinquiry-workflow)
4. [API Endpoints](#api-endpoints)
5. [Request/Response Formats](#requestresponse-formats)
6. [Status Codes](#status-codes)
7. [Error Handling](#error-handling)
8. [Image Handling](#image-handling)
9. [Complete Workflow Example](#complete-workflow-example)
10. [Best Practices](#best-practices)

---

## Overview

The Nexus Agriculture API uses an **inquiry-based order system**. This means:

- **No payment integration required** - Orders are created as inquiries
- **Admin follow-up** - Admin will contact customers to confirm order details
- **Status tracking** - Orders can be tracked through various statuses
- **Stock management** - Stock is NOT reduced until admin confirms the order

### Key Concepts

- **Inquiry**: Initial order submission (no payment required)
- **Order Status**: `inquiry`, `pending`, `processing`, `shipped`, `delivered`, `cancelled`
- **Payment Status**: `not_required` (for inquiries), `pending`, `paid`, `failed`, `refunded`

---

## Authentication

All API endpoints require authentication using **Bearer Token**.

### Headers Required
```
Authorization: Bearer {access_token}
Accept: application/json
Content-Type: application/json
```

### Getting Access Token

**Endpoint**: `POST /api/v1/login`

**Request**:
```json
{
  "email": "customer@example.com",
  "password": "password123"
}
```

**Response**:
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "John Doe",
      "email": "customer@example.com",
      "role": "customer"
    },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGc..."
  }
}
```

**Store the token** and include it in all subsequent requests.

---

## Order/Inquiry Workflow

### Complete Flow

1. **Browse Products** → Get product list
2. **Add to Cart** → Add products to cart
3. **View Cart** → Review cart items
4. **Create Inquiry** → Submit order as inquiry
5. **View Orders** → Check order status
6. **Order Details** → View full order information

### Status Flow

```
inquiry → pending → processing → shipped → delivered
                ↓
            cancelled
```

---

## API Endpoints

### Base URL
```
https://your-domain.com/api/v1
```

**Note**: All endpoints use the `/api/v1` prefix.

### 1. Get Products

**Endpoint**: `GET /api/v1/products`

**Query Parameters**:
- `category_id` (optional): Filter by category
- `subcategory_id` (optional): Filter by subcategory
- `search` (optional): Search products
- `brand` (optional): Filter by brand
- `power_source` (optional): Filter by power source
- `min_price` (optional): Minimum price filter
- `max_price` (optional): Maximum price filter
- `featured` (optional): Filter featured products (true/false)
- `sort` (optional): Sort by (`name`, `price_low`, `price_high`, `newest`)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Response**:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name": "Tractor Model X",
        "slug": "tractor-model-x",
        "sku": "TRC-001",
        "description": "High-quality agricultural tractor",
        "short_description": "Powerful farming equipment",
        "price": 50000.00,
        "original_price": 50000.00,
        "sale_price": null,
        "dealer_price": 45000.00,
        "dealer_sale_price": null,
        "discount_percentage": 0,
        "stock_quantity": 10,
        "in_stock": true,
        "is_featured": false,
        "image": "https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890",
        "images": [
          "https://your-domain.com/storage/products/gallery/tractor-1.jpg?v=1234567890"
        ],
        "brand": "John Deere",
        "model": "Model X",
        "power_source": "Diesel",
        "warranty": "2 Years",
        "weight": 1500.50,
        "dimensions": "200x100x150",
        "category": {
          "id": 1,
          "name": "Tractors",
          "slug": "tractors"
        },
        "subcategory": {
          "id": 1,
          "name": "Heavy Duty Tractors",
          "slug": "tractors-heavy-duty-tractors"
        },
        "created_at": "2025-11-08T10:00:00.000000Z",
        "updated_at": "2025-11-08T10:00:00.000000Z"
      }
    ],
    "total": 50,
    "per_page": 15
  }
}
```

### 2. Get Categories

**Endpoint**: `GET /api/v1/categories`

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Tractors",
      "slug": "tractors",
      "description": "Agricultural tractors and equipment",
      "image": "https://your-domain.com/storage/categories/tractors.jpg",
      "products_count": 25,
      "subcategories": [
        {
          "id": 1,
          "name": "Heavy Duty Tractors",
          "slug": "tractors-heavy-duty-tractors",
          "description": "Heavy duty agricultural tractors",
          "image": "https://your-domain.com/storage/subcategories/heavy-duty.jpg"
        },
        {
          "id": 2,
          "name": "Compact Tractors",
          "slug": "tractors-compact-tractors",
          "description": "Compact tractors for small farms",
          "image": null
        }
      ]
    }
  ]
}
```

### 3. Get Single Category with Products

**Endpoint**: `GET /api/v1/categories/{categorySlug}`

**Query Parameters**:
- `sort` (optional): Sort by (`name`, `price_low`, `price_high`, `newest`)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Response**:
```json
{
  "success": true,
  "data": {
    "category": {
      "id": 1,
      "name": "Tractors",
      "slug": "tractors",
      "description": "Agricultural tractors",
      "image": "https://your-domain.com/storage/categories/tractors.jpg"
    },
    "products": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "name": "Tractor Model X",
          "price": 50000.00,
          "image": "https://your-domain.com/storage/products/primary/tractor.jpg",
          "category": {
            "id": 1,
            "name": "Tractors"
          },
          "subcategory": {
            "id": 1,
            "name": "Heavy Duty Tractors"
          }
        }
      ]
    }
  }
}
```

### 4. Get Subcategories

**Endpoint**: `GET /api/v1/subcategories`

**Query Parameters**:
- `category_id` (optional): Filter subcategories by category

**Response**:
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Heavy Duty Tractors",
      "slug": "tractors-heavy-duty-tractors",
      "description": "Heavy duty agricultural tractors",
      "image": "https://your-domain.com/storage/subcategories/heavy-duty.jpg",
      "category_id": 1,
      "category": {
        "id": 1,
        "name": "Tractors",
        "slug": "tractors"
      },
      "products_count": 15
    }
  ]
}
```

### 5. Get Single Subcategory with Products

**Endpoint**: `GET /api/v1/subcategories/{subcategorySlug}`

**Query Parameters**:
- `sort` (optional): Sort by (`name`, `price_low`, `price_high`, `newest`)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Response**:
```json
{
  "success": true,
  "data": {
    "subcategory": {
      "id": 1,
      "name": "Heavy Duty Tractors",
      "slug": "tractors-heavy-duty-tractors",
      "description": "Heavy duty agricultural tractors",
      "image": "https://your-domain.com/storage/subcategories/heavy-duty.jpg",
      "category_id": 1,
      "category": {
        "id": 1,
        "name": "Tractors",
        "slug": "tractors"
      }
    },
    "products": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "name": "Tractor Model X",
          "price": 50000.00,
          "image": "https://your-domain.com/storage/products/primary/tractor.jpg",
          "category": {
            "id": 1,
            "name": "Tractors"
          },
          "subcategory": {
            "id": 1,
            "name": "Heavy Duty Tractors"
          }
        }
      ]
    }
  }
}
```

### 6. Search Products

**Endpoint**: `GET /api/v1/products/search`

**Query Parameters**:
- `q` (required): Search query (minimum 2 characters)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Response**: Same format as Get Products endpoint.

### 7. Get Featured Products

**Endpoint**: `GET /api/v1/products/featured`

**Query Parameters**:
- `limit` (optional): Number of products (default: 10)

**Response**: Array of products (same format as Get Products).

### 8. Get Single Product

**Endpoint**: `GET /api/v1/products/{productSlug}`

**Response**:
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Tractor Model X",
    "slug": "tractor-model-x",
    "description": "High-quality agricultural tractor with advanced features",
    "short_description": "Powerful farming equipment",
    "sku": "TRC-001",
    "price": 50000.00,
    "original_price": 50000.00,
    "sale_price": null,
    "dealer_price": 45000.00,
    "dealer_sale_price": null,
    "discount_percentage": 0,
    "stock_quantity": 10,
    "in_stock": true,
    "is_featured": false,
    "image": "https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890",
    "images": [
      "https://your-domain.com/storage/products/gallery/tractor-1.jpg?v=1234567890",
      "https://your-domain.com/storage/products/gallery/tractor-2.jpg?v=1234567890"
    ],
    "brand": "John Deere",
    "model": "Model X",
    "power_source": "Diesel",
    "warranty": "2 Years",
    "weight": 1500.50,
    "dimensions": "200x100x150",
    "category": {
      "id": 1,
      "name": "Tractors",
      "slug": "tractors"
    },
    "subcategory": {
      "id": 1,
      "name": "Heavy Duty Tractors",
      "slug": "tractors-heavy-duty-tractors"
    },
    "related_products": [
      {
        "id": 2,
        "name": "Tractor Model Y",
        "price": 55000.00,
        "image": "https://your-domain.com/storage/products/primary/tractor-y.jpg"
      }
    ],
    "created_at": "2025-11-08T10:00:00.000000Z",
    "updated_at": "2025-11-08T10:00:00.000000Z"
  }
}
```

### 9. Add to Cart

**Endpoint**: `POST /api/v1/cart/add`

**Request**:
```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Response**:
```json
{
  "success": true,
  "message": "Product added to cart",
  "data": {
    "cart_id": 1,
    "items": [
      {
        "product_id": 1,
        "product_name": "Tractor Model X",
        "quantity": 2,
        "price": 50000.00,
        "total": 100000.00,
        "image": "https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890"
      }
    ],
    "subtotal": 100000.00,
    "total": 100000.00
  }
}
```

### 10. Get Cart

**Endpoint**: `GET /api/v1/cart`

**Response**:
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "product_id": 1,
        "product_name": "Tractor Model X",
        "quantity": 2,
        "price": 50000.00,
        "total": 100000.00,
        "image": "https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890"
      }
    ],
    "subtotal": 100000.00,
    "tax": 8000.00,
    "shipping": 25.00,
    "total": 108025.00
  }
}
```

### 11. Create Order (Inquiry)

**Endpoint**: `POST /api/v1/orders`

**Important**: This creates an **inquiry**, not a paid order. No payment is required.

**Request**:
```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+1234567890",
  "billing_address": "123 Main Street, City, State, ZIP",
  "shipping_address": "123 Main Street, City, State, ZIP",
  "notes": "Please call before delivery"
}
```

**Note**: 
- `customer_phone` is **required** (for admin follow-up)
- `shipping_address` is optional (defaults to billing_address)
- `notes` is optional

**Response**:
```json
{
  "success": true,
  "message": "Inquiry received! We will contact you shortly to confirm your order.",
  "data": {
    "order_number": "AGR-T5BSBQTM",
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "+1234567890",
    "billing_address": {
      "address": "123 Main Street, City, State, ZIP"
    },
    "shipping_address": {
      "address": "123 Main Street, City, State, ZIP"
    },
    "subtotal": 100000.00,
    "tax_amount": 8000.00,
    "shipping_amount": 25.00,
    "total_amount": 108025.00,
    "payment_method": "inquiry",
    "payment_status": "not_required",
    "order_status": "inquiry",
    "is_inquiry": true,
    "notes": "Please call before delivery",
    "items": [
      {
        "product_id": 1,
        "product_name": "Tractor Model X",
        "product_sku": "TRC-001",
        "quantity": 2,
        "price": 50000.00,
        "total": 100000.00,
        "image": "https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890"
      }
    ],
    "created_at": "2025-11-08T10:30:00.000000Z",
    "updated_at": "2025-11-08T10:30:00.000000Z"
  }
}
```

**Key Fields**:
- `order_status: "inquiry"` - Order is an inquiry
- `payment_status: "not_required"` - No payment needed
- `is_inquiry: true` - Helper flag for UI
- `order_number` - Unique identifier (save this!)

### 12. Get User Orders

**Endpoint**: `GET /api/v1/orders`

**Query Parameters**:
- `status` (optional): Filter by status (`inquiry`, `pending`, `processing`, `shipped`, `delivered`, `cancelled`)
- `payment_status` (optional): Filter by payment status (`not_required`, `pending`, `paid`, `failed`, `refunded`)
- `per_page` (optional): Items per page (default: 15)
- `page` (optional): Page number

**Response**:
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "order_number": "AGR-T5BSBQTM",
        "customer_name": "John Doe",
        "customer_email": "john@example.com",
        "customer_phone": "+1234567890",
        "billing_address": {
      "address": "123 Main Street, City, State, ZIP"
    },
        "shipping_address": {
      "address": "123 Main Street, City, State, ZIP"
    },
        "subtotal": 100000.00,
        "tax_amount": 8000.00,
        "shipping_amount": 25.00,
        "total_amount": 108025.00,
        "payment_method": "inquiry",
        "payment_status": "not_required",
        "order_status": "inquiry",
        "is_inquiry": true,
        "notes": null,
        "items": [
          {
            "product_id": 1,
            "product_name": "Tractor Model X",
            "product_sku": "TRC-001",
            "quantity": 2,
            "price": 50000.00,
            "total": 100000.00,
            "image": "https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890"
          }
        ],
        "created_at": "2025-11-08T10:30:00.000000Z",
        "updated_at": "2025-11-08T10:30:00.000000Z"
      }
    ],
    "total": 1,
    "per_page": 15
  }
}
```

### 13. Get Single Order

**Endpoint**: `GET /api/v1/orders/{orderNumber}`

**Example**: `GET /api/v1/orders/AGR-T5BSBQTM`

**Response**: Same format as order item in list above.

### 14. Get Order Invoice Data

**Endpoint**: `GET /api/v1/orders/{orderNumber}/invoice`

**Response**: Same format as single order (returns order data for invoice generation).

---

## Request/Response Formats

### Standard Success Response
```json
{
  "success": true,
  "message": "Optional message",
  "data": { ... }
}
```

### Standard Error Response
```json
{
  "success": false,
  "message": "Error message",
  "errors": {
    "field_name": ["Error message for this field"]
  }
}
```

### Pagination Format
```json
{
  "current_page": 1,
  "data": [ ... ],
  "first_page_url": "https://...",
  "from": 1,
  "last_page": 5,
  "last_page_url": "https://...",
  "next_page_url": "https://...",
  "path": "https://...",
  "per_page": 15,
  "prev_page_url": null,
  "to": 15,
  "total": 75
}
```

---

## Status Codes

### Order Status

| Status | Description | UI Color Suggestion |
|--------|-------------|---------------------|
| `inquiry` | Initial inquiry submitted | Blue/Info |
| `pending` | Order confirmed, awaiting processing | Yellow/Warning |
| `processing` | Order being prepared | Blue/Primary |
| `shipped` | Order has been shipped | Blue/Info |
| `delivered` | Order delivered successfully | Green/Success |
| `cancelled` | Order cancelled | Red/Danger |

### Payment Status

| Status | Description | When Used |
|--------|-------------|-----------|
| `not_required` | No payment needed | For inquiries |
| `pending` | Payment pending | When order is confirmed |
| `paid` | Payment received | After payment |
| `failed` | Payment failed | If payment fails |
| `refunded` | Payment refunded | If order cancelled/refunded |

### Helper Flag

- `is_inquiry: true/false` - Use this to show inquiry-specific UI (e.g., "We'll contact you" message)

---

## Error Handling

### HTTP Status Codes

- `200` - Success
- `201` - Created (for new orders)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (missing/invalid token)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

### Common Error Scenarios

#### 1. Validation Error (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "customer_phone": ["The customer phone field is required."],
    "customer_email": ["The customer email must be a valid email address."]
  }
}
```

#### 2. Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

#### 3. Empty Cart (400)
```json
{
  "success": false,
  "message": "Your cart is empty. Please add items before checkout."
}
```

#### 4. Order Not Found (404)
```json
{
  "success": false,
  "message": "Order not found"
}
```

### Error Handling Best Practices

1. **Always check `success` field** before accessing `data`
2. **Handle validation errors** by displaying field-specific messages
3. **Show user-friendly messages** for common errors
4. **Retry logic** for network errors (401, 500)
5. **Log errors** for debugging

---

## Image Handling

### Image URLs

All image URLs returned by the API are **absolute URLs** (full URLs starting with `http://` or `https://`).

### Cache Busting

Image URLs include a cache buster parameter (`?v=timestamp`) to ensure fresh images:
```
https://your-domain.com/storage/products/primary/tractor.jpg?v=1234567890
```

### Image Fields

- `image` - Main product image (single URL)
- `images` - Array of gallery images (array of URLs)

### Loading Images in Flutter

```dart
// Example using CachedNetworkImage
CachedNetworkImage(
  imageUrl: product['image'],
  placeholder: (context, url) => CircularProgressIndicator(),
  errorWidget: (context, url, error) => Icon(Icons.error),
)
```

### Image Fallback

If an image URL is `null` or fails to load, show a placeholder image.

---

## Complete Workflow Example

### Step 1: Login
```dart
// POST /api/v1/login
final response = await http.post(
  Uri.parse('https://your-domain.com/api/v1/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'customer@example.com',
    'password': 'password123',
  }),
);

final data = jsonDecode(response.body);
final token = data['data']['token'];
// Store token securely
```

### Step 2: Get Categories
```dart
// GET /api/v1/categories
final response = await http.get(
  Uri.parse('https://your-domain.com/api/v1/categories'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

final data = jsonDecode(response.body);
final categories = data['data'];
// Each category includes subcategories array
```

### Step 3: Get Subcategories
```dart
// GET /api/v1/subcategories?category_id=1
final response = await http.get(
  Uri.parse('https://your-domain.com/api/v1/subcategories?category_id=1'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

final data = jsonDecode(response.body);
final subcategories = data['data'];
```

### Step 4: Get Products
```dart
// GET /api/v1/products?category_id=1&subcategory_id=2
final response = await http.get(
  Uri.parse('https://your-domain.com/api/v1/products?category_id=1&subcategory_id=2'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

final data = jsonDecode(response.body);
final products = data['data']['data'];
// Each product includes category and subcategory
```

### Step 5: Add to Cart
```dart
// POST /api/v1/cart/add
final response = await http.post(
  Uri.parse('https://your-domain.com/api/v1/cart/add'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'product_id': 1,
    'quantity': 2,
  }),
);
```

### Step 6: Get Cart
```dart
// GET /api/v1/cart
final response = await http.get(
  Uri.parse('https://your-domain.com/api/v1/cart'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

final cart = jsonDecode(response.body)['data'];
```

### Step 7: Create Order (Inquiry)
```dart
// POST /api/v1/orders
final response = await http.post(
  Uri.parse('https://your-domain.com/api/v1/orders'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
  body: jsonEncode({
    'customer_name': 'John Doe',
    'customer_email': 'john@example.com',
    'customer_phone': '+1234567890',
    'billing_address': '123 Main Street, City, State, ZIP',
    'shipping_address': '123 Main Street, City, State, ZIP',
    'notes': 'Please call before delivery',
  }),
);

if (response.statusCode == 201) {
  final order = jsonDecode(response.body)['data'];
  final orderNumber = order['order_number'];
  // Show success message
  // Navigate to order details
}
```

### Step 8: View Orders
```dart
// GET /api/v1/orders?status=inquiry
final response = await http.get(
  Uri.parse('https://your-domain.com/api/v1/orders?status=inquiry'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

final orders = jsonDecode(response.body)['data']['data'];
```

### Step 9: View Order Details
```dart
// GET /api/v1/orders/AGR-T5BSBQTM
final response = await http.get(
  Uri.parse('https://your-domain.com/api/v1/orders/$orderNumber'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);

final order = jsonDecode(response.body)['data'];
```

---

## Best Practices

### 1. Token Management
- **Store token securely** (use `flutter_secure_storage` or similar)
- **Refresh token** before expiration
- **Handle token expiration** gracefully (redirect to login)

### 2. Error Handling
```dart
try {
  final response = await http.get(...);
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    if (data['success'] == true) {
      // Handle success
    } else {
      // Handle API error
      showError(data['message']);
    }
  } else if (response.statusCode == 401) {
    // Token expired, redirect to login
    navigateToLogin();
  } else {
    // Handle other errors
    showError('Something went wrong');
  }
} catch (e) {
  // Handle network errors
  showError('Network error: ${e.toString()}');
}
```

### 3. Loading States
- Show loading indicators during API calls
- Disable buttons while processing
- Use skeleton loaders for better UX

### 4. Form Validation
- Validate on client-side before API call
- Show field-specific error messages
- Highlight invalid fields

### 5. Order Status Display
```dart
String getStatusColor(String status) {
  switch (status) {
    case 'inquiry':
      return 'info'; // Blue
    case 'pending':
      return 'warning'; // Yellow
    case 'processing':
      return 'primary'; // Blue
    case 'shipped':
      return 'info'; // Blue
    case 'delivered':
      return 'success'; // Green
    case 'cancelled':
      return 'danger'; // Red
    default:
      return 'secondary';
  }
}

String getStatusText(String status) {
  if (status == 'inquiry') {
    return 'Inquiry - We\'ll contact you soon';
  }
  return status.toUpperCase();
}
```

### 6. Inquiry-Specific UI
```dart
if (order['is_inquiry'] == true) {
  // Show inquiry message
  return Card(
    child: Column(
      children: [
        Icon(Icons.info, color: Colors.blue),
        Text('Inquiry Received'),
        Text('We will contact you within 24 hours to confirm your order.'),
        Text('Phone: ${order['customer_phone']}'),
      ],
    ),
  );
}
```

### 7. Image Loading
- Use cached network images
- Show placeholders while loading
- Handle image load errors gracefully
- Respect cache buster parameters

### 8. Pagination
- Implement infinite scroll or "Load More" button
- Track current page
- Handle empty states

### 9. Network Resilience
- Implement retry logic for failed requests
- Show offline indicators
- Cache critical data locally

### 10. User Feedback
- Show success messages after actions
- Confirm destructive actions (e.g., cancel order)
- Provide clear error messages

---

## Testing Checklist

Before going live, test:

- [ ] Login/Logout flow
- [ ] Browse products
- [ ] Add/remove from cart
- [ ] Create order (inquiry)
- [ ] View order list
- [ ] Filter orders by status
- [ ] View order details
- [ ] Handle empty cart error
- [ ] Handle validation errors
- [ ] Handle network errors
- [ ] Handle token expiration
- [ ] Image loading (with cache busting)
- [ ] Pagination
- [ ] Offline handling

---

## Support

For API issues or questions:
- Check API response for error messages
- Verify token is valid and included in headers
- Ensure all required fields are provided
- Check network connectivity

---

## Product Filtering Examples

### Filter by Category
```dart
// GET /api/v1/products?category_id=1
final uri = Uri.parse('https://your-domain.com/api/v1/products')
    .replace(queryParameters: {'category_id': '1'});
```

### Filter by Subcategory
```dart
// GET /api/v1/products?subcategory_id=2
final uri = Uri.parse('https://your-domain.com/api/v1/products')
    .replace(queryParameters: {'subcategory_id': '2'});
```

### Filter by Category and Subcategory
```dart
// GET /api/v1/products?category_id=1&subcategory_id=2
final uri = Uri.parse('https://your-domain.com/api/v1/products')
    .replace(queryParameters: {
      'category_id': '1',
      'subcategory_id': '2'
    });
```

### Search Products
```dart
// GET /api/v1/products/search?q=tractor
final uri = Uri.parse('https://your-domain.com/api/v1/products/search')
    .replace(queryParameters: {'q': 'tractor'});
```

## Flutter Code Examples

### Complete Product Model
```dart
class Product {
  final int id;
  final String name;
  final String slug;
  final String sku;
  final double price;
  final double? originalPrice;
  final double? salePrice;
  final double? dealerPrice;
  final String image;
  final List<String> images;
  final Category category;
  final Subcategory? subcategory;
  final bool inStock;
  final int stockQuantity;
  
  Product({
    required this.id,
    required this.name,
    required this.slug,
    required this.sku,
    required this.price,
    this.originalPrice,
    this.salePrice,
    this.dealerPrice,
    required this.image,
    required this.images,
    required this.category,
    this.subcategory,
    required this.inStock,
    required this.stockQuantity,
  });
  
  factory Product.fromJson(Map<String, dynamic> json) {
    return Product(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      sku: json['sku'],
      price: (json['price'] as num).toDouble(),
      originalPrice: json['original_price'] != null 
          ? (json['original_price'] as num).toDouble() 
          : null,
      salePrice: json['sale_price'] != null 
          ? (json['sale_price'] as num).toDouble() 
          : null,
      dealerPrice: json['dealer_price'] != null 
          ? (json['dealer_price'] as num).toDouble() 
          : null,
      image: json['image'] ?? '',
      images: List<String>.from(json['images'] ?? []),
      category: Category.fromJson(json['category']),
      subcategory: json['subcategory'] != null 
          ? Subcategory.fromJson(json['subcategory']) 
          : null,
      inStock: json['in_stock'] ?? false,
      stockQuantity: json['stock_quantity'] ?? 0,
    );
  }
}

class Category {
  final int id;
  final String name;
  final String slug;
  final List<Subcategory>? subcategories;
  
  Category({
    required this.id,
    required this.name,
    required this.slug,
    this.subcategories,
  });
  
  factory Category.fromJson(Map<String, dynamic> json) {
    return Category(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      subcategories: json['subcategories'] != null
          ? (json['subcategories'] as List)
              .map((s) => Subcategory.fromJson(s))
              .toList()
          : null,
    );
  }
}

class Subcategory {
  final int id;
  final String name;
  final String slug;
  final int? categoryId;
  final Category? category;
  
  Subcategory({
    required this.id,
    required this.name,
    required this.slug,
    this.categoryId,
    this.category,
  });
  
  factory Subcategory.fromJson(Map<String, dynamic> json) {
    return Subcategory(
      id: json['id'],
      name: json['name'],
      slug: json['slug'],
      categoryId: json['category_id'],
      category: json['category'] != null 
          ? Category.fromJson(json['category']) 
          : null,
    );
  }
}
```

### API Service Example
```dart
class ApiService {
  final String baseUrl = 'https://your-domain.com/api/v1';
  final String? token;
  
  ApiService({this.token});
  
  Map<String, String> get headers => {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
    if (token != null) 'Authorization': 'Bearer $token',
  };
  
  // Get Categories
  Future<List<Category>> getCategories() async {
    final response = await http.get(
      Uri.parse('$baseUrl/categories'),
      headers: headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return (data['data'] as List)
            .map((json) => Category.fromJson(json))
            .toList();
      }
    }
    throw Exception('Failed to load categories');
  }
  
  // Get Subcategories
  Future<List<Subcategory>> getSubcategories({int? categoryId}) async {
    final uri = Uri.parse('$baseUrl/subcategories');
    final queryParams = categoryId != null 
        ? {'category_id': categoryId.toString()} 
        : {};
    
    final response = await http.get(
      uri.replace(queryParameters: queryParams),
      headers: headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return (data['data'] as List)
            .map((json) => Subcategory.fromJson(json))
            .toList();
      }
    }
    throw Exception('Failed to load subcategories');
  }
  
  // Get Products
  Future<Map<String, dynamic>> getProducts({
    int? categoryId,
    int? subcategoryId,
    String? search,
    String sort = 'name',
    int page = 1,
    int perPage = 15,
  }) async {
    final queryParams = <String, String>{
      'sort': sort,
      'page': page.toString(),
      'per_page': perPage.toString(),
    };
    
    if (categoryId != null) {
      queryParams['category_id'] = categoryId.toString();
    }
    if (subcategoryId != null) {
      queryParams['subcategory_id'] = subcategoryId.toString();
    }
    if (search != null && search.isNotEmpty) {
      queryParams['search'] = search;
    }
    
    final response = await http.get(
      Uri.parse('$baseUrl/products')
          .replace(queryParameters: queryParams),
      headers: headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return {
          'products': (data['data']['data'] as List)
              .map((json) => Product.fromJson(json))
              .toList(),
          'pagination': data['data'],
        };
      }
    }
    throw Exception('Failed to load products');
  }
  
  // Get Single Product
  Future<Product> getProduct(String slug) async {
    final response = await http.get(
      Uri.parse('$baseUrl/products/$slug'),
      headers: headers,
    );
    
    if (response.statusCode == 200) {
      final data = jsonDecode(response.body);
      if (data['success'] == true) {
        return Product.fromJson(data['data']);
      }
    }
    throw Exception('Failed to load product');
  }
}
```

### UI Example: Category and Subcategory Selection
```dart
class CategorySubcategorySelector extends StatefulWidget {
  @override
  _CategorySubcategorySelectorState createState() => 
      _CategorySubcategorySelectorState();
}

class _CategorySubcategorySelectorState 
    extends State<CategorySubcategorySelector> {
  List<Category> categories = [];
  List<Subcategory> subcategories = [];
  Category? selectedCategory;
  Subcategory? selectedSubcategory;
  
  @override
  void initState() {
    super.initState();
    loadCategories();
  }
  
  Future<void> loadCategories() async {
    final cats = await ApiService().getCategories();
    setState(() {
      categories = cats;
    });
  }
  
  Future<void> loadSubcategories(int categoryId) async {
    final subs = await ApiService().getSubcategories(categoryId: categoryId);
    setState(() {
      subcategories = subs;
      selectedSubcategory = null; // Reset subcategory when category changes
    });
  }
  
  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        // Category Dropdown
        DropdownButton<Category>(
          value: selectedCategory,
          hint: Text('Select Category'),
          items: categories.map((category) {
            return DropdownMenuItem(
              value: category,
              child: Text(category.name),
            );
          }).toList(),
          onChanged: (Category? category) {
            setState(() {
              selectedCategory = category;
            });
            if (category != null) {
              loadSubcategories(category.id);
            }
          },
        ),
        
        // Subcategory Dropdown (only show if category is selected)
        if (selectedCategory != null)
          DropdownButton<Subcategory>(
            value: selectedSubcategory,
            hint: Text('Select Subcategory (Optional)'),
            items: subcategories.map((subcategory) {
              return DropdownMenuItem(
                value: subcategory,
                child: Text(subcategory.name),
              );
            }).toList(),
            onChanged: (Subcategory? subcategory) {
              setState(() {
                selectedSubcategory = subcategory;
              });
            },
          ),
      ],
    );
  }
}
```

---

## Changelog

### Version 1.1 (2025-11-08)
- Added subcategory support
- Added subcategory endpoints
- Updated product responses to include subcategory
- Updated category responses to include subcategories
- Added subcategory filtering for products
- Enhanced Flutter code examples

### Version 1.0 (2025-11-08)
- Initial API documentation
- Inquiry-based order system
- Image URL handling with cache busting
- Complete workflow examples

---

**Last Updated**: November 8, 2025


