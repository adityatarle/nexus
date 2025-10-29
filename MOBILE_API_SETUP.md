# Mobile API Setup Guide

## Overview
This Laravel application has been configured with RESTful API endpoints for mobile application development using Flutter.

## What's Been Added

### 1. Laravel Sanctum Installation
- API token authentication
- Secure token-based authentication for mobile apps

### 2. API Routes (`routes/api.php`)
- All endpoints prefixed with `/api/v1/`
- Public routes (no authentication)
- Protected routes (require authentication token)

### 3. API Controllers
Located in `app/Http/Controllers/Api/`:
- **AuthController** - Registration, login, logout
- **ProductController** - Product listing, search, details
- **CategoryController** - Category listing and details
- **CartController** - Shopping cart operations
- **OrderController** - Order management
- **WishlistController** - Wishlist operations
- **ProfileController** - User profile and notifications

### 4. API Documentation
See `API_DOCUMENTATION.md` for complete endpoint documentation.

## Setup Instructions

### Local Development

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Run Migrations**
   ```bash
   php artisan migrate
   ```
   This will create the `personal_access_tokens` table for Sanctum.

3. **Test API Endpoints**
   - Base URL: `http://localhost:8000/api/v1`
   - Test with Postman or any API client
   - Example: `GET http://localhost:8000/api/v1/products`

### Production Deployment (Hostinger)

1. **Upload Files**
   - Upload all project files to your Hostinger server
   - Ensure `.env` file is properly configured

2. **Configure Environment**
   Update `.env` file:
   ```env
   APP_URL=https://yourdomain.com
   APP_ENV=production
   APP_DEBUG=false
   
   # Database configuration
   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Run Migrations**
   ```bash
   php artisan migrate --force
   ```

4. **Run Storage Link**
   ```bash
   php artisan storage:link
   ```
   This creates a symbolic link for public access to uploaded images.

5. **Set Permissions**
   ```bash
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

6. **Clear Cache**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

## API Base URL for Flutter App

**Production:**
```
https://yourdomain.com/api/v1
```

**Development:**
```
http://localhost:8000/api/v1
```

## Authentication Flow

1. **Register/Login** - User registers or logs in, receives a token
2. **Store Token** - Flutter app stores the token securely
3. **Include in Requests** - Add token to Authorization header:
   ```
   Authorization: Bearer {token}
   ```
4. **Logout** - Token is revoked when user logs out

## Important Notes

### Cart Management
- The cart currently uses session storage
- For mobile apps, consider implementing cart persistence in the database
- Each API request creates a new session, so cart may not persist between requests
- **Solution**: Store cart items in database tied to user_id for mobile apps

### Image URLs
- All images return full URLs (including domain)
- Ensure `APP_URL` in `.env` is set correctly
- Run `php artisan storage:link` to make storage files publicly accessible

### CORS Configuration
For mobile apps, you may need to configure CORS. Add to `config/cors.php`:
```php
'paths' => ['api/*'],
'allowed_origins' => ['*'],
'allowed_methods' => ['*'],
'allowed_headers' => ['*'],
```

### Rate Limiting
API endpoints have rate limiting enabled. Adjust in `bootstrap/app.php`:
```php
$middleware->throttleApi(60, 1); // 60 requests per minute
```

## Testing the API

### Using cURL

**Login:**
```bash
curl -X POST https://yourdomain.com/api/v1/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

**Get Products:**
```bash
curl -X GET https://yourdomain.com/api/v1/products \
  -H "Authorization: Bearer {your_token}" \
  -H "Content-Type: application/json"
```

### Using Postman

1. Import the collection from `API_DOCUMENTATION.md`
2. Set base URL: `https://yourdomain.com/api/v1`
3. For authenticated requests, add header:
   - Key: `Authorization`
   - Value: `Bearer {token}`

## Flutter Integration Steps

1. **Create API Service Class**
   ```dart
   class ApiService {
     final String baseUrl = 'https://yourdomain.com/api/v1';
     
     Future<Map<String, dynamic>> login(String email, String password) async {
       // Implementation
     }
   }
   ```

2. **Store Token Securely**
   Use `flutter_secure_storage` or `shared_preferences` package

3. **Handle Authentication**
   - Store token after login
   - Include in all authenticated requests
   - Handle token expiration/logout

4. **Error Handling**
   - Check `success` field in responses
   - Handle 401 (Unauthorized) by redirecting to login
   - Display error messages from `message` or `errors` fields

## Troubleshooting

### API Returns 404
- Check that routes are loaded: `php artisan route:list`
- Verify `bootstrap/app.php` includes API routes
- Clear route cache: `php artisan route:clear`

### Images Not Loading
- Run `php artisan storage:link`
- Check `APP_URL` in `.env`
- Verify file permissions on `storage/app/public`

### Authentication Not Working
- Verify Sanctum is installed: `composer show laravel/sanctum`
- Check User model has `HasApiTokens` trait
- Verify token is being sent in Authorization header

### CORS Errors
- Configure CORS in `config/cors.php`
- Set proper allowed origins
- Add CORS headers in middleware

## Next Steps

1. **Improve Cart for Mobile**
   - Move cart from session to database
   - Add `user_id` to cart items
   - Sync cart across devices

2. **Push Notifications**
   - Integrate FCM (Firebase Cloud Messaging)
   - Send order updates via push notifications

3. **Payment Gateway**
   - Integrate payment gateway (Stripe, PayPal, etc.)
   - Handle payment callbacks

4. **Offline Support**
   - Cache products locally in Flutter
   - Sync data when online

## Support

For questions or issues:
1. Check `API_DOCUMENTATION.md` for endpoint details
2. Review Laravel Sanctum documentation
3. Check application logs: `storage/logs/laravel.log`

---

**API is ready for Flutter development!** 🚀

