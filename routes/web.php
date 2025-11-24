<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\DealerRegistrationController;
use App\Http\Controllers\Admin\DealerManagementController;
use App\Http\Controllers\AgricultureProductController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\AgricultureCategoryController;
use App\Http\Controllers\AgricultureCartController;
use App\Http\Controllers\AgricultureCheckoutController;

// Authentication Routes with Rate Limiting
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('login');
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:3,60') // 3 registrations per hour
        ->name('register');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Specific login routes with strict rate limiting
    Route::get('/customer-login', [AuthController::class, 'showCustomerLogin'])->name('customer-login');
    Route::post('/customer-login', [AuthController::class, 'customerLogin'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('customer-login');
    
    Route::get('/dealer-login', [AuthController::class, 'showDealerLogin'])->name('dealer-login');
    Route::post('/dealer-login', [AuthController::class, 'dealerLogin'])
        ->middleware('throttle:5,1') // 5 attempts per minute
        ->name('dealer-login');
});

// Dealer Registration Routes with Rate Limiting
Route::prefix('dealer')->name('dealer.')->middleware('auth')->group(function () {
    Route::get('/registration', [DealerRegistrationController::class, 'showRegistration'])->name('registration');
    Route::post('/registration', [DealerRegistrationController::class, 'register'])
        ->middleware('throttle:3,60') // 3 registrations per hour
        ->name('register');
    Route::get('/pending', [DealerRegistrationController::class, 'showPending'])->name('pending');
    Route::get('/dashboard', [DealerRegistrationController::class, 'showDashboard'])->name('dashboard');
});

// Dealer Dashboard Routes (for approved dealers)
use App\Http\Controllers\DealerDashboardController;
Route::prefix('dealer')->name('dealer.')->middleware('auth')->group(function () {
    Route::get('/products', [DealerDashboardController::class, 'products'])->name('products');
    Route::get('/orders', [DealerDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [DealerDashboardController::class, 'orderShow'])->name('orders.show');
    Route::get('/invoice/{orderNumber}/download', [DealerDashboardController::class, 'downloadInvoice'])->name('invoice.download');
    Route::get('/profile', [DealerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DealerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/notifications', [DealerDashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/mark-read', [DealerDashboardController::class, 'markNotificationRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [DealerDashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
});

// Customer Dashboard Routes
use App\Http\Controllers\CustomerDashboardController;
Route::prefix('customer')->name('customer.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/orders', [CustomerDashboardController::class, 'orders'])->name('orders');
    Route::get('/orders/{orderNumber}', [CustomerDashboardController::class, 'orderShow'])->name('orders.show');
    Route::get('/profile', [CustomerDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [CustomerDashboardController::class, 'updatePassword'])->name('password.update');
    Route::get('/notifications', [CustomerDashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/mark-read', [CustomerDashboardController::class, 'markNotificationRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [CustomerDashboardController::class, 'markAllNotificationsRead'])->name('notifications.mark-all-read');
});

// Coming Soon Home page (new home page)
use App\Http\Controllers\HomeController;
Route::get('/', [HomeController::class, 'commingSoon'])->name('home');

// APK Download route
Route::get('/download-app', [HomeController::class, 'downloadApk'])->name('app.download');

// Old Agriculture Home page (kept aside for future use)
Route::get('/old-home', [HomeController::class, 'index'])->name('agriculture.home');

// Agriculture Product routes
Route::get('/products', [AgricultureProductController::class, 'index'])->name('agriculture.products.index');
Route::get('/products/search', [AgricultureProductController::class, 'search'])->name('agriculture.products.search');
Route::get('/products/{product}', [AgricultureProductController::class, 'show'])->name('agriculture.products.show');

// Agriculture Category routes
Route::get('/categories', [AgricultureCategoryController::class, 'index'])->name('agriculture.categories.index');
Route::get('/categories/{category}', [AgricultureCategoryController::class, 'show'])->name('agriculture.categories.show');

// Agriculture Cart routes with Rate Limiting
Route::get('/cart', [AgricultureCartController::class, 'index'])->name('agriculture.cart.index');
Route::post('/cart/add', [AgricultureCartController::class, 'add'])
    ->middleware('throttle:30,1') // 30 additions per minute
    ->name('agriculture.cart.add');
Route::patch('/cart/update', [AgricultureCartController::class, 'update'])
    ->middleware('throttle:30,1') // 30 updates per minute
    ->name('agriculture.cart.update');
Route::delete('/cart/remove', [AgricultureCartController::class, 'remove'])
    ->middleware('throttle:30,1') // 30 removals per minute
    ->name('agriculture.cart.remove');
Route::delete('/cart/clear', [AgricultureCartController::class, 'clear'])
    ->middleware('throttle:10,1') // 10 clears per minute
    ->name('agriculture.cart.clear');

// Wishlist (auth users)
Route::middleware('auth')->group(function () {
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('agriculture.wishlist.index');
    Route::post('/wishlist/add', [WishlistController::class, 'add'])->name('agriculture.wishlist.add');
    Route::delete('/wishlist/remove', [WishlistController::class, 'remove'])->name('agriculture.wishlist.remove');
    Route::delete('/wishlist/clear', [WishlistController::class, 'clear'])->name('agriculture.wishlist.clear');
});

// Agriculture Checkout routes with Rate Limiting
Route::get('/checkout', [AgricultureCheckoutController::class, 'index'])->name('agriculture.checkout.index');
Route::post('/checkout/process', [AgricultureCheckoutController::class, 'process'])
    ->middleware('throttle:10,1') // 10 checkout attempts per minute
    ->name('agriculture.checkout.process');
Route::get('/checkout/success/{orderNumber}', [AgricultureCheckoutController::class, 'success'])->name('agriculture.checkout.success');

// Static pages
Route::get('/about', function () {
    return view('pages.about');
})->name('about');

Route::get('/contact', function () {
    return view('pages.contact');
})->name('contact');

Route::get('/faq', function () {
    return view('pages.faq');
})->name('faq');

Route::get('/privacy', function () {
    return view('pages.privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('pages.terms');
})->name('terms');

// Contact form submission
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'subject' => 'required|string|max:255',
        'message' => 'required|string|max:1000',
    ]);
    
    // Here you would typically send an email or store in database
    // For now, just redirect back with success message
    return redirect()->back()->with('success', 'Thank you for your message! We will get back to you soon.');
})->name('contact.submit');

// Route name aliases for framework defaults
Route::get('/login', function () {
    return redirect()->route('auth.login');
})->name('login');

Route::get('/register', function () {
    return redirect()->route('auth.register');
})->name('register');

// Newsletter subscription
Route::post('/newsletter/subscribe', function (\Illuminate\Http\Request $request) {
    // Simple newsletter subscription (you can integrate with email service later)
    return redirect()->back()->with('success', 'Thank you for subscribing to our newsletter!');
})->name('newsletter.subscribe');

// Image serving route (fallback for when symlinks fail)
use App\Http\Controllers\ImageController;
Route::get('/image/{path}', [ImageController::class, 'serve'])
    ->where('path', '.*')
    ->name('image.serve');

// Admin Routes
require __DIR__.'/admin.php';
