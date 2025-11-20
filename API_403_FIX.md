# 🔴 API 403 Forbidden Error - Fix Guide

## Problem
Getting **403 Forbidden** HTML error when accessing API endpoints:
- `GET /api/v1/products` → 403 Forbidden
- `GET /api/v1/products/17` → 403 Forbidden

**Error Response:** HTML page saying "Access to this resource on the server is denied!"

## Root Cause
The 403 HTML error indicates a **server-level block** (Apache/Nginx), not a Laravel application issue. This typically happens when:
1. Web server security rules block `/api/` paths
2. Hosting provider has security restrictions
3. ModSecurity or similar security module is blocking requests
4. Parent directory `.htaccess` is blocking access
5. Directory permissions are incorrect

## ✅ Solutions

### Solution 1: Update .htaccess (Already Applied)
The `public/.htaccess` file has been updated to explicitly allow API routes.

### Solution 2: Check Server Configuration (REQUIRED)

**For Apache Servers:**
1. Check if there's a parent `.htaccess` file blocking `/api/`
2. Verify `mod_rewrite` is enabled
3. Check Apache error logs: `/var/log/apache2/error.log` or cPanel error logs

**For Nginx Servers:**
Check nginx configuration for blocks on `/api/` paths.

### Solution 3: Contact Hosting Provider

Since this is a **server-level block**, you may need to contact your hosting provider (Hostinger) to:

1. **Whitelist `/api/` paths** in server security rules
2. **Disable ModSecurity rules** blocking API endpoints (if applicable)
3. **Check firewall rules** that might be blocking API requests
4. **Verify directory permissions** for the API routes

**Information to provide to hosting support:**
```
Issue: API endpoints returning 403 Forbidden HTML error
Domain: nexus.heuristictechpark.com
Affected Endpoints: /api/v1/products, /api/v1/products/{id}
Error: HTML 403 page (not Laravel JSON error)
Request: GET https://nexus.heuristictechpark.com/api/v1/products

What we need:
1. Check if ModSecurity or security modules are blocking /api/ paths
2. Verify .htaccess is being processed correctly
3. Check if there are server-level security rules blocking API routes
4. Ensure mod_rewrite is enabled
5. Check Apache/Nginx error logs for specific blocking rules
```

### Solution 4: Temporary Workaround (If Server Config Can't Be Changed)

If you cannot modify server configuration, you could:
1. Use a different API prefix (e.g., `/mobile-api/v1/products`)
2. Use a subdomain for API (e.g., `api.nexus.heuristictechpark.com`)

## 🔍 Diagnostic Steps

### Step 1: Test if Laravel is Reaching the Request
Add logging to see if requests reach Laravel:

```php
// In routes/api.php, add at the top:
\Log::info('API Request', [
    'uri' => request()->getRequestUri(),
    'method' => request()->method(),
    'headers' => request()->headers->all()
]);
```

If you don't see logs, the request is being blocked before reaching Laravel.

### Step 2: Test Direct File Access
Try accessing a test file:
- Create: `public/api-test.php`
- Content: `<?php echo "API test works"; ?>`
- Access: `https://nexus.heuristictechpark.com/api-test.php`

If this also returns 403, it's definitely a server-level block.

### Step 3: Check Server Error Logs
```bash
# SSH into server
tail -f /var/log/apache2/error.log
# or
tail -f /var/log/nginx/error.log

# Then make an API request and watch for errors
```

## 📋 Files Updated

1. ✅ `public/.htaccess` - Added explicit API route allowance
2. ✅ `app/Http/Controllers/Api/ProductController.php` - Fixed route model binding

## 🧪 Testing

After fixes, test with:

```bash
# Test without authentication (should work)
curl -X GET https://nexus.heuristictechpark.com/api/v1/products

# Test with authentication
curl -X GET https://nexus.heuristictechpark.com/api/v1/products \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [...]
  }
}
```

## ⚠️ Important Notes

1. **The 403 HTML error is NOT from Laravel** - Laravel would return JSON errors
2. **This is a server-level security block** - needs hosting provider intervention
3. **The .htaccess fix may not be enough** if server has higher-level security rules
4. **Check hosting control panel** for security settings or firewall rules

## 🚨 Immediate Action Required

**Contact your hosting provider (Hostinger) support** with the information above. The server is blocking API requests before they reach Laravel.



