# 📋 Quick Reference Card - Production Launch
## Nexus Agriculture eCommerce Platform

**Print this page for quick reference during deployment**

---

## 🔴 CRITICAL - Must Complete (7 items)

```
□ 1. Create .env with production values
     • APP_ENV=production
     • APP_DEBUG=false  
     • Strong DB password
     • Configure SMTP email
     • Setup payment gateway keys

□ 2. Security Headers Middleware
     • Create app/Http/Middleware/SecurityHeaders.php
     • Add X-Frame-Options, X-XSS-Protection, CSP
     • Register in bootstrap/app.php

□ 3. Database Optimization
     • Switch from SQLite to MySQL
     • Run migrations: php artisan migrate --force
     • Add indexes (see PRODUCTION_READINESS_REPORT.md)
     • Configure backups

□ 4. Error Handling
     • Create resources/views/errors/404.blade.php
     • Create resources/views/errors/500.blade.php
     • Set LOG_LEVEL=error in .env

□ 5. Rate Limiting
     • Add throttle:5,1 to login routes
     • Add throttle:3,60 to registration
     • Add throttle:3,5 to admin login

□ 6. File Upload Security
     • Validate file types: jpeg,jpg,png,webp
     • Max size: 2MB
     • Sanitize filenames
     • Check dimensions: min 400x400

□ 7. Email Configuration
     • Setup SMTP service (SendGrid/AWS SES)
     • Test email delivery
     • Configure queue: QUEUE_CONNECTION=redis
```

---

## ⚠️ HIGH PRIORITY - Strongly Recommended (5 items)

```
□ 8. Redis Setup
     • Install: composer require predis/predis
     • Set CACHE_DRIVER=redis
     • Set SESSION_DRIVER=redis

□ 9. SSL/HTTPS
     • Install certificate: sudo certbot --nginx
     • Force HTTPS in .env: APP_URL=https://...
     • Test: https://www.ssllabs.com

□ 10. Backup System
     • Create backup script (see guide)
     • Schedule cron: 0 2 * * *
     • Test restoration

□ 11. Payment Gateway
     • Configure Razorpay/Stripe
     • Test in sandbox mode
     • Verify webhooks

□ 12. Performance
     • Run: php artisan config:cache
     • Run: php artisan route:cache
     • Run: php artisan view:cache
     • Run: npm run build
```

---

## 📊 MEDIUM PRIORITY - Recommended (3 items)

```
□ 13. Monitoring
     • Setup UptimeRobot (free)
     • Configure error tracking
     • Add log rotation

□ 14. SEO Basics
     • Add meta tags
     • Create sitemap.xml
     • Configure robots.txt

□ 15. Mobile Testing
     • Test on iOS Safari
     • Test on Android Chrome
     • Verify checkout flow
```

---

## 💻 SERVER SETUP COMMANDS

### Ubuntu 22.04 Quick Setup
```bash
# System update
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install php8.2-fpm php8.2-mysql php8.2-mbstring \
php8.2-xml php8.2-gd php8.2-curl php8.2-zip php8.2-redis -y

# Install services
sudo apt install mysql-server redis-server nginx -y

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y
```

### Database Setup
```bash
sudo mysql -u root -p

CREATE DATABASE nexus_production;
CREATE USER 'nexus_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD';
GRANT ALL PRIVILEGES ON nexus_production.* TO 'nexus_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Application Deployment
```bash
cd /var/www/nexus

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install && npm run build

# Setup application
cp ENV_CONFIG_GUIDE.md .env
nano .env  # Edit values
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=AdminUserSeeder
php artisan storage:link

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
sudo chown -R www-data:www-data /var/www/nexus
sudo chmod -R 755 /var/www/nexus
sudo chmod -R 775 storage bootstrap/cache
```

### SSL Certificate
```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com
```

---

## 🔧 CRITICAL .ENV SETTINGS

```env
# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Database (MySQL required)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=nexus_production
DB_USERNAME=nexus_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# Cache & Session (Redis required)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Email (Required)
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=YOUR_SENDGRID_API_KEY
MAIL_FROM_ADDRESS="noreply@yourdomain.com"

# Payment (Required)
RAZORPAY_KEY=rzp_live_XXXXXXXX
RAZORPAY_SECRET=XXXXXXXXXXXXXXXX

# Security
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
LOG_LEVEL=error
```

---

## 🧪 TESTING CHECKLIST

```
User Flow Tests:
□ User registration works
□ User login/logout works
□ Password reset works
□ Product browsing works
□ Add to cart works
□ Checkout completes
□ Payment processes
□ Order email received
□ Admin login works
□ Dealer approval works

Security Tests:
□ HTTPS enforced
□ Rate limiting works
□ File upload restricted
□ SQL injection prevented
□ XSS protection active
□ CSRF tokens present
□ Error pages show (404, 500)

Performance Tests:
□ Page load < 2 seconds
□ Mobile responsive
□ Images optimized
□ Caching active
□ No N+1 queries
```

---

## 📞 EMERGENCY TROUBLESHOOTING

### Site Not Loading
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

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/nexus
sudo chmod -R 755 /var/www/nexus
sudo chmod -R 775 storage bootstrap/cache
```

### Database Connection Failed
```bash
# Test connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check MySQL is running
sudo systemctl status mysql
sudo systemctl restart mysql
```

### SSL Issues
```bash
# Check certificate
sudo certbot certificates

# Renew certificate
sudo certbot renew

# Test SSL
curl -I https://yourdomain.com
```

---

## 📊 PERFORMANCE BENCHMARKS

Target Metrics:
- Page Load: < 2 seconds
- TTFB: < 200ms
- Uptime: > 99.9%
- SSL Rating: A+
- Mobile Score: > 80

---

## 🔗 DOCUMENTATION LINKS

**Start Here:**
- PRODUCTION_READINESS_SUMMARY.md

**Critical Reading:**
- PRODUCTION_READINESS_REPORT.md
- SECURITY_HARDENING_GUIDE.md
- ENV_CONFIG_GUIDE.md
- DEPLOYMENT_CHECKLIST.md

---

## 📝 GO-LIVE CHECKLIST

**Pre-Launch (Day -1):**
```
□ All critical items complete
□ Staging environment tested
□ Backup system tested
□ Rollback plan documented
□ Team briefed
□ Support prepared
```

**Launch Day:**
```
□ DNS updated
□ SSL active
□ Email tested
□ Payment tested
□ Monitoring active
□ Go live!
```

**Post-Launch (Day +1):**
```
□ Monitor errors
□ Check logs
□ Verify backups
□ Test functionality
□ Review performance
□ Collect feedback
```

---

## 🚨 SECURITY REMINDERS

**Never Commit:**
- .env file
- API keys
- Database passwords
- SSL certificates
- Private keys

**Always Check:**
- APP_DEBUG=false
- APP_ENV=production
- HTTPS enforced
- Rate limiting active
- File permissions correct
- Backups running

---

## 💰 MONTHLY COSTS ESTIMATE

```
VPS Hosting:        $15-50
Email Service:      $0-20
Backup Storage:     $5-10
SSL Certificate:    Free
Domain:             $1-2
CDN (optional):     $0-10
------------------------
Total:              $21-92/month
+ Payment fees (2%)
```

---

## ⏱️ DEPLOYMENT TIMELINE

```
Day 1-2: Planning & server setup
Day 3-4: Implementation & configuration  
Day 5-6: Testing & optimization
Day 7:   Launch!
```

---

## 📱 SUPPORT CONTACTS

**Documentation:** See PRODUCTION_READINESS_SUMMARY.md
**Security:** See SECURITY_HARDENING_GUIDE.md
**Deployment:** See DEPLOYMENT_CHECKLIST.md
**Configuration:** See ENV_CONFIG_GUIDE.md

---

## ✅ SUCCESS CRITERIA

Launch is successful when:
- ✅ Site loads over HTTPS
- ✅ Users can register/login
- ✅ Products display correctly
- ✅ Checkout works end-to-end
- ✅ Payments process successfully
- ✅ Emails deliver
- ✅ Admin dashboard accessible
- ✅ No critical errors in logs
- ✅ Performance metrics met
- ✅ Security scans pass

---

**🎯 Current Status: 75% Ready**
**🎯 With Critical Fixes: 95% Ready**
**🎯 With All Fixes: 100% Production-Ready**

---

**Last Updated:** October 29, 2025
**Version:** 1.0.0

**📋 Print this card and check off items as you complete them!**

---

*For detailed instructions, refer to the full documentation suite.*

















