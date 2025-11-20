# 🔧 Environment Configuration Guide
## Nexus Agriculture eCommerce Platform

This document details all environment variables needed for the application.

---

## 📝 Creating Your .env File

Copy this content to your `.env` file:

```env
# ==============================================
# NEXUS AGRICULTURE ECOMMERCE - ENVIRONMENT CONFIG
# ==============================================

# ----------------------------------------------
# APPLICATION SETTINGS
# ----------------------------------------------
APP_NAME="Nexus Agriculture"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=Asia/Kolkata
APP_URL=http://localhost:8000
ASSET_URL=

# ----------------------------------------------
# DATABASE CONFIGURATION
# ----------------------------------------------
# For local development (SQLite)
DB_CONNECTION=sqlite

# For production (MySQL) - RECOMMENDED
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=nexus_agriculture
# DB_USERNAME=your_database_user
# DB_PASSWORD=your_database_password

# ----------------------------------------------
# CACHE & SESSION CONFIGURATION
# ----------------------------------------------
CACHE_DRIVER=file
SESSION_DRIVER=file
FILESYSTEM_DISK=public
SESSION_LIFETIME=120

# For production - RECOMMENDED
# CACHE_DRIVER=redis
# SESSION_DRIVER=redis

# ----------------------------------------------
# QUEUE CONFIGURATION
# ----------------------------------------------
QUEUE_CONNECTION=sync

# For production - RECOMMENDED
# QUEUE_CONNECTION=redis

# ----------------------------------------------
# REDIS CONFIGURATION
# ----------------------------------------------
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# ----------------------------------------------
# MAIL CONFIGURATION
# ----------------------------------------------
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# ----------------------------------------------
# PAYMENT GATEWAY CONFIGURATION
# ----------------------------------------------
RAZORPAY_KEY=
RAZORPAY_SECRET=

# ----------------------------------------------
# APPLICATION BUSINESS SETTINGS
# ----------------------------------------------
TAX_RATE=18
CURRENCY=INR
CURRENCY_SYMBOL=₹
SHIPPING_CHARGE=100
FREE_SHIPPING_THRESHOLD=5000

# ----------------------------------------------
# LOGGING
# ----------------------------------------------
LOG_CHANNEL=stack
LOG_LEVEL=debug
```

---

## 🔑 Variable Descriptions

### Application Settings

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `APP_NAME` | Yes | "Nexus Agriculture" | Application name displayed in UI |
| `APP_ENV` | Yes | local | Environment: local, staging, production |
| `APP_KEY` | Yes | - | Encryption key (generate with `php artisan key:generate`) |
| `APP_DEBUG` | Yes | true | Debug mode (MUST be false in production) |
| `APP_URL` | Yes | http://localhost:8000 | Base URL of your application |
| `APP_TIMEZONE` | No | UTC | Application timezone |

### Database Settings

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `DB_CONNECTION` | Yes | sqlite | Database driver: sqlite, mysql, pgsql |
| `DB_HOST` | If MySQL | 127.0.0.1 | Database server hostname |
| `DB_PORT` | If MySQL | 3306 | Database server port |
| `DB_DATABASE` | Yes | - | Database name or path to SQLite file |
| `DB_USERNAME` | If MySQL | - | Database username |
| `DB_PASSWORD` | If MySQL | - | Database password |

### Cache & Session

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `CACHE_DRIVER` | Yes | file | Cache driver: file, redis, memcached |
| `SESSION_DRIVER` | Yes | file | Session driver: file, redis, database |
| `SESSION_LIFETIME` | Yes | 120 | Session lifetime in minutes |

### Redis Configuration

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `REDIS_HOST` | If using Redis | 127.0.0.1 | Redis server hostname |
| `REDIS_PORT` | If using Redis | 6379 | Redis server port |
| `REDIS_PASSWORD` | If using Redis | null | Redis password |

### Mail Configuration

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `MAIL_MAILER` | Yes | log | Mail driver: smtp, sendmail, log |
| `MAIL_HOST` | If SMTP | - | SMTP server hostname |
| `MAIL_PORT` | If SMTP | 587 | SMTP server port |
| `MAIL_USERNAME` | If SMTP | - | SMTP username |
| `MAIL_PASSWORD` | If SMTP | - | SMTP password |
| `MAIL_ENCRYPTION` | If SMTP | tls | Encryption: tls, ssl |
| `MAIL_FROM_ADDRESS` | Yes | - | Default sender email address |
| `MAIL_FROM_NAME` | Yes | - | Default sender name |

### Payment Gateway

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `RAZORPAY_KEY` | For Razorpay | - | Razorpay API key |
| `RAZORPAY_SECRET` | For Razorpay | - | Razorpay API secret |

### Business Settings

| Variable | Required | Default | Description |
|----------|----------|---------|-------------|
| `TAX_RATE` | Yes | 18 | Tax/GST percentage |
| `CURRENCY` | Yes | INR | Currency code (ISO 4217) |
| `CURRENCY_SYMBOL` | Yes | ₹ | Currency symbol for display |
| `SHIPPING_CHARGE` | Yes | 100 | Default shipping charge |
| `FREE_SHIPPING_THRESHOLD` | Yes | 5000 | Minimum order for free shipping |

---

## 🔄 Environment-Specific Configurations

### Local Development

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

MAIL_MAILER=log
```

### Staging Environment

```env
APP_ENV=staging
APP_DEBUG=true
APP_URL=https://staging.yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=nexus_staging
DB_USERNAME=staging_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### Production Environment

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=nexus_production
DB_USERNAME=production_user
DB_PASSWORD=very_secure_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_SECURE_COOKIE=true

MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls

RAZORPAY_KEY=rzp_live_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxx

LOG_CHANNEL=daily
LOG_LEVEL=error
```

---

## 📧 Email Service Configuration Examples

### Gmail SMTP

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_specific_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

**Note:** Use App-Specific Password, not your regular Gmail password.

### SendGrid

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
```

### AWS SES

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your_aws_access_key
AWS_SECRET_ACCESS_KEY=your_aws_secret_key
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
```

### Mailgun

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=mg.yourdomain.com
MAILGUN_SECRET=your_mailgun_api_key
MAILGUN_ENDPOINT=api.mailgun.net
```

---

## 💳 Payment Gateway Configuration

### Razorpay (Recommended for India)

**Test Mode:**
```env
RAZORPAY_KEY=rzp_test_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxx
```

**Live Mode:**
```env
RAZORPAY_KEY=rzp_live_xxxxxxxxxx
RAZORPAY_SECRET=xxxxxxxxxxxxxx
```

Get keys from: https://dashboard.razorpay.com/app/keys

### Stripe (International)

```env
STRIPE_KEY=pk_test_xxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_test_xxxxxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxxxxx
```

For live:
```env
STRIPE_KEY=pk_live_xxxxxxxxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxxxxxxxx
```

---

## 🗄️ Database Configuration Examples

### SQLite (Development Only)

```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite
```

Or use relative path:
```env
DB_CONNECTION=sqlite
# DB_DATABASE will default to database/database.sqlite
```

### MySQL

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexus_agriculture
DB_USERNAME=nexus_user
DB_PASSWORD=your_password
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### PostgreSQL

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=nexus_agriculture
DB_USERNAME=nexus_user
DB_PASSWORD=your_password
DB_SCHEMA=public
DB_SSLMODE=prefer
```

---

## 🔐 Security Best Practices

### 1. Generate Strong APP_KEY

```bash
php artisan key:generate
```

### 2. Use Strong Database Passwords

```
# Bad
DB_PASSWORD=123456

# Good
DB_PASSWORD=X9$mK2pL#nQ8vR4wY6zT@hJ3sF7gD0bN
```

### 3. Production Security Settings

```env
APP_ENV=production
APP_DEBUG=false
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

### 4. File Permissions

```bash
# Set proper permissions
chmod 644 .env
chown www-data:www-data .env
```

### 5. Never Commit .env

Ensure `.gitignore` includes:
```
.env
.env.backup
.env.production
```

---

## 🚀 Quick Setup Commands

### Initial Setup

```bash
# 1. Create .env file
cp ENV_CONFIG_GUIDE.md .env  # Copy the configuration
nano .env  # Edit with your values

# 2. Generate application key
php artisan key:generate

# 3. Create database (if using SQLite)
touch database/database.sqlite

# 4. Run migrations
php artisan migrate

# 5. Seed database
php artisan db:seed

# 6. Create storage link
php artisan storage:link

# 7. Clear and cache config
php artisan config:cache
```

### Production Deployment

```bash
# 1. Set production environment
nano .env  # Update all production values

# 2. Install dependencies
composer install --optimize-autoloader --no-dev

# 3. Run migrations
php artisan migrate --force

# 4. Seed production data
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=AgricultureCategorySeeder

# 5. Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 6. Build assets
npm install
npm run build

# 7. Set permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔍 Troubleshooting

### Issue: "No application encryption key has been specified"

```bash
php artisan key:generate
```

### Issue: Database connection failed

1. Check database credentials in `.env`
2. Ensure database server is running
3. Test connection:
```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

### Issue: Permission denied on storage

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
```

### Issue: Mail not sending

1. Verify SMTP credentials
2. Check firewall/port access
3. Test email:
```bash
php artisan tinker
>>> Mail::raw('Test', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

---

## 📝 Environment Validation

Create a command to validate your configuration:

```bash
php artisan about
```

This will show:
- Application name, environment, and debug status
- PHP version and extensions
- Database connection status
- Cache and session drivers
- Queue connection

---

## 🔗 Additional Resources

- [Laravel Configuration Documentation](https://laravel.com/docs/configuration)
- [Laravel Environment Configuration](https://laravel.com/docs/configuration#environment-configuration)
- [Razorpay Documentation](https://razorpay.com/docs/)
- [SendGrid PHP Documentation](https://docs.sendgrid.com/for-developers/sending-email/php)

---

**Last Updated:** October 29, 2025  
**Version:** 1.0.0

For support, refer to the main documentation or contact the development team.

















