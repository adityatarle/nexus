<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\SubcategoryController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\DealerController;
use App\Http\Controllers\Api\OfferController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your mobile application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public Routes
Route::prefix('v1')->group(function () {
    // Diagnostic endpoint to test if API is accessible
    Route::get('/test', function () {
        return response()->json([
            'success' => true,
            'message' => 'API is accessible',
            'timestamp' => now()->toISOString(),
            'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ]);
    });
    
    // Authentication Routes
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    
    // Public Product Routes (with optional authentication for dealer pricing)
    // These routes are public but will authenticate user if token is provided
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/search', [ProductController::class, 'search']);
    Route::get('/products/featured', [ProductController::class, 'featured']);
    Route::get('/products/{product}', [ProductController::class, 'show']);
    
    // Public Category Routes
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{category}', [CategoryController::class, 'show']);
    
    // Public Subcategory Routes
    Route::get('/subcategories', [SubcategoryController::class, 'index']);
    Route::get('/subcategories/{subcategory}', [SubcategoryController::class, 'show']);
    
    // Public Offer Routes
    Route::get('/offers', [OfferController::class, 'index']);
    Route::get('/offers/{offer}', [OfferController::class, 'show']);
    Route::get('/offers/product/{productId}', [OfferController::class, 'forProduct']);
    Route::post('/offers/calculate-discount', [OfferController::class, 'calculateDiscount']);
    
    // Protected Routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        // Authentication
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'user']);
        
        // Profile Management
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::post('/profile/change-password', [ProfileController::class, 'changePassword']);
        
        // Cart Routes (session-backed; enable session for API requests)
        Route::middleware([\Illuminate\Session\Middleware\StartSession::class])->group(function () {
            Route::get('/cart', [CartController::class, 'index']);
            Route::post('/cart/add', [CartController::class, 'add']);
            Route::put('/cart/update', [CartController::class, 'update']);
            Route::delete('/cart/remove/{productId}', [CartController::class, 'remove']);
            Route::delete('/cart/clear', [CartController::class, 'clear']);
            Route::get('/cart/count', [CartController::class, 'count']);
        });
        
        // Wishlist Routes
        Route::get('/wishlist', [WishlistController::class, 'index']);
        Route::post('/wishlist/add', [WishlistController::class, 'add']);
        Route::delete('/wishlist/remove/{productId}', [WishlistController::class, 'remove']);
        Route::delete('/wishlist/clear', [WishlistController::class, 'clear']);
        Route::get('/wishlist/check/{productId}', [WishlistController::class, 'check']);
        
        // Order Routes
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{orderNumber}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders/{orderNumber}/invoice', [OrderController::class, 'invoice']);
        
        // Notifications
        Route::get('/notifications', [ProfileController::class, 'notifications']);
        Route::post('/notifications/{id}/read', [ProfileController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [ProfileController::class, 'markAllAsRead']);
        
        // Dealer Routes
        Route::get('/dealer/status', [DealerController::class, 'status']);
        Route::post('/dealer/register', [DealerController::class, 'register']);
    });
});

