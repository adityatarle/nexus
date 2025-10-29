# 🌐 Web Application Credentials

**Base URL:** `http://127.0.0.1:8000`

---

## 👨‍💼 ADMIN LOGIN

### Admin Account
- **Login URL:** `http://127.0.0.1:8000/admin/login`
- **Email:** `admin@nexus.com`
- **Password:** `admin123`
- **Role:** Admin
- **Access:**
  - Full admin dashboard
  - Manage products
  - Manage categories
  - Approve/reject dealers
  - View all orders
  - Manage customers
  - View reports & analytics
  - Configure settings

**Dashboard:** `http://127.0.0.1:8000/admin/dashboard`

---

## 🛒 CUSTOMER LOGIN

### Customer Account
- **Login URL:** 
  - `http://127.0.0.1:8000/auth/login` (General login)
  - `http://127.0.0.1:8000/auth/customer-login` (Customer specific)
- **Email:** `customer@nexus.com`
- **Password:** `customer123`
- **Role:** Customer
- **Access:**
  - Browse products (retail prices)
  - Add products to cart
  - Add products to wishlist
  - Place orders
  - View order history
  - Manage profile
  - View notifications
- **Dashboard:** `http://127.0.0.1:8000/customer/dashboard`

---

## 💼 DEALER LOGIN

### ✅ Approved Dealer
- **Login URL:**
  - `http://127.0.0.1:8000/auth/login` (General login)
  - `http://127.0.0.1:8000/auth/dealer-login` (Dealer specific)
- **Email:** `dealer@nexus.com`
- **Password:** `dealer123`
- **Role:** Dealer
- **Status:** ✅ Approved
- **Access:**
  - See wholesale/dealer prices ✓
  - Browse products with dealer pricing
  - Place bulk orders
  - View dealer dashboard
  - Manage orders
  - Download invoices
  - View dealer-specific pricing tiers
- **Dashboard:** `http://127.0.0.1:8000/dealer/dashboard`
- **Products:** `http://127.0.0.1:8000/dealer/products`

### ⏳ Pending Dealer
- **Login URL:**
  - `http://127.0.0.1:8000/auth/login` (General login)
  - `http://127.0.0.1:8000/auth/dealer-login` (Dealer specific)
- **Email:** `pending@nexus.com`
- **Password:** `pending123`
- **Role:** Dealer
- **Status:** ⏳ Pending Approval
- **Access:**
  - Can login but sees "Pending Approval" page
  - Cannot see wholesale prices
  - Cannot access dealer features
  - Must wait for admin approval
- **Pending Page:** `http://127.0.0.1:8000/dealer/pending`

---

## 📋 Quick Reference

| Account Type | Email | Password | Login URL |
|-------------|-------|----------|-----------|
| **Admin** | admin@nexus.com | admin123 | /admin/login |
| **Customer** | customer@nexus.com | customer123 | /auth/login |
| **Dealer (Approved)** | dealer@nexus.com | dealer123 | /auth/login |
| **Dealer (Pending)** | pending@nexus.com | pending123 | /auth/login |

---

## 🔗 Important Links

### Public Pages
- **Home:** `http://127.0.0.1:8000/`
- **Products:** `http://127.0.0.1:8000/products`
- **Categories:** `http://127.0.0.1:8000/categories`
- **Cart:** `http://127.0.0.1:8000/cart`
- **Register:** `http://127.0.0.1:8000/auth/register`

### Admin Pages
- **Dashboard:** `http://127.0.0.1:8000/admin/dashboard`
- **Products:** `http://127.0.0.1:8000/admin/products`
- **Categories:** `http://127.0.0.1:8000/admin/categories`
- **Orders:** `http://127.0.0.1:8000/admin/orders`
- **Dealers:** `http://127.0.0.1:8000/admin/dealers`
- **Customers:** `http://127.0.0.1:8000/admin/customers`
- **Reports:** `http://127.0.0.1:8000/admin/reports`
- **Settings:** `http://127.0.0.1:8000/admin/settings`

### Customer Pages
- **Dashboard:** `http://127.0.0.1:8000/customer/dashboard`
- **Orders:** `http://127.0.0.1:8000/customer/orders`
- **Profile:** `http://127.0.0.1:8000/customer/profile`
- **Wishlist:** `http://127.0.0.1:8000/wishlist`

### Dealer Pages
- **Dashboard:** `http://127.0.0.1:8000/dealer/dashboard`
- **Products:** `http://127.0.0.1:8000/dealer/products`
- **Orders:** `http://127.0.0.1:8000/dealer/orders`
- **Profile:** `http://127.0.0.1:8000/dealer/profile`

---

## 🔐 Authentication Method

**Web application uses:**
- Session-based authentication
- Laravel's built-in `Auth::attempt()` method
- Cookies for session management
- Remember me functionality available

---

## ⚠️ Notes

1. All passwords are for **testing purposes only**
2. Change passwords before production deployment
3. Web login uses sessions (cookies)
4. Web and API authentication are separate (same credentials, different methods)

