# ✅ Production Deployment Checklist
## Nexus Agriculture eCommerce Platform

**Quick Reference Guide for Going Live**

---

## 📋 PRE-DEPLOYMENT CHECKLIST

### 🔴 Phase 1: Critical Requirements (Must Complete)

#### Security Configuration
- [ ] Create `.env` file with production values (see `ENV_CONFIG_GUIDE.md`)
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY` with `php artisan key:generate`
- [ ] Use strong database password (minimum 16 characters)
- [ ] Enable HTTPS/SSL certificate
- [ ] Force HTTPS redirect in production
- [ ] Add security headers middleware
- [ ] Implement rate limiting on login routes
- [ ] Review all file upload validations
- [ ] Set proper file permissions (755/644)

#### Database Setup
- [ ] Switch from SQLite to MySQL/PostgreSQL
- [ ] Create production database
- [ ] Create database user with proper permissions
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Add database indexes (see production readiness report)
- [ ] Seed initial data: `php artisan db:seed --class=AdminUserSeeder`
- [ ] Seed categories: `php artisan db:seed --class=AgricultureCategorySeeder`
- [ ] Test database connection
- [ ] Configure database backup strategy

#### Error Handling
- [ ] Create custom 404 error page
- [ ] Create custom 500 error page
- [ ] Create custom 503 error page
- [ ] Configure production logging (daily rotation)
- [ ] Set `LOG_LEVEL=error`
- [ ] Test error pages work correctly

#### Email Configuration
- [ ] Setup SMTP service (SendGrid, AWS SES, or Mailgun)
- [ ] Configure mail settings in `.env`
- [ ] Test email delivery
- [ ] Setup email notifications for orders
- [ ] Configure queue worker for email sending
- [ ] Add email templates for all notifications

#### File Uploads
- [ ] Validate file types (images only)
- [ ] Set maximum file size limits
- [ ] Sanitize filenames
- [ ] Store uploads outside public directory
- [ ] Create storage symbolic link: `php artisan storage:link`

#### Asset Optimization
- [ ] Run `npm run build` for production
- [ ] Minify CSS and JavaScript
- [ ] Optimize and compress images
- [ ] Setup CDN (optional but recommended)
- [ ] Enable GZIP compression on server

---

### ⚠️ Phase 2: High Priority (Strongly Recommended)

#### Caching Setup
- [ ] Install and configure Redis
- [ ] Set `CACHE_DRIVER=redis`
- [ ] Set `SESSION_DRIVER=redis`
- [ ] Set `QUEUE_CONNECTION=redis`
- [ ] Test Redis connection
- [ ] Implement query caching for frequently accessed data

#### Payment Gateway
- [ ] Choose payment provider (Razorpay recommended for India)
- [ ] Create merchant account
- [ ] Get API keys (test and live)
- [ ] Configure payment settings in `.env`
- [ ] Test payment flow in sandbox mode
- [ ] Implement payment verification
- [ ] Add payment success/failure pages
- [ ] Test with real payment (small amount)

#### Backup System
- [ ] Create database backup script
- [ ] Create file backup script
- [ ] Schedule automated daily backups
- [ ] Test backup restoration
- [ ] Setup offsite backup storage (AWS S3, etc.)
- [ ] Configure backup retention policy (30 days)
- [ ] Document backup procedures

#### SSL/HTTPS
- [ ] Purchase or get free SSL certificate (Let's Encrypt)
- [ ] Install SSL certificate on server
- [ ] Update `APP_URL` to use `https://`
- [ ] Test SSL configuration (SSLLabs.com)
- [ ] Setup automatic SSL renewal
- [ ] Configure HSTS headers

#### Server Configuration
- [ ] Configure PHP-FPM properly
- [ ] Enable OPcache for PHP
- [ ] Setup firewall (UFW or similar)
- [ ] Configure fail2ban for brute-force protection
- [ ] Setup log rotation
- [ ] Optimize MySQL/PostgreSQL settings
- [ ] Configure Nginx/Apache properly

---

### 📊 Phase 3: Recommended (For Best Performance)

#### Performance Optimization
- [ ] Run `composer install --optimize-autoloader --no-dev`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Run `php artisan view:cache`
- [ ] Run `php artisan event:cache`
- [ ] Enable query result caching
- [ ] Implement lazy loading for images
- [ ] Setup database connection pooling

#### Monitoring & Logging
- [ ] Setup uptime monitoring (UptimeRobot, Pingdom)
- [ ] Configure error tracking (Sentry, Bugsnag)
- [ ] Setup performance monitoring (New Relic, optional)
- [ ] Configure log aggregation
- [ ] Setup email alerts for critical errors
- [ ] Monitor disk space usage
- [ ] Monitor database performance

#### SEO & Analytics
- [ ] Add proper meta tags to all pages
- [ ] Create and submit sitemap.xml
- [ ] Configure robots.txt
- [ ] Add Open Graph tags
- [ ] Setup Google Analytics (optional)
- [ ] Setup Google Search Console
- [ ] Add structured data markup
- [ ] Test mobile responsiveness

#### Security Enhancements
- [ ] Implement CORS properly
- [ ] Add Content Security Policy headers
- [ ] Enable XSS protection headers
- [ ] Configure secure cookies
- [ ] Review and test all authentication flows
- [ ] Implement 2FA for admin (future enhancement)
- [ ] Regular security audits

---

## 🖥️ SERVER SETUP CHECKLIST

### Minimum Server Requirements
- [ ] Ubuntu 22.04 LTS (or similar)
- [ ] PHP 8.2 or higher
- [ ] MySQL 8.0+ or PostgreSQL 13+
- [ ] Nginx 1.18+ or Apache 2.4+
- [ ] 2GB RAM minimum (4GB recommended)
- [ ] 20GB storage minimum (50GB recommended)
- [ ] Redis 6.0+
- [ ] SSL certificate

### Software Installation
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP and extensions
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-gd php8.2-curl php8.2-zip php8.2-redis -y

# Install MySQL
sudo apt install mysql-server -y

# Install Redis
sudo apt install redis-server -y

# Install Nginx
sudo apt install nginx -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js and NPM
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Install Supervisor (for queues)
sudo apt install supervisor -y
```

### Database Setup
```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p

CREATE DATABASE nexus_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'nexus_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON nexus_production.* TO 'nexus_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Application Deployment
```bash
# Navigate to web directory
cd /var/www

# Clone repository (or upload files)
sudo git clone https://github.com/yourusername/nexus.git nexus
cd nexus

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Configure environment
cp ENV_CONFIG_GUIDE.md .env
nano .env  # Edit with production values

# Generate application key
php artisan key:generate

# Setup database
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan db:seed --class=AgricultureCategorySeeder
php artisan storage:link

# Optimize application
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set permissions
sudo chown -R www-data:www-data /var/www/nexus
sudo chmod -R 755 /var/www/nexus
sudo chmod -R 775 storage bootstrap/cache
```

### Nginx Configuration
```bash
# Create Nginx config
sudo nano /etc/nginx/sites-available/nexus

# Paste this configuration:
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name yourdomain.com www.yourdomain.com;
    
    # Redirect to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name yourdomain.com www.yourdomain.com;
    root /var/www/nexus/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    index index.php;
    charset utf-8;

    # Logs
    access_log /var/log/nginx/nexus-access.log;
    error_log /var/log/nginx/nexus-error.log;

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
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static assets
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml|svg|woff|woff2|ttf|eot)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|svn) {
        deny all;
        return 404;
    }

    # Upload size limits
    client_max_body_size 10M;
}
```

```bash
# Enable site and restart Nginx
sudo ln -s /etc/nginx/sites-available/nexus /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### SSL Certificate Installation
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtain certificate
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run
```

### Queue Worker Setup
```bash
# Create supervisor config
sudo nano /etc/supervisor/conf.d/nexus-worker.conf

# Paste this configuration:
```

```ini
[program:nexus-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/nexus/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/nexus/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start worker
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start nexus-worker:*
```

### Cron Job Setup
```bash
# Edit crontab
sudo crontab -e -u www-data

# Add this line:
* * * * * cd /var/www/nexus && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 TESTING CHECKLIST

### Functionality Testing
- [ ] Test user registration
- [ ] Test user login/logout
- [ ] Test password reset
- [ ] Test admin login
- [ ] Test product browsing
- [ ] Test product search
- [ ] Test category filtering
- [ ] Test add to cart
- [ ] Test cart updates
- [ ] Test checkout process
- [ ] Test payment gateway
- [ ] Test order confirmation email
- [ ] Test dealer registration
- [ ] Test dealer approval workflow
- [ ] Test dealer dashboard access
- [ ] Test dual pricing display
- [ ] Test invoice generation
- [ ] Test admin dashboard
- [ ] Test product management
- [ ] Test order management
- [ ] Test customer management
- [ ] Test reports generation

### Security Testing
- [ ] Test SQL injection prevention
- [ ] Test XSS prevention
- [ ] Test CSRF protection
- [ ] Test authentication bypass attempts
- [ ] Test file upload restrictions
- [ ] Test rate limiting
- [ ] Test secure cookies
- [ ] Test HTTPS enforcement
- [ ] Verify password hashing
- [ ] Test session timeout

### Performance Testing
- [ ] Test page load times (<2 seconds)
- [ ] Test with 100 concurrent users
- [ ] Test database query performance
- [ ] Test cache effectiveness
- [ ] Test asset loading speed
- [ ] Test mobile performance
- [ ] Monitor memory usage
- [ ] Monitor CPU usage

### Browser Testing
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Mobile Safari (iOS)
- [ ] Mobile Chrome (Android)

---

## 📊 POST-DEPLOYMENT CHECKLIST

### Immediate Actions
- [ ] Verify site is accessible via HTTPS
- [ ] Test complete user registration and order flow
- [ ] Check all pages load correctly
- [ ] Verify emails are being sent
- [ ] Test payment processing
- [ ] Check error logs for issues
- [ ] Verify backups are running
- [ ] Test 404 and 500 error pages

### First 24 Hours
- [ ] Monitor error logs continuously
- [ ] Check server resources (CPU, memory, disk)
- [ ] Monitor website uptime
- [ ] Track any user-reported issues
- [ ] Verify all cron jobs are running
- [ ] Check queue workers are processing
- [ ] Monitor payment transactions
- [ ] Review backup completion

### First Week
- [ ] Review and fix any reported bugs
- [ ] Analyze user behavior (analytics)
- [ ] Monitor conversion rates
- [ ] Check for broken links
- [ ] Review server performance
- [ ] Optimize slow queries
- [ ] Update documentation with learnings
- [ ] Plan for enhancements

---

## 🆘 TROUBLESHOOTING GUIDE

### Common Issues

**Issue: 500 Internal Server Error**
```bash
# Check logs
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log

# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Issue: Permission Denied**
```bash
sudo chown -R www-data:www-data /var/www/nexus
sudo chmod -R 755 /var/www/nexus
sudo chmod -R 775 storage bootstrap/cache
```

**Issue: Database Connection Failed**
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check credentials in .env
# Ensure MySQL is running
sudo systemctl status mysql
```

**Issue: SSL Certificate Error**
```bash
# Renew certificate
sudo certbot renew

# Check certificate validity
sudo certbot certificates
```

**Issue: Queue Not Processing**
```bash
# Check supervisor status
sudo supervisorctl status

# Restart worker
sudo supervisorctl restart nexus-worker:*

# Check worker logs
tail -f storage/logs/worker.log
```

---

## 📞 SUPPORT CONTACTS

### Emergency Contacts
- **Hosting Provider Support:** [Contact Info]
- **Domain Registrar:** [Contact Info]
- **Payment Gateway Support:** [Contact Info]
- **Email Service Support:** [Contact Info]

### Documentation References
- **Main Documentation:** `README.md`
- **Production Readiness:** `PRODUCTION_READINESS_REPORT.md`
- **Environment Config:** `ENV_CONFIG_GUIDE.md`
- **Deployment Guide:** `DEPLOYMENT_GUIDE.md`
- **Technical Docs:** `TECHNICAL_DOCS.md`
- **User Guide:** `USER_GUIDE.md`

---

## ✅ FINAL SIGN-OFF

- [ ] All critical items completed
- [ ] All high priority items completed
- [ ] Testing completed successfully
- [ ] Documentation updated
- [ ] Team trained on admin panel
- [ ] Backup system verified
- [ ] Monitoring setup complete
- [ ] SSL certificate installed and tested
- [ ] Payment gateway tested with real transaction
- [ ] Emergency rollback plan documented

**Deployed By:** ___________________  
**Date:** ___________________  
**Deployment Time:** ___________________  
**Sign-off:** ___________________

---

**Last Updated:** October 29, 2025  
**Version:** 1.0.0

🎉 **Good luck with your deployment!**

















