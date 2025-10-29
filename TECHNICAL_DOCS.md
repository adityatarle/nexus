# Agriculture Equipment E-commerce System - Technical Documentation

## System Architecture

### MVC Pattern Implementation
The system follows Laravel's MVC (Model-View-Controller) architecture:

- **Models**: Handle data logic and database interactions
- **Views**: Present data to users (Blade templates)
- **Controllers**: Process user requests and coordinate between models and views

### Database Design

#### Entity Relationship Diagram
```
AgricultureCategory (1) -----> (N) AgricultureProduct
AgricultureProduct (1) -----> (N) AgricultureOrderItem
AgricultureOrder (1) -----> (N) AgricultureOrderItem
User (1) -----> (N) AgricultureOrder
```

#### Table Specifications

**agriculture_categories**
- Primary Key: id
- Unique Constraints: slug
- Indexes: is_active, sort_order

**agriculture_products**
- Primary Key: id
- Unique Constraints: slug, sku
- Foreign Keys: agriculture_category_id
- Indexes: is_active, is_featured, in_stock, brand, power_source

**agriculture_orders**
- Primary Key: id
- Unique Constraints: order_number
- Foreign Keys: user_id
- Indexes: order_status, payment_status, created_at

**agriculture_order_items**
- Primary Key: id
- Foreign Keys: agriculture_order_id, agriculture_product_id
- Indexes: agriculture_order_id, agriculture_product_id

## API Documentation

### Product API

#### GET /products
Retrieve paginated list of products with filtering options.

**Query Parameters:**
- `search` (string): Search term for product name, description, brand, or model
- `category` (integer): Filter by category ID
- `brand` (string): Filter by brand name
- `power_source` (string): Filter by power source
- `min_price` (decimal): Minimum price filter
- `max_price` (decimal): Maximum price filter
- `sort` (string): Sort by 'name', 'price_low', 'price_high', 'newest'
- `page` (integer): Page number for pagination

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "name": "John Deere Tractor",
      "slug": "john-deere-tractor",
      "price": "25000.00",
      "sale_price": "22000.00",
      "sku": "JD-TR-001",
      "stock_quantity": 5,
      "brand": "John Deere",
      "model": "6R Series",
      "power_source": "Diesel",
      "category": {
        "id": 1,
        "name": "Tractors"
      }
    }
  ],
  "links": {...},
  "meta": {...}
}
```

#### GET /products/{product}
Retrieve detailed information about a specific product.

**Response:**
```json
{
  "id": 1,
  "name": "John Deere Tractor",
  "description": "High-performance tractor for modern farming...",
  "price": "25000.00",
  "sale_price": "22000.00",
  "sku": "JD-TR-001",
  "stock_quantity": 5,
  "in_stock": true,
  "is_featured": true,
  "brand": "John Deere",
  "model": "6R Series",
  "power_source": "Diesel",
  "warranty": "2 Years",
  "weight": "4500.00",
  "dimensions": "4.5x2.1x2.8",
  "category": {...},
  "related_products": [...]
}
```

### Cart API

#### POST /cart/add
Add a product to the shopping cart.

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 2
}
```

**Response:**
```json
{
  "success": true,
  "message": "Product added to cart successfully!",
  "cart_count": 3
}
```

#### PATCH /cart/update
Update quantity of a product in the cart.

**Request Body:**
```json
{
  "product_id": 1,
  "quantity": 3
}
```

#### DELETE /cart/remove
Remove a product from the cart.

**Request Body:**
```json
{
  "product_id": 1
}
```

### Order API

#### POST /checkout/process
Process the checkout and create an order.

**Request Body:**
```json
{
  "customer_name": "John Doe",
  "customer_email": "john@example.com",
  "customer_phone": "+1234567890",
  "billing_address": "123 Farm Road, Agriculture City",
  "shipping_address": "123 Farm Road, Agriculture City",
  "payment_method": "credit_card",
  "notes": "Please deliver during business hours"
}
```

**Response:**
```json
{
  "success": true,
  "order_number": "AGR-ABC12345",
  "redirect_url": "/checkout/success/AGR-ABC12345"
}
```

## Security Implementation

### Input Validation
All user inputs are validated using Laravel's validation rules:

```php
$request->validate([
    'name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'price' => 'required|numeric|min:0',
    'quantity' => 'required|integer|min:1'
]);
```

### CSRF Protection
All forms include CSRF tokens:
```php
@csrf
```

### SQL Injection Prevention
Using Eloquent ORM and parameterized queries prevents SQL injection attacks.

### XSS Protection
Blade templates automatically escape output:
```php
{{ $user_input }} // Automatically escaped
{!! $trusted_html !!} // Raw output (use carefully)
```

## Performance Optimization

### Database Optimization

#### Indexing Strategy
```sql
-- Product search optimization
CREATE INDEX idx_products_search ON agriculture_products(name, description, brand, model);

-- Category filtering
CREATE INDEX idx_products_category ON agriculture_products(agriculture_category_id);

-- Stock management
CREATE INDEX idx_products_stock ON agriculture_products(in_stock, stock_quantity);

-- Order processing
CREATE INDEX idx_orders_status ON agriculture_orders(order_status, payment_status);
CREATE INDEX idx_orders_date ON agriculture_orders(created_at);
```

#### Query Optimization
- Use eager loading to prevent N+1 queries:
```php
$products = AgricultureProduct::with('category')->get();
```

- Implement pagination for large datasets:
```php
$products = AgricultureProduct::paginate(12);
```

### Caching Strategy
```php
// Cache expensive queries
$categories = Cache::remember('categories', 3600, function () {
    return AgricultureCategory::active()->ordered()->get();
});

// Cache product counts
$productCount = Cache::remember('product_count', 1800, function () {
    return AgricultureProduct::active()->count();
});
```

### Asset Optimization
- Minify CSS and JavaScript files
- Compress images
- Use CDN for static assets
- Implement browser caching

## Error Handling

### Custom Error Pages
- 404: Product not found
- 500: Server error
- 403: Access denied

### Logging
```php
// Log important events
Log::info('Order created', ['order_number' => $order->order_number]);
Log::warning('Low stock alert', ['product_id' => $product->id]);
Log::error('Payment failed', ['order_id' => $order->id]);
```

### Exception Handling
```php
try {
    $order = AgricultureOrder::create($orderData);
} catch (Exception $e) {
    Log::error('Order creation failed', ['error' => $e->getMessage()]);
    return redirect()->back()->with('error', 'Order creation failed. Please try again.');
}
```

## Testing Strategy

### Unit Tests
```php
// Product model tests
public function test_product_can_calculate_discount()
{
    $product = AgricultureProduct::factory()->create([
        'price' => 100,
        'sale_price' => 80
    ]);
    
    $this->assertEquals(20, $product->discount_percentage);
}
```

### Feature Tests
```php
// Cart functionality tests
public function test_user_can_add_product_to_cart()
{
    $product = AgricultureProduct::factory()->create();
    
    $response = $this->post('/cart/add', [
        'product_id' => $product->id,
        'quantity' => 2
    ]);
    
    $response->assertRedirect();
    $this->assertDatabaseHas('cart_items', [
        'product_id' => $product->id,
        'quantity' => 2
    ]);
}
```

### Integration Tests
- Test complete checkout flow
- Test admin product management
- Test order processing workflow

## Deployment Guide

### Production Environment Setup

#### Server Requirements
- PHP 8.2+
- MySQL 8.0+ or PostgreSQL 13+
- Nginx 1.18+ or Apache 2.4+
- Redis 6.0+ (for caching)
- SSL Certificate

#### Environment Configuration
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=agriculture_store
DB_USERNAME=your_username
DB_PASSWORD=your_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

#### Deployment Steps
1. **Server Setup**
   ```bash
   # Install dependencies
   sudo apt update
   sudo apt install nginx mysql-server php8.2-fpm php8.2-mysql
   
   # Install Composer
   curl -sS https://getcomposer.org/installer | php
   sudo mv composer.phar /usr/local/bin/composer
   ```

2. **Application Deployment**
   ```bash
   # Clone repository
   git clone <repository-url> /var/www/agriculture-store
   cd /var/www/agriculture-store
   
   # Install dependencies
   composer install --no-dev --optimize-autoloader
   npm install && npm run production
   
   # Configure environment
   cp .env.example .env
   php artisan key:generate
   
   # Setup database
   php artisan migrate --force
   php artisan db:seed --force
   
   # Set permissions
   sudo chown -R www-data:www-data /var/www/agriculture-store
   sudo chmod -R 755 /var/www/agriculture-store
   sudo chmod -R 775 storage bootstrap/cache
   ```

3. **Nginx Configuration**
   ```nginx
   server {
       listen 80;
       listen 443 ssl;
       server_name yourdomain.com;
       root /var/www/agriculture-store/public;
       
       index index.php;
       
       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }
       
       location ~ \.php$ {
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
           fastcgi_index index.php;
           fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
           include fastcgi_params;
       }
       
       location ~ /\.ht {
           deny all;
       }
   }
   ```

4. **SSL Setup**
   ```bash
   # Using Let's Encrypt
   sudo apt install certbot python3-certbot-nginx
   sudo certbot --nginx -d yourdomain.com
   ```

### Monitoring and Maintenance

#### Log Monitoring
```bash
# Monitor application logs
tail -f storage/logs/laravel.log

# Monitor Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log
```

#### Database Maintenance
```bash
# Regular backups
mysqldump -u username -p agriculture_store > backup_$(date +%Y%m%d).sql

# Optimize database
mysql -u username -p -e "OPTIMIZE TABLE agriculture_products, agriculture_orders;"
```

#### Performance Monitoring
- Use Laravel Telescope for debugging
- Monitor server resources (CPU, memory, disk)
- Set up uptime monitoring
- Track application performance metrics

## Troubleshooting Guide

### Common Issues and Solutions

#### 1. Database Connection Issues
**Problem**: Database connection failed
**Solution**: 
- Check database credentials in `.env`
- Ensure database server is running
- Verify database exists and user has permissions

#### 2. Permission Errors
**Problem**: Permission denied errors
**Solution**:
```bash
sudo chown -R www-data:www-data /var/www/agriculture-store
sudo chmod -R 755 /var/www/agriculture-store
sudo chmod -R 775 storage bootstrap/cache
```

#### 3. Asset Loading Issues
**Problem**: CSS/JS files not loading
**Solution**:
```bash
npm run production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### 4. Session Issues
**Problem**: Sessions not persisting
**Solution**:
- Check session driver configuration
- Ensure storage directory is writable
- Clear session cache: `php artisan session:table`

#### 5. Email Issues
**Problem**: Emails not sending
**Solution**:
- Configure SMTP settings in `.env`
- Test email configuration: `php artisan tinker`
- Check mail logs

### Debug Mode
Enable debug mode for development:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Performance Issues
1. **Slow Queries**: Use Laravel Debugbar to identify slow queries
2. **Memory Issues**: Increase PHP memory limit
3. **Cache Issues**: Clear application cache
4. **Database Issues**: Optimize database indexes

---

This technical documentation provides comprehensive information about the Agriculture Equipment E-commerce System's architecture, implementation, and maintenance procedures.

