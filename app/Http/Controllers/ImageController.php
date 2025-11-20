<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Image Controller - Route-based image serving with fallbacks
 * 
 * This provides a reliable way to serve images even if symlinks fail
 * or file permissions are incorrect.
 */
class ImageController extends Controller
{
    /**
     * Serve image from storage
     * 
     * @param string $path Encoded image path (base64)
     * @return BinaryFileResponse|\Illuminate\Http\Response
     */
    public function serve(string $path)
    {
        try {
            // Decode the path (base64 encoded for safety)
            $decodedPath = base64_decode($path, true);
            
            // If decoding failed, try using path as-is (for direct paths)
            if ($decodedPath === false) {
                $decodedPath = urldecode($path);
            }
            
            // Validate path to prevent directory traversal
            if (!$this->isValidPath($decodedPath)) {
                \Log::warning('Invalid image path attempted', ['path' => $path]);
                return $this->serveDefaultImage();
            }
            
            // Try multiple storage locations
            $fullPath = $this->findImageFile($decodedPath);
            
            if (!$fullPath || !file_exists($fullPath)) {
                \Log::warning('Image file not found', [
                    'decoded_path' => $decodedPath,
                    'checked_locations' => $this->getCheckedLocations($decodedPath),
                ]);
                // Return default image if file not found
                return $this->serveDefaultImage();
            }
            
            // Get file info
            $mimeType = mime_content_type($fullPath);
            if (!$mimeType) {
                $mimeType = 'image/jpeg'; // Default
            }
            
            $fileSize = filesize($fullPath);
            
            // Return image with proper headers
            return Response::file($fullPath, [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Image serving failed', [
                'path' => $path ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->serveDefaultImage();
        }
    }
    
    /**
     * Get checked locations for logging
     * 
     * @param string $path
     * @return array
     */
    private function getCheckedLocations(string $path): array
    {
        $path = ltrim($path, '/');
        return [
            storage_path('app/public/' . $path),
            public_path('storage/' . $path),
            storage_path('app/public/products/primary/' . basename($path)),
            storage_path('app/public/products/gallery/' . basename($path)),
        ];
    }
    
    /**
     * Find image file in multiple possible locations
     * 
     * @param string $path
     * @return string|null
     */
    private function findImageFile(string $path): ?string
    {
        // Remove leading slash if present
        $path = ltrim($path, '/');
        
        // Possible storage locations to check
        $locations = [
            // Standard storage location
            storage_path('app/public/' . $path),
            // Direct in public/storage (if symlink works)
            public_path('storage/' . $path),
            // Products directory variations
            storage_path('app/public/products/primary/' . basename($path)),
            storage_path('app/public/products/gallery/' . basename($path)),
            // Full path if already absolute
            $path,
        ];
        
        // Check each location
        foreach ($locations as $location) {
            if (file_exists($location) && is_file($location)) {
                return $location;
            }
        }
        
        return null;
    }
    
    /**
     * Validate path to prevent directory traversal attacks
     * 
     * @param string $path
     * @return bool
     */
    private function isValidPath(string $path): bool
    {
        // Remove leading/trailing slashes
        $path = trim($path, '/');
        
        // Check for directory traversal attempts
        if (str_contains($path, '..') || str_contains($path, '//')) {
            return false;
        }
        
        // Only allow image files
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowedExtensions);
    }
    
    /**
     * Serve default placeholder image
     * 
     * @return \Illuminate\Http\Response
     */
    private function serveDefaultImage()
    {
        $defaultPath = public_path('assets/organic/images/product-thumb-1.png');
        
        if (file_exists($defaultPath)) {
            return Response::file($defaultPath, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        }
        
        // Return 404 if default image doesn't exist
        abort(404, 'Image not found');
    }
}

