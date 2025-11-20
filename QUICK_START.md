# 🚀 Quick Start Guide - Nexus Agriculture Platform

## Getting Started in 5 Minutes

### Step 1: Run Migrations
```bash
php artisan migrate
```

This will create:
- Users table with role-based fields
- Products table with dual pricing
- Orders and order items tables
- Dealer registrations table
- Notifications table
- Settings table (with default values)

### Step 2: Seed Sample Data (Optional)
```bash
php artisan db:seed
```

This will create:
- Admin user
- Sample categories
- Sample products with dual pricing
- Sample customers and dealers

### Step 3: Start the Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

---

## 🔐 Access the System

### Admin Dashboard
**URL:** http://localhost:8000/admin/login

**Credentials:**
- Email: `admin@agriculture.com`
- Password: `password`

**What you can do:**
- Manage products (add dual pricing)
- Approve dealer registrations
- View customer list
- Manage orders
- View reports & analytics
- Configure settings

---

### Register as Customer
**URL:** http://localhost:8000/auth/register

**Steps:**
1. Fill registration form with role: "Customer"
2. Login at http://localhost:8000/auth/login
3. Browse products (retail prices)
4. Place orders
5. Access dashboard at http://localhost:8000/customer/dashboard

---

### Register as Dealer
**URL:** http://localhost:8000/dealer/registration

**Steps:**
1. First register as user with role: "Dealer" at http://localhost:8000/auth/register
2. Login and go to http://localhost:8000/dealer/registration
3. Fill complete business information:
   - Business Name
   - GST Number (format: 22AAAAA0000A1Z5)
   - PAN Number (format: ABCDE1234F)
   - Business Address, City, State, Pincode
   - Contact details
   - Business type and other info
4. Submit and wait for admin approval
5. Check http://localhost:8000/dealer/pending for status
6. Once approved, access http://localhost:8000/dealer/dashboard
7. View wholesale prices and place orders

---

## 📦 Test the Complete Flow

### Test Scenario 1: Customer Orders
1. Register as customer
2. Browse products at http://localhost:8000/products
3. Add items to cart
4. Checkout at http://localhost:8000/checkout
5. View order in dashboard
6. Admin can update order status
7. Customer receives notifications

### Test Scenario 2: Dealer Approval & Purchase
1. Register as dealer
2. Fill dealer registration form
3. Login as admin
4. Go to Dealer Management
5. Approve the dealer
6. Login as dealer
7. See wholesale prices (with discount badges)
8. Place order
9. Download PDF invoice

### Test Scenario 3: Admin Management
1. Login as admin
2. Add new product with both retail and dealer prices
3. View reports (sales, inventory, customers)
4. Manage orders (change status)
5. Configure settings (tax rate, shipping)
6. View analytics charts

---

## 🎯 Key URLs Reference

### Public Routes
- Home: `/`
- Products: `/products`
- Login: `/auth/login`
- Register: `/auth/register`

### Customer Routes (Requires Auth)
- Dashboard: `/customer/dashboard`
- Orders: `/customer/orders`
- Profile: `/customer/profile`
- Notifications: `/customer/notifications`

### Dealer Routes (Requires Auth + Approval)
- Dashboard: `/dealer/dashboard`
- Products (Wholesale): `/dealer/products`
- Orders: `/dealer/orders`
- Profile: `/dealer/profile`
- Notifications: `/dealer/notifications`

### Admin Routes
- Login: `/admin/login`
- Dashboard: `/admin/dashboard`
- Products: `/admin/products`
- Categories: `/admin/categories`
- Orders: `/admin/orders`
- Dealers: `/admin/dealers`
- Customers: `/admin/customers`
- Reports: `/admin/reports`
- Settings: `/admin/settings`

---

## ⚙️ Quick Configuration

### Update Site Settings
1. Login as admin
2. Go to Settings (`/admin/settings`)
3. Update:
   - Site name and contact info
   - Tax rate (default: 18%)
   - Shipping charges (default: ₹50)
   - Free shipping threshold (default: ₹1000)
   - Payment methods

### Add Products with Dual Pricing
1. Go to `/admin/products/create`
2. Fill product details
3. Set **two prices**:
   - **Price:** Retail price (e.g., ₹1000)
   - **Dealer Price:** Wholesale price (e.g., ₹750)
4. Set stock quantity
5. Assign category
6. Save

### Approve a Dealer
1. Go to `/admin/dealers`
2. Click on pending registration
3. Review business details
4. Click "Approve" or "Reject"
5. Dealer receives notification
6. If approved, they can now see wholesale prices

---

## 🔍 Troubleshooting

### Issue: Settings not saving
**Solution:** Clear cache
```bash
php artisan cache:clear
php artisan config:clear
```

### Issue: PDF invoices not generating
**Solution:** Ensure dompdf is installed
```bash
composer require barryvdh/laravel-dompdf
```

### Issue: Migrations failing
**Solution:** Fresh migration
```bash
php artisan migrate:fresh
php artisan db:seed
```

### Issue: Admin can't login
**Solution:** Run admin seeder
```bash
php artisan db:seed --class=AdminUserSeeder
```

---

## 📊 Understanding the Dual Pricing System

### How it Works

**For Customers:**
- See only retail prices
- Example: Product shows ₹1000

**For Dealers (Before Approval):**
- See only retail prices
- Message: "Register as dealer for wholesale prices"

**For Dealers (After Approval):**
- See dealer prices with discount badges
- Example: ~~₹1000~~ **₹750** (25% OFF)
- Can place bulk orders

### Setting Up Dual Pricing

When adding a product:
1. **Price:** ₹1000 (what customers pay)
2. **Sale Price:** ₹900 (optional customer discount)
3. **Dealer Price:** ₹750 (what dealers pay)
4. **Dealer Sale Price:** ₹700 (optional dealer discount)

The system automatically:
- Calculates discount percentages
- Shows appropriate prices based on user role
- Applies correct pricing in cart and checkout
- Displays on invoices correctly

---

## 🎨 Customization Tips

### Change Colors
Edit `resources/views/admin/layout.blade.php` (CSS variables):
```css
:root {
    --primary-color: #2c3e50;
    --accent-color: #3498db;
}
```

### Add More Payment Methods
1. Go to `/admin/settings`
2. Update "Payment Methods" (it's a JSON array)
3. Save

### Modify Email Templates
When you integrate email:
- Create views in `resources/views/emails/`
- Use Laravel's Mailable classes
- Configure mail settings in `.env`

---

## ✅ Features Checklist

Test each feature to ensure everything works:

- [ ] Customer registration and login
- [ ] Dealer registration and approval
- [ ] Admin login and dashboard
- [ ] Product browsing (retail prices)
- [ ] Cart and checkout
- [ ] Order placement
- [ ] Order status updates
- [ ] Dual pricing display
- [ ] Dealer product catalog
- [ ] Invoice download (PDF)
- [ ] Reports and analytics
- [ ] Notifications
- [ ] Settings configuration

---

## 🎉 You're All Set!

Your eCommerce platform is now ready to use. Explore the admin dashboard to:
1. Add your agricultural products
2. Set up dual pricing
3. Configure site settings
4. Start receiving customer and dealer registrations

For detailed documentation, see `PROJECT_OVERVIEW.md`

**Happy Selling! 🚜🌾**


















