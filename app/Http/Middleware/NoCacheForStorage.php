<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to prevent caching of storage files
 * 
 * Adds headers to prevent browsers and CDNs from caching uploaded files
 * This ensures users always see the latest version of images
 */
class NoCacheForStorage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Check if this is a storage file request
        if ($this->isStorageFile($request)) {
            // Disable caching for storage files
            $response->header('Cache-Control', 'no-cache, no-store, must-revalidate, public, max-age=0');
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
            $response->header('X-Content-Type-Options', 'nosniff');
        }

        return $response;
    }

    /**
     * Check if request is for a storage file
     * 
     * @param Request $request
     * @return bool
     */
    private function isStorageFile(Request $request): bool
    {
        $path = $request->getPathInfo();
        
        // Check if path starts with /storage/
        return str_starts_with($path, '/storage/') || 
               str_contains($path, '/storage/');
    }
}








