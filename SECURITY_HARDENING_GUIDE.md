# 🔐 Security Hardening Guide
## Nexus Agriculture eCommerce Platform

**Critical Security Improvements for Production**

---

## 📋 Security Assessment Summary

**Current Security Level:** ⚠️ Moderate  
**Target Security Level:** ✅ High  
**Critical Vulnerabilities:** 7 items requiring immediate attention

---

## 🔴 CRITICAL SECURITY FIXES (Implement Before Launch)

### 1. Security Headers Middleware

**Issue:** Missing critical security headers make the application vulnerable to XSS, clickjacking, and other attacks.

**Solution:** Create security headers middleware.

**Create file:** `app/Http/Middleware/SecurityHeaders.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS protection in older browsers
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control referrer information
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Disable dangerous features
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=()');

        // HSTS for HTTPS enforcement (only in production)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://checkout.razorpay.com",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com",
            "img-src 'self' data: https: blob:",
            "connect-src 'self' https://api.razorpay.com",
            "frame-src 'self' https://api.razorpay.com",
        ]);
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
```

**Register middleware in** `bootstrap/app.php`:

```php
use App\Http\Middleware\SecurityHeaders;

->withMiddleware(function (Middleware $middleware) {
    $middleware->append(SecurityHeaders::class);
})
```

---

### 2. Rate Limiting & Brute Force Protection

**Issue:** No rate limiting allows brute force attacks on login and registration.

**Solution:** Implement comprehensive rate limiting.

**Update** `routes/web.php`:

```php
// Login rate limiting (5 attempts per minute)
Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login');

// Registration rate limiting (3 attempts per hour)
Route::post('/register', [AuthController::class, 'register'])
    ->middleware('throttle:3,60')
    ->name('register');

// Password reset rate limiting (3 attempts per hour)
Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('throttle:3,60')
    ->name('password.email');

// Admin login extra protection (3 attempts per 5 minutes)
Route::post('/admin/login', [AdminAuthController::class, 'login'])
    ->middleware('throttle:3,5')
    ->name('admin.login');
```

**Update** `app/Http/Kernel.php` or create custom throttle:

```php
// In bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->throttleApi();
    
    // Custom throttle groups
    $middleware->throttleWithRedis();
})
```

**Create custom failed login tracking:**

**Create file:** `app/Http/Middleware/TrackFailedLogins.php`

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrackFailedLogins
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_TIME = 15; // minutes
    private const WARNING_THRESHOLD = 3;

    public function handle(Request $request, Closure $next)
    {
        $key = $this->getThrottleKey($request);
        $attempts = Cache::get($key, 0);

        // Check if locked out
        if ($attempts >= self::MAX_ATTEMPTS) {
            Log::warning('Multiple failed login attempts', [
                'ip' => $request->ip(),
                'email' => $request->input('email'),
                'attempts' => $attempts,
            ]);

            return response()->json([
                'error' => 'Too many login attempts. Please try again in ' . self::LOCKOUT_TIME . ' minutes.'
            ], 429);
        }

        $response = $next($request);

        // Track failed attempts
        if ($response->status() === 401) {
            $newAttempts = $attempts + 1;
            Cache::put($key, $newAttempts, now()->addMinutes(self::LOCKOUT_TIME));

            // Send alert after threshold
            if ($newAttempts >= self::WARNING_THRESHOLD) {
                Log::warning('Potential brute force attack', [
                    'ip' => $request->ip(),
                    'email' => $request->input('email'),
                    'attempts' => $newAttempts,
                ]);
            }
        }

        // Clear attempts on successful login
        if ($response->status() === 200) {
            Cache::forget($key);
        }

        return $response;
    }

    private function getThrottleKey(Request $request): string
    {
        return 'login_attempts:' . $request->ip() . ':' . $request->input('email');
    }
}
```

---

### 3. Input Validation & Sanitization

**Issue:** Insufficient input validation can lead to SQL injection, XSS, and data corruption.

**Solution:** Strengthen validation rules across the application.

**Create** `app/Http/Requests/ProductStoreRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'slug' => ['required', 'string', 'max:255', 'unique:agriculture_products,slug', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
            'description' => ['required', 'string', 'min:50', 'max:5000'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'lt:price'],
            'dealer_price' => ['required', 'numeric', 'min:0', 'max:99999999.99', 'lte:price'],
            'dealer_sale_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99', 'lt:dealer_price'],
            'sku' => ['required', 'string', 'max:100', 'unique:agriculture_products,sku', 'regex:/^[A-Z0-9-]+$/'],
            'stock_quantity' => ['required', 'integer', 'min:0', 'max:999999'],
            'agriculture_category_id' => ['required', 'exists:agriculture_categories,id'],
            'brand' => ['nullable', 'string', 'max:100'],
            'model' => ['nullable', 'string', 'max:100'],
            'weight' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'dimensions' => ['nullable', 'string', 'max:100'],
            'warranty' => ['nullable', 'string', 'max:100'],
            'is_featured' => ['boolean'],
            'is_active' => ['boolean'],
            'primary_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048', 'dimensions:min_width=400,min_height=400'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required',
            'name.min' => 'Product name must be at least 3 characters',
            'slug.regex' => 'Slug must contain only lowercase letters, numbers, and hyphens',
            'sku.regex' => 'SKU must contain only uppercase letters, numbers, and hyphens',
            'price.min' => 'Price must be greater than 0',
            'sale_price.lt' => 'Sale price must be less than regular price',
            'dealer_price.lte' => 'Dealer price cannot exceed regular price',
            'primary_image.max' => 'Image size must not exceed 2MB',
            'primary_image.dimensions' => 'Image must be at least 400x400 pixels',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Sanitize inputs
        if ($this->has('name')) {
            $this->merge([
                'name' => strip_tags($this->name),
            ]);
        }

        if ($this->has('description')) {
            $this->merge([
                'description' => strip_tags($this->description, '<p><br><strong><em><ul><ol><li>'),
            ]);
        }
    }
}
```

**Create** `app/Http/Requests/CheckoutRequest.php`:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'customer_email' => ['required', 'email:rfc,dns', 'max:255'],
            'customer_phone' => ['required', 'regex:/^[6-9]\d{9}$/', 'size:10'],
            'billing_address' => ['required', 'string', 'min:10', 'max:500'],
            'shipping_address' => ['required', 'string', 'min:10', 'max:500'],
            'city' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s]+$/'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/', 'size:6'],
            'payment_method' => ['required', 'in:cod,online,bank_transfer'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms_accepted' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_name.regex' => 'Name must contain only letters and spaces',
            'customer_phone.regex' => 'Please enter a valid 10-digit Indian mobile number',
            'customer_email.email' => 'Please enter a valid email address',
            'pincode.regex' => 'Please enter a valid 6-digit Indian pincode',
            'terms_accepted.accepted' => 'You must accept the terms and conditions',
        ];
    }
}
```

---

### 4. File Upload Security

**Issue:** File uploads without proper validation can lead to code execution vulnerabilities.

**Solution:** Implement comprehensive file upload security.

**Create** `app/Services/FileUploadService.php`:

```php
<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class FileUploadService
{
    private const ALLOWED_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    private const MAX_FILE_SIZE = 2048 * 1024; // 2MB in bytes
    private const MIN_IMAGE_WIDTH = 400;
    private const MIN_IMAGE_HEIGHT = 400;

    public function uploadProductImage(UploadedFile $file, string $type = 'product'): string
    {
        // Validate file
        $this->validateFile($file);

        // Generate secure filename
        $filename = $this->generateSecureFilename($file);

        // Process and optimize image
        $this->processImage($file, $filename);

        // Store file
        $path = "products/{$type}/" . $filename;
        Storage::disk('public')->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    private function validateFile(UploadedFile $file): void
    {
        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new \Exception('File size exceeds maximum allowed size of 2MB');
        }

        // Check MIME type
        if (!in_array($file->getMimeType(), self::ALLOWED_MIME_TYPES)) {
            throw new \Exception('Invalid file type. Only JPEG, PNG, and WebP images are allowed');
        }

        // Check file extension matches MIME type
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        
        $validExtensions = [
            'image/jpeg' => ['jpg', 'jpeg'],
            'image/png' => ['png'],
            'image/webp' => ['webp'],
        ];

        if (!isset($validExtensions[$mimeType]) || !in_array($extension, $validExtensions[$mimeType])) {
            throw new \Exception('File extension does not match file type');
        }

        // Verify it's actually an image
        try {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo === false) {
                throw new \Exception('File is not a valid image');
            }

            // Check minimum dimensions
            if ($imageInfo[0] < self::MIN_IMAGE_WIDTH || $imageInfo[1] < self::MIN_IMAGE_HEIGHT) {
                throw new \Exception('Image dimensions must be at least ' . self::MIN_IMAGE_WIDTH . 'x' . self::MIN_IMAGE_HEIGHT . ' pixels');
            }
        } catch (\Exception $e) {
            throw new \Exception('Invalid image file');
        }
    }

    private function generateSecureFilename(UploadedFile $file): string
    {
        // Get original filename without extension
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitize filename (remove special characters)
        $safeName = Str::slug($originalName);
        
        // Generate unique identifier
        $uniqueId = Str::random(16);
        
        // Get extension
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Combine into secure filename
        return $safeName . '-' . time() . '-' . $uniqueId . '.' . $extension;
    }

    private function processImage(UploadedFile $file, string $filename): void
    {
        // Additional image processing can be added here
        // - Resize to standard dimensions
        // - Strip EXIF data
        // - Optimize for web
        // - Create thumbnails
    }

    public function deleteFile(string $path): bool
    {
        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->delete($path);
        }
        return false;
    }
}
```

**Update product controller to use the service:**

```php
use App\Services\FileUploadService;

class ProductController extends Controller
{
    protected $fileUploadService;

    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    public function store(ProductStoreRequest $request)
    {
        try {
            // Upload primary image
            if ($request->hasFile('primary_image')) {
                $imagePath = $this->fileUploadService->uploadProductImage(
                    $request->file('primary_image'),
                    'primary'
                );
            }

            // Upload gallery images
            $galleryPaths = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $image) {
                    $galleryPaths[] = $this->fileUploadService->uploadProductImage(
                        $image,
                        'gallery'
                    );
                }
            }

            // Create product...
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

---

### 5. SQL Injection Prevention

**Issue:** While Eloquent provides protection, raw queries need extra caution.

**Solution:** Always use parameter binding and never trust user input.

**Bad Example (Vulnerable):**
```php
// NEVER DO THIS!
$results = DB::select("SELECT * FROM users WHERE email = '" . $request->email . "'");
```

**Good Example (Safe):**
```php
// Use parameter binding
$results = DB::select('SELECT * FROM users WHERE email = ?', [$request->email]);

// Or use Eloquent (recommended)
$user = User::where('email', $request->email)->first();

// For complex queries, still use bindings
$orders = DB::table('agriculture_orders')
    ->whereIn('status', $statuses)
    ->where('user_id', $userId)
    ->where('created_at', '>=', $startDate)
    ->get();
```

**Create database security helper:**

```php
<?php

namespace App\Helpers;

class DatabaseSecurity
{
    public static function sanitizeForLike(string $value): string
    {
        // Escape special characters in LIKE queries
        return str_replace(['%', '_'], ['\%', '\_'], $value);
    }

    public static function sanitizeOrderBy(string $column, array $allowed): string
    {
        // Only allow whitelisted column names for ORDER BY
        if (!in_array($column, $allowed)) {
            return $allowed[0]; // Return default
        }
        return $column;
    }

    public static function sanitizeDirection(string $direction): string
    {
        // Only allow ASC or DESC
        return strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';
    }
}
```

**Usage:**
```php
use App\Helpers\DatabaseSecurity;

// Safe search query
$searchTerm = DatabaseSecurity::sanitizeForLike($request->search);
$products = AgricultureProduct::where('name', 'LIKE', "%{$searchTerm}%")->get();

// Safe sorting
$allowedColumns = ['name', 'price', 'created_at'];
$sortColumn = DatabaseSecurity::sanitizeOrderBy($request->sort, $allowedColumns);
$sortDirection = DatabaseSecurity::sanitizeDirection($request->direction);
$products = AgricultureProduct::orderBy($sortColumn, $sortDirection)->get();
```

---

### 6. XSS (Cross-Site Scripting) Prevention

**Issue:** User-generated content can inject malicious scripts.

**Solution:** Always escape output and sanitize inputs.

**In Blade templates:**
```blade
{{-- Safe (automatically escaped) --}}
<h1>{{ $product->name }}</h1>
<p>{{ $user->bio }}</p>

{{-- Raw HTML (use only for trusted content) --}}
{!! $trustedHtmlContent !!}

{{-- Safe with additional escaping --}}
<script>
    // Use JSON encoding for JavaScript
    const productData = @json($product);
    const userName = {{ Js::from($user->name) }};
</script>
```

**Create content sanitizer:**

```php
<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class ContentSanitizer
{
    private $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,strong,em,u,ul,ol,li,a[href],h1,h2,h3,h4,h5,h6');
        $config->set('CSS.AllowedProperties', []);
        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitizeHtml(string $content): string
    {
        return $this->purifier->purify($content);
    }

    public function sanitizeText(string $content): string
    {
        return strip_tags($content);
    }

    public function sanitizeForAttribute(string $content): string
    {
        return htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
    }
}
```

---

### 7. CSRF Protection

**Issue:** CSRF attacks can perform unauthorized actions on behalf of authenticated users.

**Solution:** Ensure all forms include CSRF tokens (Laravel does this by default).

**In forms:**
```blade
<form method="POST" action="{{ route('checkout') }}">
    @csrf
    {{-- Form fields --}}
</form>

{{-- For AJAX requests --}}
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
</script>
```

**Verify CSRF middleware is active in** `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->validateCsrfTokens(except: [
        // Add any routes that should be exempt (webhooks, etc.)
        'webhook/*',
    ]);
})
```

---

## ⚠️ ADDITIONAL SECURITY MEASURES

### 8. Password Security

**Enforce strong password policy:**

**Create** `app/Rules/StrongPassword.php`:

```php
<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class StrongPassword implements Rule
{
    public function passes($attribute, $value)
    {
        // Minimum 8 characters, at least one uppercase, one lowercase, one number, one special character
        return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $value);
    }

    public function message()
    {
        return 'The :attribute must be at least 8 characters and contain at least one uppercase letter, one lowercase letter, one number, and one special character.';
    }
}
```

**Usage in registration:**
```php
use App\Rules\StrongPassword;

$request->validate([
    'password' => ['required', new StrongPassword(), 'confirmed'],
]);
```

---

### 9. Session Security

**Update** `.env` for production:
```env
SESSION_DRIVER=redis
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=strict
```

**Regenerate session ID after login:**
```php
public function login(Request $request)
{
    $credentials = $request->only('email', 'password');
    
    if (Auth::attempt($credentials)) {
        // Regenerate session to prevent fixation attacks
        $request->session()->regenerate();
        
        return redirect()->intended('/dashboard');
    }
    
    return back()->withErrors(['email' => 'Invalid credentials']);
}
```

---

### 10. API Security

**If using APIs, implement API key authentication:**

**Create** `app/Http/Middleware/ValidateApiKey.php`:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateApiKey
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-API-Key');
        
        if (!$apiKey || !$this->isValidApiKey($apiKey)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        return $next($request);
    }
    
    private function isValidApiKey($key): bool
    {
        // Check against stored API keys
        return $key === config('app.api_key');
    }
}
```

---

## 📝 SECURITY CHECKLIST

### Pre-Launch Security Audit

- [ ] All security headers implemented
- [ ] Rate limiting on authentication routes
- [ ] Input validation on all forms
- [ ] File upload restrictions enforced
- [ ] SQL injection prevention verified
- [ ] XSS protection on all outputs
- [ ] CSRF tokens on all forms
- [ ] Strong password policy enforced
- [ ] Session security configured
- [ ] HTTPS enforced in production
- [ ] Sensitive data encrypted
- [ ] Error messages don't reveal system info
- [ ] Admin routes protected
- [ ] API endpoints secured
- [ ] File permissions correct (644/755)
- [ ] .env file not accessible via web
- [ ] Database credentials secure
- [ ] Third-party dependencies updated
- [ ] Security logs configured
- [ ] Backup encryption enabled

### Regular Security Maintenance

- [ ] Update Laravel and packages monthly
- [ ] Review access logs weekly
- [ ] Check for security patches
- [ ] Audit user permissions
- [ ] Review failed login attempts
- [ ] Test backup restoration
- [ ] Renew SSL certificates
- [ ] Review firewall rules
- [ ] Check for unused routes
- [ ] Audit database queries

---

## 🚨 INCIDENT RESPONSE PLAN

### If Security Breach Detected

1. **Immediate Actions:**
   - Take affected systems offline
   - Change all passwords and API keys
   - Revoke active sessions
   - Enable maintenance mode

2. **Investigation:**
   - Review access logs
   - Identify entry point
   - Assess damage scope
   - Document findings

3. **Recovery:**
   - Patch vulnerabilities
   - Restore from clean backup
   - Reset all credentials
   - Notify affected users

4. **Prevention:**
   - Implement additional security measures
   - Update security documentation
   - Train team on lessons learned
   - Schedule security audit

---

## 📚 SECURITY RESOURCES

### Tools
- **OWASP ZAP** - Security scanner
- **SQLMap** - SQL injection testing
- **Burp Suite** - Web security testing
- **Laravel Telescope** - Application insights

### References
- [OWASP Top 10](https://owasp.org/www-project-top-ten/)
- [Laravel Security Best Practices](https://laravel.com/docs/security)
- [PHP Security Guide](https://phptherightway.com/#security)
- [Web Security Academy](https://portswigger.net/web-security)

---

**Last Updated:** October 29, 2025  
**Version:** 1.0.0  
**Security Level:** Target - High

⚠️ **Security is an ongoing process. Regular audits and updates are essential.**

















