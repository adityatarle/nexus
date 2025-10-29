# Mobile API Documentation

## Base URL
```
Production: https://yourdomain.com/api/v1
Development: http://localhost:8000/api/v1
```

## Authentication
Most endpoints require authentication using Laravel Sanctum token. Include the token in the Authorization header:

```
Authorization: Bearer {token}
```

## Response Format
All API responses follow this format:

### Success Response
```json
{
    "success": true,
    "message": "Optional success message",
    "data": { ... }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... } // Validation errors (if applicable)
}
```

---

## Endpoints

### Authentication

#### Register User
```http
POST /api/v1/register
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer", // or "dealer"
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
            "email": "john@example.com",
            "role": "customer",
            ...
        },
        "token": "1|xxxxxxxxxxxx"
    }
}
```

#### Login
```http
POST /api/v1/login
```

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "token": "1|xxxxxxxxxxxx"
    }
}
```

#### Logout
```http
POST /api/v1/logout
Headers: Authorization: Bearer {token}
```

#### Get Current User
```http
GET /api/v1/user
Headers: Authorization: Bearer {token}
```

#### Forgot Password
```http
POST /api/v1/forgot-password
```

**Request Body:**
```json
{
    "email": "john@example.com"
}
```

---

### Products

#### Get All Products
```http
GET /api/v1/products
```

**Query Parameters:**
- `category_id` - Filter by category ID
- `brand` - Filter by brand
- `power_source` - Filter by power source
- `min_price` - Minimum price
- `max_price` - Maximum price
- `featured` - Show only featured products (true/false)
- `sort` - Sort by: `name`, `price_low`, `price_high`, `newest`
- `per_page` - Items per page (default: 15)
- `page` - Page number

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "name": "Product Name",
                "slug": "product-name",
                "description": "...",
                "short_description": "...",
                "sku": "SKU-123",
                "price": 99.99,
                "original_price": 129.99,
                "sale_price": 99.99,
                "discount_percentage": 23,
                "stock_quantity": 50,
                "in_stock": true,
                "is_featured": false,
                "image": "https://domain.com/storage/...",
                "images": ["..."],
                "brand": "Brand Name",
                "model": "Model XYZ",
                "power_source": "Electric",
                "warranty": "1 Year",
                "weight": 10.5,
                "dimensions": "10x10x10",
                "category": {
                    "id": 1,
                    "name": "Category Name",
                    "slug": "category-name"
                },
                "created_at": "2025-01-01T00:00:00.000000Z",
                "updated_at": "2025-01-01T00:00:00.000000Z"
            }
        ],
        ...
    }
}
```

#### Search Products
```http
GET /api/v1/products/search?q=search+term
```

**Query Parameters:**
- `q` - Search term (required, min 2 characters)
- `per_page` - Items per page
- `page` - Page number

#### Get Single Product
```http
GET /api/v1/products/{id}
```

**Response includes related products:**
```json
{
    "success": true,
    "data": {
        ...product details...,
        "related_products": [...]
    }
}
```

#### Get Featured Products
```http
GET /api/v1/products/featured
```

**Query Parameters:**
- `limit` - Number of products (default: 10)

---

### Categories

#### Get All Categories
```http
GET /api/v1/categories
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "Category Name",
            "slug": "category-name",
            "description": "...",
            "image": "https://domain.com/storage/...",
            "products_count": 25
        }
    ]
}
```

#### Get Category with Products
```http
GET /api/v1/categories/{id}
```

**Query Parameters:**
- `sort` - Sort products: `name`, `price_low`, `price_high`, `newest`
- `per_page` - Items per page
- `page` - Page number

---

### Cart (Requires Authentication)

#### Get Cart
```http
GET /api/v1/cart
Headers: Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "items": [
            {
                "product_id": 1,
                "name": "Product Name",
                "sku": "SKU-123",
                "price": 99.99,
                "quantity": 2,
                "subtotal": 199.98,
                "image": "https://domain.com/storage/...",
                "in_stock": true,
                "stock_quantity": 50
            }
        ],
        "subtotal": 199.98,
        "tax_amount": 16.00,
        "shipping_amount": 25.00,
        "total": 240.98,
        "items_count": 2
    }
}
```

#### Add to Cart
```http
POST /api/v1/cart/add
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 2
}
```

#### Update Cart Item
```http
PUT /api/v1/cart/update
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "product_id": 1,
    "quantity": 3
}
```

#### Remove from Cart
```http
DELETE /api/v1/cart/remove/{productId}
Headers: Authorization: Bearer {token}
```

#### Clear Cart
```http
DELETE /api/v1/cart/clear
Headers: Authorization: Bearer {token}
```

#### Get Cart Count
```http
GET /api/v1/cart/count
Headers: Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "count": 5
    }
}
```

---

### Wishlist (Requires Authentication)

#### Get Wishlist
```http
GET /api/v1/wishlist
Headers: Authorization: Bearer {token}
```

#### Add to Wishlist
```http
POST /api/v1/wishlist/add
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "product_id": 1
}
```

#### Remove from Wishlist
```http
DELETE /api/v1/wishlist/remove/{productId}
Headers: Authorization: Bearer {token}
```

#### Clear Wishlist
```http
DELETE /api/v1/wishlist/clear
Headers: Authorization: Bearer {token}
```

#### Check if Product in Wishlist
```http
GET /api/v1/wishlist/check/{productId}
Headers: Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "is_in_wishlist": true
    }
}
```

---

### Orders (Requires Authentication)

#### Get User Orders
```http
GET /api/v1/orders
Headers: Authorization: Bearer {token}
```

**Query Parameters:**
- `status` - Filter by order status
- `per_page` - Items per page
- `page` - Page number

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "order_number": "AGR-XXXXXXXX",
                "customer_name": "John Doe",
                "customer_email": "john@example.com",
                "customer_phone": "+1234567890",
                "billing_address": "...",
                "shipping_address": "...",
                "subtotal": 199.98,
                "tax_amount": 16.00,
                "shipping_amount": 25.00,
                "total_amount": 240.98,
                "payment_method": "credit_card",
                "payment_status": "pending",
                "order_status": "pending",
                "notes": null,
                "items": [...],
                "created_at": "2025-01-01T00:00:00.000000Z"
            }
        ],
        ...
    }
}
```

#### Get Single Order
```http
GET /api/v1/orders/{orderNumber}
Headers: Authorization: Bearer {token}
```

#### Create Order
```http
POST /api/v1/orders
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "customer_name": "John Doe",
    "customer_email": "john@example.com",
    "customer_phone": "+1234567890",
    "billing_address": "123 Main St, City, Country",
    "shipping_address": "123 Main St, City, Country",
    "payment_method": "credit_card", // or "bank_transfer", "cash_on_delivery"
    "notes": "Optional notes"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Order placed successfully!",
    "data": {
        ...order details...
    }
}
```

#### Get Order Invoice
```http
GET /api/v1/orders/{orderNumber}/invoice
Headers: Authorization: Bearer {token}
```

---

### Profile (Requires Authentication)

#### Get Profile
```http
GET /api/v1/profile
Headers: Authorization: Bearer {token}
```

#### Update Profile
```http
PUT /api/v1/profile
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "newemail@example.com",
    "phone": "+1234567890"
}
```

#### Change Password
```http
POST /api/v1/profile/change-password
Headers: Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "current_password": "oldpassword",
    "password": "newpassword",
    "password_confirmation": "newpassword"
}
```

#### Get Notifications
```http
GET /api/v1/notifications
Headers: Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "Order Confirmed",
                "message": "Your order has been confirmed",
                "type": "info",
                "is_read": false,
                "created_at": "2025-01-01T00:00:00.000000Z"
            }
        ],
        ...
    }
}
```

#### Mark Notification as Read
```http
POST /api/v1/notifications/{id}/read
Headers: Authorization: Bearer {token}
```

#### Mark All Notifications as Read
```http
POST /api/v1/notifications/read-all
Headers: Authorization: Bearer {token}
```

---

## HTTP Status Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Rate Limiting

API endpoints are rate-limited. Default limits:
- Authentication endpoints: 5 requests per minute
- Other endpoints: 60 requests per minute

---

## Image URLs

All image URLs are full URLs including the domain:
```
https://yourdomain.com/storage/images/product.jpg
```

If no image is available, a default placeholder is returned:
```
https://yourdomain.com/assets/organic/images/product-thumb-1.png
```

---

## Error Handling

Always check the `success` field in the response. On errors:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": ["Error message 1", "Error message 2"]
    }
}
```

---

## Flutter Integration Example

```dart
// Login example
Future<Map<String, dynamic>> login(String email, String password) async {
  final response = await http.post(
    Uri.parse('$baseUrl/api/v1/login'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'email': email,
      'password': password,
    }),
  );
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    if (data['success']) {
      // Save token
      await storage.write(key: 'token', value: data['data']['token']);
      return data['data'];
    }
  }
  throw Exception('Login failed');
}

// Authenticated request example
Future<Map<String, dynamic>> getProducts(String token) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/products'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
  );
  
  if (response.statusCode == 200) {
    final data = jsonDecode(response.body);
    return data['data'];
  }
  throw Exception('Failed to load products');
}
```

---

## Notes for Hostinger Deployment

1. **Enable HTTPS** - Always use HTTPS in production
2. **Update Base URL** - Update API base URL in Flutter app to your domain
3. **CORS Configuration** - Configure CORS in Laravel for mobile app domain
4. **Storage Link** - Run `php artisan storage:link` on server
5. **Environment Variables** - Set proper `APP_URL` in `.env`
6. **API Rate Limiting** - Adjust rate limits in `bootstrap/app.php` if needed

---

## Support

For API support, contact your development team or refer to the project documentation.

