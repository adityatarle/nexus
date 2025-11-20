# API Quick Reference - Nexus Agriculture

## Base URL
```
https://your-domain.com/api
```

## Authentication
```
Headers:
Authorization: Bearer {token}
Accept: application/json
Content-Type: application/json
```

---

## Essential Endpoints

### 1. Login
```
POST /api/auth/login
Body: { "email": "...", "password": "..." }
Response: { "success": true, "data": { "token": "...", "user": {...} } }
```

### 2. Get Products
```
GET /api/products?category_id=1&per_page=15
Response: { "success": true, "data": { "data": [...] } }
```

### 3. Add to Cart
```
POST /api/cart/add
Body: { "product_id": 1, "quantity": 2 }
Response: { "success": true, "data": {...} }
```

### 4. Get Cart
```
GET /api/cart
Response: { "success": true, "data": { "items": [...], "total": 1000.00 } }
```

### 5. Create Order (Inquiry) ⭐
```
POST /api/orders
Body: {
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+1234567890",  // REQUIRED
  "billing_address": "123 Main St, City, State, ZIP",
  "shipping_address": "123 Main St, City, State, ZIP",  // Optional
  "notes": "Optional notes"
}
Response: {
  "success": true,
  "message": "Inquiry received! We will contact you shortly...",
  "data": {
    "order_number": "AGR-XXXXX",
    "order_status": "inquiry",
    "payment_status": "not_required",
    "is_inquiry": true,
    ...
  }
}
```

### 6. Get Orders
```
GET /api/orders?status=inquiry&per_page=15
Response: { "success": true, "data": { "data": [...] } }
```

### 7. Get Single Order
```
GET /api/orders/{orderNumber}
Response: { "success": true, "data": {...} }
```

---

## Order Status Values

| Status | Description |
|--------|------------|
| `inquiry` | Initial inquiry (no payment) |
| `pending` | Order confirmed, awaiting processing |
| `processing` | Order being prepared |
| `shipped` | Order shipped |
| `delivered` | Order delivered |
| `cancelled` | Order cancelled |

## Payment Status Values

| Status | Description |
|--------|------------|
| `not_required` | For inquiries (no payment needed) |
| `pending` | Payment pending |
| `paid` | Payment received |
| `failed` | Payment failed |
| `refunded` | Payment refunded |

---

## Key Fields

### Order Object
- `order_number` - Unique identifier (save this!)
- `order_status` - Current status
- `payment_status` - Payment status
- `is_inquiry` - Boolean flag (true for inquiries)
- `total_amount` - Total order amount
- `items` - Array of order items
- `created_at` - Order creation timestamp

### Order Item Object
- `product_id` - Product ID
- `product_name` - Product name
- `quantity` - Quantity ordered
- `price` - Unit price
- `total` - Line total
- `image` - Product image URL (absolute URL)

---

## Important Notes

1. **No Payment Required**: Orders are created as inquiries. Admin will contact customer.

2. **Phone Required**: `customer_phone` is mandatory for follow-up.

3. **Image URLs**: All image URLs are absolute (full URLs) with cache busting.

4. **Error Handling**: Always check `success` field before accessing `data`.

5. **Status Filtering**: Use `?status=inquiry` to filter inquiries.

---

## Common Error Codes

- `200` - Success
- `201` - Created (new order)
- `400` - Bad Request
- `401` - Unauthorized (invalid/missing token)
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error

---

## Flutter Code Snippets

### Make Authenticated Request
```dart
final response = await http.get(
  Uri.parse('https://your-domain.com/api/orders'),
  headers: {
    'Authorization': 'Bearer $token',
    'Accept': 'application/json',
  },
);
```

### Check Response
```dart
final data = jsonDecode(response.body);
if (data['success'] == true) {
  // Handle success
  final orders = data['data']['data'];
} else {
  // Handle error
  showError(data['message']);
}
```

### Display Inquiry Status
```dart
if (order['is_inquiry'] == true) {
  return Text('Inquiry - We\'ll contact you soon');
}
```

---

## Full Documentation

See `FLUTTER_API_GUIDE.md` for complete documentation with examples.






