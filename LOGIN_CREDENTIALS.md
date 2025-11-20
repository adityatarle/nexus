# 🔐 Login Credentials - Nexus Agriculture eCommerce

## ✅ All Test Accounts Created Successfully!

---

## 👤 **ADMIN LOGIN**
- **URL:** http://127.0.0.1:8000/admin/login
- **Email:** `admin@nexus.com`
- **Password:** `admin123`
- **Access:** Full admin dashboard, manage products, dealers, customers, orders

---

## 🛒 **CUSTOMER LOGIN**
- **URL:** http://127.0.0.1:8000/auth/customer-login  
  (or) http://127.0.0.1:8000/auth/login
- **Email:** `customer@nexus.com`
- **Password:** `customer123`
- **Access:** 
  - Browse products (retail prices)
  - Add to cart & wishlist
  - Place orders
  - View order history
  - Customer dashboard

---

## 💼 **APPROVED DEALER LOGIN**
- **URL:** http://127.0.0.1:8000/auth/dealer-login  
  (or) http://127.0.0.1:8000/auth/login
- **Email:** `dealer@nexus.com`
- **Password:** `dealer123`
- **Access:** 
  - See wholesale/dealer prices ✓
  - Place bulk orders
  - Dealer dashboard
  - Order management
  - Download invoices
- **Status:** ✅ Admin Approved - Full Access

---

## ⏳ **PENDING DEALER LOGIN**
- **URL:** http://127.0.0.1:8000/auth/dealer-login  
  (or) http://127.0.0.1:8000/auth/login
- **Email:** `pending@nexus.com`
- **Password:** `pending123`
- **Access:** 
  - Can login but sees "Pending Approval" page
  - Cannot see wholesale prices until admin approves
- **Status:** ⏳ Waiting for Admin Approval

---

## 🔄 Quick Test Scenarios

### Test 1: Customer Flow
1. Login as customer (`customer@nexus.com` / `customer123`)
2. Browse products on home page
3. Add products to cart (no login required for cart)
4. Add products to wishlist (login required)
5. View cart: http://127.0.0.1:8000/agriculture/cart
6. View wishlist: http://127.0.0.1:8000/wishlist

### Test 2: Dealer Flow
1. Login as approved dealer (`dealer@nexus.com` / `dealer123`)
2. Notice wholesale prices on products (lower than retail)
3. Add to cart - uses dealer pricing
4. Access dealer dashboard: http://127.0.0.1:8000/dealer/dashboard
5. View dealer products: http://127.0.0.1:8000/dealer/products

### Test 3: Admin Flow
1. Login as admin (`admin@nexus.com` / `admin123`)
2. Access admin dashboard: http://127.0.0.1:8000/admin
3. Manage dealers: http://127.0.0.1:8000/admin/dealers
4. Approve pending dealer (`pending@nexus.com`)
5. Manage products: http://127.0.0.1:8000/admin/products
6. View all orders: http://127.0.0.1:8000/admin/orders

---

## 🛒 Cart & Wishlist Status

### ✅ **Cart - WORKING**
- Add to cart from home page ✓
- Add to cart from product listing ✓
- Add to cart from product detail ✓
- View cart page ✓
- Update quantities ✓
- Remove items ✓
- Cart count in header ✓
- Session-based (works without login) ✓
- Uses correct pricing (retail/dealer based on user) ✓

### ✅ **Wishlist - WORKING**
- Add to wishlist (requires login) ✓
- View wishlist page ✓
- Remove items ✓
- Wishlist count in header ✓
- Database-backed per user ✓
- Redirect to login if not authenticated ✓

---

## 📋 Key Features

### Pricing System
- **Retail Prices:** Shown to customers and guests
- **Dealer Prices:** Shown ONLY to approved dealers
- **Dual Pricing:** Every product has both retail and dealer prices
- **Dynamic Display:** Prices adjust based on logged-in user's role

### User Roles
- **Customer:** Normal retail shopping
- **Dealer:** Approved wholesalers with special pricing
- **Admin:** Full system control

---

## 🚀 Ready to Test!

All systems operational. Start testing with the credentials above!

**Home Page:** http://127.0.0.1:8000


















