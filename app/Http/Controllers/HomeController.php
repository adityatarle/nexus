<?php

namespace App\Http\Controllers;

use App\Models\AgricultureCategory;
use App\Models\AgricultureProduct;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HomeController extends Controller
{
    public function index()
    {
        // Get categories that have products in stock
        $categories = AgricultureCategory::active()
            ->whereHas('products', function($query) {
                $query->where('is_active', true)
                      ->where('in_stock', true);
            })
            ->withCount(['products' => function($query) {
                $query->where('is_active', true)
                      ->where('in_stock', true);
            }])
            ->ordered()
            ->take(8)
            ->get();
        
        $featuredProducts = AgricultureProduct::active()
            ->featured()
            ->inStock()
            ->with('category')
            ->take(8)
            ->get();
        
        $newArrivals = AgricultureProduct::active()
            ->inStock()
            ->with('category')
            ->latest()
            ->take(8)
            ->get();
        
        $bestSellers = AgricultureProduct::active()
            ->inStock()
            ->with('category')
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->take(8)
            ->get();
        
        // Get statistics
        $stats = [
            'total_products' => AgricultureProduct::active()->count(),
            'total_customers' => User::customers()->count(),
            'total_categories' => AgricultureCategory::active()->count(),
            'total_farmers_served' => User::customers()->count() + User::where('role', 'dealer')->where('is_dealer_approved', true)->count(),
            'total_service_centers' => Setting::get('total_service_centers', 15),
        ];
        
        return view('home', compact('categories', 'featuredProducts', 'newArrivals', 'bestSellers', 'stats'));
    }

    /**
     * Show the coming soon page
     */
    public function comingSoon()
    {
        return view('coming-soon');
    }

    /**
     * Download APK file
     */
    public function downloadApk()
    {
        // First, try to get APK path from settings (if uploaded via admin)
        $apkPath = Setting::get('app_apk_path', null);
        
        // If no setting, look for default APK files in app-downloads directory
        if (!$apkPath || !Storage::disk('public')->exists($apkPath)) {
            // Default APK file names to check (in order of preference)
            $defaultApkNames = [
                'app.apk',
                'nexus-agriculture.apk',
                'agriculture-app.apk',
                'application.apk'
            ];
            
            $foundApk = null;
            foreach ($defaultApkNames as $apkName) {
                $defaultPath = "app-downloads/{$apkName}";
                if (Storage::disk('public')->exists($defaultPath)) {
                    $foundApk = $defaultPath;
                    break;
                }
            }
            
            // If still not found, look for any .apk file in the directory
            if (!$foundApk) {
                $directory = 'app-downloads';
                if (Storage::disk('public')->exists($directory)) {
                    $files = Storage::disk('public')->files($directory);
                    foreach ($files as $file) {
                        if (pathinfo($file, PATHINFO_EXTENSION) === 'apk') {
                            $foundApk = $file;
                            break;
                        }
                    }
                }
            }
            
            if ($foundApk) {
                $apkPath = $foundApk;
            } else {
                // Return a user-friendly error page instead of 404
                return redirect()->route('home')->with('error', 'APK file is not available yet. Please check back soon or contact the administrator.');
            }
        }

        $fullPath = Storage::disk('public')->path($apkPath);
        $filename = basename($apkPath);

        // Verify file exists before downloading
        if (!file_exists($fullPath)) {
            return redirect()->route('home')->with('error', 'APK file not found. Please contact the administrator.');
        }

        return response()->download($fullPath, $filename, [
            'Content-Type' => 'application/vnd.android.package-archive',
        ]);
    }
}
