# 🔌 API Credentials & Authentication Guide

**Base URL:** `http://127.0.0.1:8000/api/v1` (Local)  
**Production:** `https://yourdomain.com/api/v1`

---

## 🔑 Authentication Method

API uses **Laravel Sanctum token-based authentication**:
- Get token by logging in via API
- Include token in `Authorization` header for protected endpoints
- Format: `Authorization: Bearer {token}`

---

## 📝 Step-by-Step: Getting API Token

### Step 1: Login to Get Token

**Endpoint:** `POST /api/v1/login`

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "customer@nexus.com",
    "password": "customer123"
  }'
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 2,
            "name": "Test Customer",
            "email": "customer@nexus.com",
            "role": "customer",
            ...
        },
        "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

### Step 2: Use Token in Requests

**Example Request:**
```bash
curl -X GET http://127.0.0.1:8000/api/v1/products \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx" \
  -H "Content-Type: application/json"
```

---

## 👥 Available API Credentials

### 👨‍💼 ADMIN API ACCESS

**Credentials:**
- **Email:** `admin@nexus.com`
- **Password:** `admin123`

**Login Request:**
```json
POST /api/v1/login
{
    "email": "admin@nexus.com",
    "password": "admin123"
}
```

**Access:** Full admin access to all API endpoints

---

### 🛒 CUSTOMER API ACCESS

**Credentials:**
- **Email:** `customer@nexus.com`
- **Password:** `customer123`

**Login Request:**
```json
POST /api/v1/login
{
    "email": "customer@nexus.com",
    "password": "customer123"
}
```

**Access:**
- View products (retail prices)
- Manage cart
- Manage wishlist
- Place orders
- View own orders
- Manage profile

---

### 💼 DEALER API ACCESS

#### ✅ Approved Dealer
**Credentials:**
- **Email:** `dealer@nexus.com`
- **Password:** `dealer123`

**Login Request:**
```json
POST /api/v1/login
{
    "email": "dealer@nexus.com",
    "password": "dealer123"
}
```

**Access:**
- View products (dealer/wholesale prices) ✓
- Manage cart with dealer pricing
- Place bulk orders
- View dealer orders
- All customer features + dealer pricing

#### ⏳ Pending Dealer
**Credentials:**
- **Email:** `pending@nexus.com`
- **Password:** `pending123`

**Login Request:**
```json
POST /api/v1/login
{
    "email": "pending@nexus.com",
    "password": "pending123"
}
```

**Access:**
- Can login and get token
- Will see retail prices (not dealer prices)
- Must be approved by admin first

---

## 📋 Quick Reference Table

| Account Type | Email | Password | Token Endpoint |
|-------------|-------|----------|----------------|
| **Admin** | admin@nexus.com | admin123 | POST /api/v1/login |
| **Customer** | customer@nexus.com | customer123 | POST /api/v1/login |
| **Dealer (Approved)** | dealer@nexus.com | dealer123 | POST /api/v1/login |
| **Dealer (Pending)** | pending@nexus.com | pending123 | POST /api/v1/login |

---

## 🔐 Register New User via API

If you want to create a new user via API:

**Endpoint:** `POST /api/v1/register`

**Request:**
```json
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer",
    "phone": "+1234567890"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Registration successful",
    "data": {
        "user": { ... },
        "token": "2|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

---

## 📱 Code Examples

### JavaScript/TypeScript

```javascript
// Login
const loginResponse = await fetch('http://127.0.0.1:8000/api/v1/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
  },
  body: JSON.stringify({
    email: 'customer@nexus.com',
    password: 'customer123'
  })
});

const loginData = await loginResponse.json();
const token = loginData.data.token;

// Use token in subsequent requests
const productsResponse = await fetch('http://127.0.0.1:8000/api/v1/products', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Content-Type': 'application/json',
  }
});
```

### Flutter/Dart

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

// Login
Future<String> login() async {
  final response = await http.post(
    Uri.parse('http://127.0.0.1:8000/api/v1/login'),
    headers: {'Content-Type': 'application/json'},
    body: jsonEncode({
      'email': 'customer@nexus.com',
      'password': 'customer123',
    }),
  );
  
  final data = jsonDecode(response.body);
  return data['data']['token'];
}

// Use token
Future<void> getProducts(String token) async {
  final response = await http.get(
    Uri.parse('http://127.0.0.1:8000/api/v1/products'),
    headers: {
      'Authorization': 'Bearer $token',
      'Content-Type': 'application/json',
    },
  );
}
```

### cURL

```bash
# Login and save token
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@nexus.com","password":"customer123"}' \
  | jq -r '.data.token')

# Use token
curl -X GET http://127.0.0.1:8000/api/v1/products \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"
```

---

## 🔒 Token Management

### Token Lifetime
- Tokens **do not expire** by default (configurable)
- User can have multiple tokens (multiple devices)
- Token is revoked on logout

### Logout
**Endpoint:** `POST /api/v1/logout`

**Request:**
```bash
curl -X POST http://127.0.0.1:8000/api/v1/logout \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

This revokes the current token.

### Get Current User
**Endpoint:** `GET /api/v1/user`

**Request:**
```bash
curl -X GET http://127.0.0.1:8000/api/v1/user \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json"
```

---

## 🌐 Public vs Protected Endpoints

### Public Endpoints (No Token Required)
- `POST /api/v1/register`
- `POST /api/v1/login`
- `POST /api/v1/forgot-password`
- `GET /api/v1/products`
- `GET /api/v1/products/{id}`
- `GET /api/v1/products/search`
- `GET /api/v1/products/featured`
- `GET /api/v1/categories`
- `GET /api/v1/categories/{id}`

### Protected Endpoints (Token Required)
- `POST /api/v1/logout`
- `GET /api/v1/user`
- `GET /api/v1/profile`
- `PUT /api/v1/profile`
- `POST /api/v1/profile/change-password`
- All `/api/v1/cart/*` endpoints
- All `/api/v1/wishlist/*` endpoints
- All `/api/v1/orders/*` endpoints
- All `/api/v1/notifications/*` endpoints

---

## ⚠️ Important Notes

1. **Same Credentials, Different Methods:**
   - Web app uses session-based auth (cookies)
   - API uses token-based auth (Bearer tokens)
   - Same email/password work for both

2. **Token Storage:**
   - Store tokens securely (use secure storage in mobile apps)
   - Never expose tokens in client-side code
   - Tokens are single-use per request

3. **Error Responses:**
   ```json
   {
       "success": false,
       "message": "Unauthenticated"
   }
   ```
   Status: `401 Unauthorized`

4. **Rate Limiting:**
   - Login: 5 requests per minute
   - Other endpoints: 60 requests per minute

---

## 🧪 Test Your API Access

### Test Script (cURL)
```bash
# 1. Login
TOKEN=$(curl -s -X POST http://127.0.0.1:8000/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"customer@nexus.com","password":"customer123"}' \
  | jq -r '.data.token')

echo "Token: $TOKEN"

# 2. Get Products
curl -X GET http://127.0.0.1:8000/api/v1/products \
  -H "Authorization: Bearer $TOKEN"

# 3. Get Cart
curl -X GET http://127.0.0.1:8000/api/v1/cart \
  -H "Authorization: Bearer $TOKEN"

# 4. Get Profile
curl -X GET http://127.0.0.1:8000/api/v1/profile \
  -H "Authorization: Bearer $TOKEN"
```

---

**Need Help?** See `API_DOCUMENTATION.md` for complete endpoint documentation.

