# Nexus Agriculture - eCommerce Platform

A complete eCommerce solution for agricultural products with dual pricing (retail & wholesale), built with Laravel.

---

## 🌟 Key Features

### Multi-Role System
- **Customers**: Browse and purchase at retail prices
- **Dealers/Wholesalers**: Access wholesale pricing after admin approval
- **Admin**: Centralized management dashboard

### Product Management
- ✅ Dual pricing system (Retail & Dealer prices)
- ✅ Product categories and filtering
- ✅ Featured products and best sellers
- ✅ Stock management
- ✅ Product images and descriptions
- ✅ Search functionality

### Shopping Experience
- ✅ Shopping cart (session-based)
- ✅ Wishlist (authenticated users)
- ✅ Role-based pricing display
- ✅ Responsive product catalog
- ✅ Quick add to cart
- ✅ Product detail pages

### User Features
- ✅ Customer registration & login
- ✅ Dealer application system
- ✅ Order history and tracking
- ✅ Profile management
- ✅ Notifications system

### Admin Dashboard
- ✅ Product management (CRUD)
- ✅ Category management
- ✅ Dealer approval system
- ✅ Order management
- ✅ Customer management
- ✅ Reports & analytics
- ✅ Site settings

### Dealer Dashboard
- ✅ Wholesale product catalog
- ✅ Bulk ordering capability
- ✅ Order history
- ✅ Invoice generation
- ✅ Profile management
- ✅ Dealer-specific notifications

---

## 🛠️ Tech Stack

- **Framework**: Laravel 11.x
- **Frontend**: Bootstrap 5, Blade Templates
- **Database**: MySQL
- **Authentication**: Laravel Breeze
- **PDF Generation**: DomPDF
- **Icons**: Font Awesome, SVG Icons
- **Styling**: Custom CSS + Bootstrap

---

## 📦 Installation

### Prerequisites
- PHP 8.2+
- Composer
- MySQL 5.7+
- Node.js & NPM (optional for asset compilation)

### Setup Steps

1. **Clone the repository**
```bash
git clone <your-repo-url>
cd nexus
```

2. **Install dependencies**
```bash
composer install
```

3. **Configure environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexus_agriculture
DB_USERNAME=root
DB_PASSWORD=
```

5. **Run migrations**
```bash
php artisan migrate
```

6. **Seed test data (optional)**
```bash
php artisan db:seed --class=TestUsersSeeder
```

7. **Create storage link**
```bash
php artisan storage:link
```

8. **Start development server**
```bash
php artisan serve
```

9. **Visit the application**
```
http://127.0.0.1:8000
```

---

## 🔐 Test Credentials

### Admin Account
- **URL**: `http://127.0.0.1:8000/admin/login`
- **Email**: admin@nexus.com
- **Password**: admin123

### Customer Account
- **URL**: `http://127.0.0.1:8000/auth/customer-login`
- **Email**: customer@nexus.com
- **Password**: customer123

### Approved Dealer Account
- **URL**: `http://127.0.0.1:8000/auth/dealer-login`
- **Email**: dealer@nexus.com
- **Password**: dealer123

### Pending Dealer Account
- **URL**: `http://127.0.0.1:8000/auth/dealer-login`
- **Email**: pending@nexus.com
- **Password**: pending123

---

## 📁 Project Structure

```
nexus/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/              # Admin controllers
│   │   │   ├── Auth/               # Authentication controllers
│   │   │   ├── AgricultureProductController.php
│   │   │   ├── AgricultureCartController.php
│   │   │   ├── WishlistController.php
│   │   │   └── ...
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php
│   │   ├── AgricultureProduct.php
│   │   ├── AgricultureCategory.php
│   │   ├── AgricultureOrder.php
│   │   ├── DealerRegistration.php
│   │   ├── Wishlist.php
│   │   └── ...
│   └── Providers/
│       └── AppServiceProvider.php  # View composers
├── database/
│   ├── migrations/
│   └── seeders/
│       └── TestUsersSeeder.php
├── resources/
│   └── views/
│       ├── admin/                  # Admin panel views
│       ├── auth/                   # Authentication views
│       ├── customer/               # Customer dashboard
│       ├── dealer/                 # Dealer dashboard
│       ├── agriculture/            # Public product views
│       ├── pages/                  # Static pages
│       ├── components/             # Reusable components
│       └── layouts/                # Layout templates
├── routes/
│   ├── web.php                     # Public routes
│   └── admin.php                   # Admin routes
└── public/
    └── assets/                     # Static assets
```

---

## 🚀 Deployment

### Production Setup

1. **Update `.env` for production**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

2. **Optimize Laravel**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

3. **Set proper permissions**
```bash
chmod -R 755 storage bootstrap/cache
```

4. **Configure web server** (Apache/Nginx)
Point document root to `/public` directory

5. **Setup SSL certificate** (Let's Encrypt recommended)

6. **Enable maintenance mode during updates**
```bash
php artisan down
# Update code
php artisan up
```

---

## 🔧 Configuration

### Site Settings
Access admin panel → Settings to configure:
- Site name and logo
- Tax settings
- Delivery charges
- Payment options

### Email Configuration
Update `.env` with your email provider:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@nexusag.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 📊 Business Logic

### Pricing System
- Each product has **two prices**: Retail and Dealer
- Pricing automatically displays based on user role:
  - **Guests/Customers**: See retail prices
  - **Approved Dealers**: See wholesale/dealer prices
  - Prices calculated in `AgricultureProduct::getPriceForUser()`

### Dealer Approval Workflow
1. User registers as customer
2. Applies for dealer account (provides business details)
3. Admin receives notification
4. Admin reviews and approves/rejects
5. Dealer receives notification
6. Approved dealers see wholesale prices

### Order Management
- Orders tracked by unique order number
- Status: Pending → Confirmed → Shipped → Delivered
- Invoice generation for all orders
- Email notifications (when configured)

---

## 🧪 Testing

See `TESTING_GUIDE.md` for comprehensive testing instructions.

### Quick Test
```bash
# Login as admin
http://127.0.0.1:8000/admin/login

# Browse products as customer
http://127.0.0.1:8000

# Add to cart (no login required)
# Add to wishlist (login required)

# View cart
http://127.0.0.1:8000/agriculture/cart

# View wishlist
http://127.0.0.1:8000/wishlist
```

---

## 🤝 Contributing

This is a private project. For contributions or inquiries, contact the project owner.

---

## 📄 License

Proprietary - All rights reserved.

---

## 👨‍💻 Support

For support or inquiries:
- **Email**: support@nexusag.com
- **Admin Panel**: Login to submit tickets

---

## 🎯 Roadmap

### Upcoming Features
- [ ] Payment gateway integration (Razorpay/Stripe)
- [ ] Real-time order tracking
- [ ] Email notifications
- [ ] SMS notifications
- [ ] Advanced analytics dashboard
- [ ] Mobile app (React Native)
- [ ] Multi-language support
- [ ] Inventory alerts automation
- [ ] Bulk import/export
- [ ] Customer reviews & ratings

---

## 📸 Screenshots

_(Add screenshots here after deployment)_

### Home Page
![Home Page](#)

### Admin Dashboard
![Admin Dashboard](#)

### Product Catalog
![Product Catalog](#)

### Dealer Dashboard
![Dealer Dashboard](#)

---

## ⚙️ System Requirements

### Minimum
- PHP 8.2
- MySQL 5.7
- 512 MB RAM
- 1 GB Disk Space

### Recommended
- PHP 8.3+
- MySQL 8.0+
- 2 GB RAM
- 10 GB Disk Space
- SSD Storage

---

## 🔒 Security

- CSRF protection enabled
- SQL injection prevention (Eloquent ORM)
- Password hashing (bcrypt)
- XSS protection
- Session security
- Role-based access control

---

## 📱 Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS & Android)

---

## 🌐 Demo

**Live Demo**: [Coming Soon]

---

**Built with ❤️ for Agriculture Industry**

*Nexus Agriculture - Connecting Farmers, Dealers, and Customers*
