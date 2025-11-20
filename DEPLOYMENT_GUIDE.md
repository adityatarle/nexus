# Agriculture E-Commerce Platform - Deployment Guide

**Version:** 1.0.0  
**Last Updated:** October 28, 2025

---

## 📋 Table of Contents

1. [Prerequisites](#prerequisites)
2. [Environment Configuration](#environment-configuration)
3. [Local Development Setup](#local-development-setup)
4. [Production Deployment](#production-deployment)
5. [Post-Deployment Steps](#post-deployment-steps)
6. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Software
- **PHP**: 8.2 or higher
- **Composer**: Latest version
- **Database**: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3
- **Node.js**: 18+ and NPM
- **Web Server**: Apache 2.4+ or Nginx 1.18+

### Optional But Recommended
- Redis (for caching and sessions)
- Supervisor (for queue workers)
- SSL Certificate (Let's Encrypt)

---

## Environment Configuration

### Create `.env` File

Copy the content below to your `.env` file in the project root:

```env
# Application Configuration
APP_NAME="Agriculture Equipment Store"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Logging
LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Database Configuration (MySQL Example)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agriculture_ecommerce
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_password

# Cache Configuration (Redis Recommended for Production)
CACHE_DRIVER=redis
FILESYSTEM_DISK=public

# Queue Configuration
QUEUE_CONNECTION=redis

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Email Configuration (SendGrid Example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your_sendgrid_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Agriculture Equipment Store"

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Application Settings
TAX_RATE=0.00
CURRENCY=INR
CURRENCY_SYMBOL=₹
```

### Generate Application Key
```bash
php artisan key:generate
```

---

## Local Development Setup

### 1. Clone the Repository
```bash
git clone https://github.com/yourusername/agriculture-ecommerce.git
cd agriculture-ecommerce
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Configure Environment
```bash
cp .env.example .env
# Edit .env with your local database credentials
php artisan key:generate
```

### 4. Setup Database
```bash
# Create database
# For SQLite (Development)
touch database/database.sqlite

# For MySQL
# mysql -u root -p
# CREATE DATABASE agriculture_ecommerce;

# Run migrations
php artisan migrate:fresh --seed
```

### 5. Create Storage Link
```bash
php artisan storage:link
```

### 6. Build Assets
```bash
npm run dev
```

### 7. Start Development Server
```bash
php artisan serve
```

Access the application at: `http://localhost:8000`

### Default Credentials

**Admin Login:**
- URL: `/admin/login`
- Email: `admin@agriculture.com`
- Password: `password`

⚠️ **IMPORTANT:** Change the admin password immediately after first login!

---

## Production Deployment

### Shared Hosting (cPanel/Plesk)

#### 1. Upload Files
Upload all files to your hosting account (typically `public_html` or `www` directory).

#### 2. Move Public Folder Contents
Move contents of `public` folder to your web root. Update `index.php` paths:

```php
// From:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// To: (if files are one level up)
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
```

#### 3. Set Permissions
```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

#### 4. Configure .htaccess
Ensure `.htaccess` exists in your web root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

### VPS/Cloud Server (Ubuntu 22.04)

#### 1. Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### 2. Install LAMP Stack
```bash
# Install PHP and extensions
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-gd php8.2-curl php8.2-zip -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Nginx
sudo apt install nginx -y
```

#### 3. Install Composer
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

#### 4. Configure MySQL
```bash
sudo mysql -u root -p

CREATE DATABASE agriculture_ecommerce;
CREATE USER 'agriuser'@'localhost' IDENTIFIED BY 'secure_password';
GRANT ALL PRIVILEGES ON agriculture_ecommerce.* TO 'agriuser'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

#### 5. Clone and Setup Application
```bash
cd /var/www
sudo git clone https://github.com/yourusername/agriculture-ecommerce.git
cd agriculture-ecommerce

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Configure environment
cp .env.example .env
nano .env  # Update with production values

# Generate key and setup
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=AgricultureCategorySeeder
php artisan db:seed --class=AgricultureProductSeeder
php artisan storage:link

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/agriculture-ecommerce
sudo chmod -R 755 /var/www/agriculture-ecommerce
sudo chmod -R 775 /var/www/agriculture-ecommerce/storage
sudo chmod -R 775 /var/www/agriculture-ecommerce/bootstrap/cache
```

#### 6. Configure Nginx
Create `/etc/nginx/sites-available/agriculture-ecommerce`:

```nginx
server {
    listen 80;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/agriculture-ecommerce/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/agriculture-ecommerce /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

#### 7. Install SSL Certificate
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

#### 8. Setup Queue Worker (Optional)
Create `/etc/supervisor/conf.d/agriculture-worker.conf`:

```ini
[program:agriculture-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/agriculture-ecommerce/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/agriculture-ecommerce/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start agriculture-worker:*
```

---

## Post-Deployment Steps

### 1. Verify Installation
- ✅ Visit your domain
- ✅ Test admin login
- ✅ Create a test product
- ✅ Test customer registration
- ✅ Test cart and checkout

### 2. Security Checklist
- ✅ Change default admin password
- ✅ Update `APP_KEY` in `.env`
- ✅ Set `APP_DEBUG=false`
- ✅ Configure HTTPS/SSL
- ✅ Setup firewall rules
- ✅ Enable security headers
- ✅ Setup regular backups

### 3. Performance Optimization
```bash
# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

### 4. Setup Backups
```bash
# Database backup script
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/agriculture"
mkdir -p $BACKUP_DIR
mysqldump -u agriuser -p agriculture_ecommerce > $BACKUP_DIR/db_$DATE.sql
gzip $BACKUP_DIR/db_$DATE.sql

# Keep only last 7 days
find $BACKUP_DIR -name "*.gz" -mtime +7 -delete
```

Add to crontab:
```bash
0 2 * * * /path/to/backup.sh
```

### 5. Monitor Application
- Setup log rotation for Laravel logs
- Monitor disk space
- Monitor database performance
- Setup uptime monitoring (UptimeRobot, Pingdom)
- Configure error tracking (Sentry, Bugsnag)

---

## Troubleshooting

### Issue: 500 Internal Server Error

**Solution:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check web server logs
# Apache
tail -f /var/log/apache2/error.log
# Nginx
tail -f /var/log/nginx/error.log

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Issue: Permission Denied

**Solution:**
```bash
sudo chown -R www-data:www-data /var/www/agriculture-ecommerce
sudo chmod -R 755 /var/www/agriculture-ecommerce
sudo chmod -R 775 storage
sudo chmod -R 775 bootstrap/cache
```

### Issue: Database Connection Error

**Solution:**
- Verify database credentials in `.env`
- Check if database exists
- Test connection: `php artisan tinker` then `DB::connection()->getPdo();`
- Check MySQL is running: `sudo systemctl status mysql`

### Issue: Images Not Displaying

**Solution:**
```bash
# Recreate storage link
rm public/storage
php artisan storage:link

# Check permissions
ls -la public/storage
ls -la storage/app/public
```

### Issue: CSS/JS Not Loading

**Solution:**
```bash
# Rebuild assets
npm run build

# Clear browser cache
# Check APP_URL in .env matches your domain
```

---

## Additional Resources

### Useful Commands
```bash
# View application status
php artisan about

# Run migrations
php artisan migrate

# Rollback migration
php artisan migrate:rollback

# Seed database
php artisan db:seed

# Clear all caches
php artisan optimize:clear

# Optimize for production
php artisan optimize

# Schedule commands (add to crontab)
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

### Performance Monitoring
- Enable query logging temporarily to identify slow queries
- Use `php artisan telescope:install` for debugging (dev only)
- Monitor with `php artisan queue:monitor`

### Security Best Practices
1. Keep Laravel and dependencies updated
2. Use HTTPS everywhere
3. Implement rate limiting
4. Validate all user inputs
5. Use prepared statements (Eloquent does this)
6. Setup CORS properly
7. Enable CSRF protection (enabled by default)
8. Use secure session configuration

---

## Support

For issues or questions:
- 📧 Email: support@yourdomain.com
- 📚 Documentation: https://laravel.com/docs
- 🐛 Report bugs: Create an issue in the repository

---

## License

This project is proprietary software. Unauthorized copying or distribution is prohibited.

---

**Last Updated:** October 28, 2025  
**Version:** 1.0.0


















