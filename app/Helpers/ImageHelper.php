<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

/**
 * Image Helper - Handles image URLs with cache busting
 * 
 * This helper generates image URLs with automatic cache busters
 * to ensure users always see the latest image versions.
 */
class ImageHelper
{
    /**
     * Get image URL with cache buster timestamp
     * Multiple fallback mechanisms for reliability
     * 
     * @param string|null $path Image path relative to storage/app/public
     * @param string $disk Storage disk (default: 'public')
     * @return string Complete image URL with cache buster
     */
    public static function imageUrl(?string $path, string $disk = 'public'): string
    {
        // Return default if no path provided
        if (!$path) {
            return self::getDefaultImageUrl();
        }

        try {
            // Remove leading slash if present
            $path = ltrim($path, '/');
            
            // Try multiple locations to find the file
            $fullPath = self::findImageFile($path, $disk);
            
            // If file doesn't exist, use route-based serving as fallback
            if (!$fullPath || !file_exists($fullPath)) {
                return self::getRouteBasedImageUrl($path);
            }
            
            // Try direct storage URL first (fastest)
            $directUrl = self::tryDirectStorageUrl($path, $fullPath);
            if ($directUrl) {
                return $directUrl;
            }
            
            // Fallback to route-based serving
            return self::getRouteBasedImageUrl($path);
            
        } catch (\Exception $e) {
            \Log::warning('ImageHelper: Failed to generate URL', [
                'path' => $path,
                'error' => $e->getMessage(),
            ]);
            
            // Last resort: route-based serving
            return self::getRouteBasedImageUrl($path);
        }
    }
    
    /**
     * Try to get direct storage URL (fastest method)
     * 
     * @param string $path
     * @param string $fullPath
     * @return string|null
     */
    private static function tryDirectStorageUrl(string $path, string $fullPath): ?string
    {
        try {
            // Check if public/storage symlink exists and works
            $publicStoragePath = public_path('storage/' . $path);
            
            if (file_exists($publicStoragePath)) {
                $url = asset("storage/{$path}");
                $timestamp = filemtime($fullPath);
                return $url . '?v=' . $timestamp;
            }
            
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }
    
    /**
     * Get route-based image URL (fallback when symlink fails)
     * 
     * @param string $path
     * @return string
     */
    private static function getRouteBasedImageUrl(string $path): string
    {
        try {
            // Encode path for route
            $encodedPath = base64_encode($path);
            $url = route('image.serve', ['path' => $encodedPath]);
            
            // Add cache buster if file exists
            $fullPath = self::findImageFile($path);
            if ($fullPath && file_exists($fullPath)) {
                $timestamp = filemtime($fullPath);
                return $url . '?v=' . $timestamp;
            }
            
            return $url;
        } catch (\Exception $e) {
            return self::getDefaultImageUrl();
        }
    }
    
    /**
     * Find image file in multiple possible locations
     * 
     * @param string $path
     * @param string $disk
     * @return string|null
     */
    private static function findImageFile(string $path, string $disk = 'public'): ?string
    {
        // Remove leading slash
        $path = ltrim($path, '/');
        
        // Possible locations to check
        $locations = [
            storage_path("app/{$disk}/" . $path),
            public_path('storage/' . $path),
            storage_path('app/public/' . $path),
            // If path is just filename, try product directories
            storage_path('app/public/products/primary/' . basename($path)),
            storage_path('app/public/products/gallery/' . basename($path)),
        ];
        
        foreach ($locations as $location) {
            if (file_exists($location) && is_file($location)) {
                return $location;
            }
        }
        
        return null;
    }
    
    /**
     * Get default placeholder image URL
     * 
     * @return string
     */
    private static function getDefaultImageUrl(): string
    {
        return asset('assets/organic/images/product-thumb-1.png');
    }

    /**
     * Get image URL for product (checks multiple image types)
     * 
     * @param $product Product model instance
     * @return string Complete image URL
     */
    public static function productImageUrl($product): string
    {
        // Priority order: primary > featured > gallery > images > default
        
        if ($product->primary_image) {
            return self::imageUrl($product->primary_image);
        }

        if ($product->featured_image) {
            return self::imageUrl($product->featured_image);
        }

        // Gallery images (is_array or JSON)
        $galleryImages = is_array($product->gallery_images)
            ? $product->gallery_images
            : json_decode($product->gallery_images ?? '[]', true);

        if ($galleryImages && count($galleryImages) > 0) {
            return self::imageUrl($galleryImages[0]);
        }

        // Product images (is_array or JSON)
        $productImages = is_array($product->images)
            ? $product->images
            : json_decode($product->images ?? '[]', true);

        if ($productImages && count($productImages) > 0) {
            return self::imageUrl($productImages[0]);
        }

        // Return default if nothing found
        return asset('assets/organic/images/product-thumb-1.png');
    }

    /**
     * Get multiple gallery image URLs
     * 
     * @param array|string|null $images Gallery images (array or JSON)
     * @return array Array of image URLs with cache busters
     */
    public static function galleryImageUrls($images): array
    {
        $urls = [];

        // Convert JSON to array if needed
        if (is_string($images)) {
            $images = json_decode($images, true) ?? [];
        }

        if (!is_array($images)) {
            return $urls;
        }

        foreach ($images as $image) {
            $urls[] = self::imageUrl($image);
        }

        return $urls;
    }

    /**
     * Check if image file exists
     * 
     * @param string|null $path Image path
     * @return bool
     */
    public static function imageExists(?string $path): bool
    {
        if (!$path) {
            return false;
        }

        try {
            $fullPath = storage_path("app/public/{$path}");
            return file_exists($fullPath);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get image dimensions
     * 
     * @param string|null $path Image path
     * @return array|null [width, height] or null if not found
     */
    public static function getImageDimensions(?string $path): ?array
    {
        if (!$path) {
            return null;
        }

        try {
            $fullPath = storage_path("app/public/{$path}");

            if (!file_exists($fullPath)) {
                return null;
            }

            $imageInfo = @getimagesize($fullPath);
            
            if ($imageInfo === false) {
                return null;
            }

            return [
                'width' => $imageInfo[0],
                'height' => $imageInfo[1],
                'type' => $imageInfo[2],
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Generate responsive image srcset
     * 
     * @param string|null $path Image path
     * @param array $sizes Array of sizes [480, 768, 1024, etc]
     * @return string srcset attribute value
     */
    public static function responsiveImageSrcset(?string $path, array $sizes = []): string
    {
        if (!$path || empty($sizes)) {
            return '';
        }

        // Default responsive sizes if none provided
        if (empty($sizes)) {
            $sizes = [480, 768, 1024, 1280];
        }

        $srcsetParts = [];
        $url = self::imageUrl($path);

        foreach ($sizes as $size) {
            $srcsetParts[] = "{$url} {$size}w";
        }

        return implode(', ', $srcsetParts);
    }
}

