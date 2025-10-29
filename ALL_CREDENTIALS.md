# 🔐 Complete Credentials List - Nexus Agriculture Platform

## 📋 Table of Contents
- [Web Application Credentials](#web-application-credentials)
- [API Credentials](#api-credentials)
- [Admin Panel Access](#admin-panel-access)
- [User Dashboard Access](#user-dashboard-access)

---

## 🌐 Web Application Credentials

### 👨‍💼 ADMIN ACCOUNTS

#### Admin Account #1 (Primary - TestUsersSeeder)
- **URL:** `http://127.0.0.1:8000/admin/login`
- **Email:** `admin@nexus.com`
- **Password:** `admin123`
- **Role:** Admin
- **Access:** Full admin dashboard, manage products, dealers, customers, orders

#### Admin Account #2 (Alternative - AdminUserSeeder)
- **URL:** `http://127.0.0.1:8000/admin/login`
- **Email:** `admin@nexusagriculture.com`
- **Password:** `password`
- **Role:** Admin
- **Access:** Full admin dashboard

---

### 🛒 CUSTOMER ACCOUNTS

#### Customer Account #1 (Primary)
- **URL:** `http://127.0.0.1:8000/auth/login` or `http://127.0.0.1:8000/auth/customer-login`
- **Email:** `customer@nexus.com`
- **Password:** `customer123`
- **Role:** Customer
- **Access:**
  - Browse products (retail prices)
  - Add to cart & wishlist
  - Place orders
  - View order history
  - Customer dashboard

#### Customer Account #2 (Alternative)
- **URL:** `http://127.0.0.1:8000/auth/login`
- **Email:** `customer@example.com`
- **Password:** `password`
- **Role:** Customer
- **Access:** Same as above

---

### 💼 DEALER ACCOUNTS

#### ✅ Approved Dealer #1 (Primary)
- **URL:** `http://127.0.0.1:8000/auth/login` or `http://127.0.0.1:8000/auth/dealer-login`
- **Email:** `dealer@nexus.com`
- **Password:** `dealer123`
- **Role:** Dealer
- **Status:** ✅ Approved
- **Access:**
  - See wholesale/dealer prices ✓
  - Place bulk orders
  - Dealer dashboard
  - Order management
  - Download invoices
  - Bulk pricing tiers

#### ✅ Approved Dealer #2 (Alternative)
- **URL:** `http://127.0.0.1:8000/auth/login`
- **Email:** `wholesaler@example.com`
- **Password:** `password`
- **Role:** Dealer
- **Status:** ✅ Approved
- **Business Name:** Agri Wholesale Co.
- **Access:** Same as above

#### ⏳ Pending Dealer (Awaiting Approval)
- **URL:** `http://127.0.0.1:8000/auth/login` or `http://127.0.0.1:8000/auth/dealer-login`
- **Email:** `pending@nexus.com`
- **Password:** `pending123`
- **Role:** Dealer
- **Status:** ⏳ Pending Approval
- **Access:**
  - Can login but sees "Pending Approval" page
  - Cannot see wholesale prices until admin approves
  - Cannot access dealer features

#### ⏳ Pending Dealer #2 (Alternative)
- **URL:** `http://127.0.0.1:8000/auth/login`
- **Email:** `dealer@example.com`
- **Password:** `password`
- **Role:** Dealer
- **Status:** ⏳ Pending Approval
- **Business Name:** Green Farm Supplies
- **Access:** Same as above

---

## 🔌 API Credentials

### API Base URL
- **Local:** `http://127.0.0.1:8000/api/v1`
- **Production:** `https://yourdomain.com/api/v1`

### How to Get API Token

#### Step 1: Register or Login via API

**Register (Creates new account):**
```bash
POST /api/v1/register
Content-Type: application/json

{
    "name": "API User",
    "email": "apiuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer",
    "phone": "+1234567890"
}
```

**Login (Gets token for existing account):**
```bash
POST /api/v1/login
Content-Type: application/json

{
    "email": "customer@nexus.com",
    "password": "customer123"
}
```

#### Step 2: Response includes token
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
    }
}
```

#### Step 3: Use token in requests
```
Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### API Test Credentials

Use any of the web credentials above with the API:

**Customer API Access:**
- Email: `customer@nexus.com`
- Password: `customer123`
- API Endpoint: `POST /api/v1/login`

**Dealer API Access:**
- Email: `dealer@nexus.com`
- Password: `dealer123`
- API Endpoint: `POST /api/v1/login`

**Admin API Access:**
- Email: `admin@nexus.com`
- Password: `admin123`
- API Endpoint: `POST /api/v1/login`

---

## 🎯 Quick Access Links

### Admin Areas
- **Admin Login:** http://127.0.0.1:8000/admin/login
- **Admin Dashboard:** http://127.0.0.1:8000/admin/dashboard
- **Manage Products:** http://127.0.0.1:8000/admin/products
- **Manage Dealers:** http://127.0.0.1:8000/admin/dealers
- **Manage Orders:** http://127.0.0.1:8000/admin/orders
- **Manage Customers:** http://127.0.0.1:8000/admin/customers

### Customer Areas
- **Customer Login:** http://127.0.0.1:8000/auth/customer-login
- **Customer Dashboard:** http://127.0.0.1:8000/customer/dashboard
- **Orders:** http://127.0.0.1:8000/customer/orders
- **Profile:** http://127.0.0.1:8000/customer/profile

### Dealer Areas
- **Dealer Login:** http://127.0.0.1:8000/auth/dealer-login
- **Dealer Dashboard:** http://127.0.0.1:8000/dealer/dashboard
- **Dealer Products:** http://127.0.0.1:8000/dealer/products
- **Dealer Orders:** http://127.0.0.1:8000/dealer/orders

### Public Areas
- **Home Page:** http://127.0.0.1:8000
- **Products:** http://127.0.0.1:8000/products
- **Categories:** http://127.0.0.1:8000/categories

---

## 📱 Mobile App API Usage

### Example: Login and Get Token

```dart
// Flutter/Dart example
final response = await http.post(
  Uri.parse('https://yourdomain.com/api/v1/login'),
  headers: {'Content-Type': 'application/json'},
  body: jsonEncode({
    'email': 'customer@nexus.com',
    'password': 'customer123',
  }),
);

final data = jsonDecode(response.body);
final token = data['data']['token'];
// Store token securely for future requests
```

### Example: Authenticated Request

```dart
final response = await http.get(
  Uri.parse('https://yourdomain.com/api/v1/products'),
  headers: {
    'Authorization': 'Bearer $token',
    'Content-Type': 'application/json',
  },
);
```

---

## 🔄 Creating New Accounts

### Via Web Interface

1. **Register Customer:** http://127.0.0.1:8000/auth/register
2. **Register Dealer:** http://127.0.0.1:8000/auth/register (select dealer role)
3. **Dealer Registration Form:** http://127.0.0.1:8000/dealer/registration (after initial registration)

### Via API

```bash
POST /api/v1/register
{
    "name": "New User",
    "email": "newuser@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer", // or "dealer"
    "phone": "+1234567890"
}
```

### Via Database Seeder

Run test users seeder:
```bash
php artisan db:seed --class=TestUsersSeeder
```

---

## 🔒 Security Notes

⚠️ **IMPORTANT:**
1. These are **TEST CREDENTIALS** - Change all passwords in production!
2. Never commit real credentials to version control
3. Use environment variables for sensitive data
4. Enable two-factor authentication in production
5. Regularly rotate API tokens
6. Use HTTPS in production

---

## 📊 Summary Table

| Account Type | Email | Password | Status | Access Level |
|-------------|-------|----------|--------|--------------|
| Admin | admin@nexus.com | admin123 | Active | Full Admin |
| Admin (Alt) | admin@nexusagriculture.com | password | Active | Full Admin |
| Customer | customer@nexus.com | customer123 | Active | Customer |
| Customer (Alt) | customer@example.com | password | Active | Customer |
| Dealer (Approved) | dealer@nexus.com | dealer123 | ✅ Approved | Dealer Full |
| Dealer (Approved Alt) | wholesaler@example.com | password | ✅ Approved | Dealer Full |
| Dealer (Pending) | pending@nexus.com | pending123 | ⏳ Pending | Limited |
| Dealer (Pending Alt) | dealer@example.com | password | ⏳ Pending | Limited |

---

## 🆘 Troubleshooting

### Can't Login?
1. Verify the account exists: Check database `users` table
2. Run seeder again: `php artisan db:seed --class=TestUsersSeeder`
3. Clear cache: `php artisan cache:clear`
4. Check session: Clear browser cookies

### API Token Not Working?
1. Verify token in Authorization header: `Bearer {token}`
2. Check token hasn't expired (tokens don't expire by default in Sanctum)
3. Try logging in again to get a new token
4. Check user account is active

### Dealer Can't See Prices?
1. Verify `is_dealer_approved = 1` in database
2. Admin must approve dealer first
3. Check dealer registration status in admin panel

---

**Last Updated:** 2025-01-29
**Platform:** Nexus Agriculture eCommerce Platform
**Version:** 1.0

