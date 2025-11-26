<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\DealerManagementController;

// Admin Login - Uses Laravel Auth system
Route::get('/admin/login', function () {
    if (Auth::check() && Auth::user()->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return view('admin.login');
})->name('admin.login');

Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        } else {
            Auth::logout();
            return redirect()->back()->withErrors(['email' => 'Access denied. Admin privileges required.']);
        }
    }
    
    return redirect()->back()->withErrors(['email' => 'Invalid credentials']);
})
->middleware('throttle:3,5') // 3 attempts per 5 minutes (stricter for admin)
->name('admin.login.post');

// Admin Dashboard Routes
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\OfferController;
use App\Http\Controllers\Admin\BrandController;

Route::prefix('admin')->name('admin.')->middleware(['auth', \App\Http\Middleware\EnsureUserIsAdmin::class])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products Management
    // Custom routes must be defined BEFORE resource routes to avoid route conflicts
    Route::get('products/download-template', [ProductController::class, 'downloadTemplate'])->name('products.download-template');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
    Route::patch('products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
    Route::resource('products', ProductController::class);
    
    // Categories Management
    Route::resource('categories', CategoryController::class);
    
    // Subcategories Management
    Route::resource('subcategories', SubcategoryController::class);
    
    // Brands Management
    Route::resource('brands', BrandController::class);
    
    // Orders Management
    Route::resource('orders', OrderController::class);
    Route::patch('orders/{order}/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::get('orders/{order}/invoice', [OrderController::class, 'generateInvoice'])->name('orders.invoice');
    
    // Dealer Management
    Route::prefix('dealers')->name('dealers.')->group(function () {
        Route::get('/', [DealerManagementController::class, 'index'])->name('index');
        Route::get('/{dealerRegistration}', [DealerManagementController::class, 'show'])->name('show');
        Route::post('/{dealerRegistration}/approve', [DealerManagementController::class, 'approve'])->name('approve');
        Route::post('/{dealerRegistration}/reject', [DealerManagementController::class, 'reject'])->name('reject');
        Route::get('/profile/{user}', [DealerManagementController::class, 'showDealerProfile'])->name('profile');
        Route::post('/{user}/revoke', [DealerManagementController::class, 'revokeDealerStatus'])->name('revoke');
        Route::post('/{user}/restore', [DealerManagementController::class, 'restoreDealerStatus'])->name('restore');
        Route::post('/{user}/reset-password', [DealerManagementController::class, 'resetPassword'])->name('reset-password');
    });
    
    // Customer Management
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/{user}', [CustomerController::class, 'show'])->name('show');
        Route::post('/{user}/reset-password', [CustomerController::class, 'resetPassword'])->name('reset-password');
    });
    
    // Reports & Analytics
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsController::class, 'index'])->name('index');
        Route::get('/sales', [ReportsController::class, 'sales'])->name('sales');
        Route::get('/inventory', [ReportsController::class, 'inventory'])->name('inventory');
        Route::get('/customers', [ReportsController::class, 'customers'])->name('customers');
        Route::get('/dealers', [ReportsController::class, 'dealers'])->name('dealers');
    });
    
    // Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/single', [SettingsController::class, 'updateSingle'])->name('settings.update-single');
    Route::post('/settings/upload-apk', [SettingsController::class, 'uploadApk'])->name('settings.upload-apk');
    
    // Offers Management
    Route::prefix('offers')->name('offers.')->group(function () {
        Route::patch('/{offer}/toggle-status', [OfferController::class, 'toggleStatus'])->name('toggle-status');
        Route::patch('/{offer}/toggle-featured', [OfferController::class, 'toggleFeatured'])->name('toggle-featured');
    });
    Route::resource('offers', OfferController::class);
    
});
