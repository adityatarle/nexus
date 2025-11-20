<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use App\Models\Wishlist;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Configure rate limiting for API - Increased to 500 for development and testing
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(500)->by($request->user()?->id ?: $request->ip());
        });
        
        // Use Bootstrap 5 for pagination styling
        Paginator::useBootstrapFive();
        
        View::composer('*', function ($view) {
            $cart = Session::get('cart', []);
            $cartCount = is_array($cart) ? array_sum(array_column($cart, 'quantity')) : 0;

            $wishlistCount = 0;
            if (Auth::check()) {
                $wishlistCount = Wishlist::where('user_id', Auth::id())->count();
            }

            $siteSettings = [
                'site_name' => Setting::get('site_name', 'Nexus Agriculture'),
                'site_logo' => Setting::get('site_logo'),
                'currency_symbol' => Setting::get('currency_symbol', '₹'),
            ];

            $view->with('cartCount', $cartCount)
                 ->with('wishlistCount', $wishlistCount)
                 ->with('siteSettings', $siteSettings)
                 ->with('currencySymbol', Setting::get('currency_symbol', '₹'));
        });
    }
}
