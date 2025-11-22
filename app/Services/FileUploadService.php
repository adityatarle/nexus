<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class FileUploadService
{
    /**
     * Allowed MIME types for image uploads
     * Accept all image types
     */
    private const ALLOWED_MIME_TYPES = []; // Empty array means accept all image types

    /**
     * Maximum file size in bytes (2MB)
     */
    private const MAX_FILE_SIZE = 2048 * 1024; // 2MB

    /**
     * Minimum image dimensions
     */
    private const MIN_IMAGE_WIDTH = 400;
    private const MIN_IMAGE_HEIGHT = 400;

    /**
     * Maximum image dimensions (to prevent DOS attacks)
     */
    private const MAX_IMAGE_WIDTH = 4000;
    private const MAX_IMAGE_HEIGHT = 4000;

    /**
     * Upload a product image with security checks
     *
     * @param UploadedFile $file
     * @param string $type (e.g., 'product', 'category', 'banner')
     * @return string The path to the uploaded file
     * @throws Exception
     */
    public function uploadProductImage(UploadedFile $file, string $type = 'product'): string
    {
        // Validate file (with relaxed dimensions for banners)
        $this->validateFile($file, $type === 'banner');

        // Generate secure filename
        $filename = $this->generateSecureFilename($file);

        // Define storage path
        $directory = "products/{$type}";
        $path = "{$directory}/{$filename}";

        // Store file
        Storage::disk('public')->putFileAs($directory, $file, $filename);

        return $path;
    }

    /**
     * Upload dealer document with security checks
     *
     * @param UploadedFile $file
     * @param int $userId
     * @return string
     * @throws Exception
     */
    public function uploadDealerDocument(UploadedFile $file, int $userId): string
    {
        // Validate document
        $this->validateDocument($file);

        // Generate secure filename
        $filename = $this->generateSecureFilename($file);

        // Define storage path (private directory)
        $directory = "dealer-documents/{$userId}";
        $path = "{$directory}/{$filename}";

        // Store file in private storage
        Storage::disk('local')->putFileAs($directory, $file, $filename);

        return $path;
    }

    /**
     * Validate uploaded image file
     *
     * @param UploadedFile $file
     * @param bool $relaxDimensions For banners, allow wider images
     * @throws Exception
     */
    private function validateFile(UploadedFile $file, bool $relaxDimensions = false): void
    {
        // Check if file was uploaded successfully
        if (!$file->isValid()) {
            throw new Exception('File upload failed. Please try again.');
        }

        // Check file size
        if ($file->getSize() > self::MAX_FILE_SIZE) {
            throw new Exception('File size exceeds maximum allowed size of 2MB.');
        }

        // Check MIME type - accept all image types
        $mimeType = $file->getMimeType();
        
        // Only check that it's an image type (starts with 'image/')
        if (!str_starts_with($mimeType, 'image/')) {
            throw new Exception("Invalid file type '{$mimeType}'. Only image files are allowed.");
        }

        // Verify it's actually an image and check dimensions
        try {
            $imageInfo = @getimagesize($file->getRealPath());
            
            if ($imageInfo === false) {
                throw new Exception('File is not a valid image.');
            }

            [$width, $height] = $imageInfo;

            // Check minimum dimensions (relaxed for banners)
            if ($relaxDimensions) {
                // For banners, allow wider images (minimum 800x200)
                $minWidth = 800;
                $minHeight = 200;
                if ($width < $minWidth || $height < $minHeight) {
                    throw new Exception(
                        'Banner image dimensions must be at least ' . 
                        $minWidth . 'x' . $minHeight . ' pixels. ' .
                        "Your image is {$width}x{$height} pixels."
                    );
                }
            } else {
                // For regular images, use standard minimum
                if ($width < self::MIN_IMAGE_WIDTH || $height < self::MIN_IMAGE_HEIGHT) {
                    throw new Exception(
                        'Image dimensions must be at least ' . 
                        self::MIN_IMAGE_WIDTH . 'x' . self::MIN_IMAGE_HEIGHT . ' pixels. ' .
                        "Your image is {$width}x{$height} pixels."
                    );
                }
            }

            // Check maximum dimensions (prevent DOS)
            if ($width > self::MAX_IMAGE_WIDTH || $height > self::MAX_IMAGE_HEIGHT) {
                throw new Exception(
                    'Image dimensions must not exceed ' . 
                    self::MAX_IMAGE_WIDTH . 'x' . self::MAX_IMAGE_HEIGHT . ' pixels.'
                );
            }

        } catch (Exception $e) {
            throw new Exception('Invalid image file: ' . $e->getMessage());
        }
    }

    /**
     * Validate uploaded document file
     *
     * @param UploadedFile $file
     * @throws Exception
     */
    private function validateDocument(UploadedFile $file): void
    {
        // Check if file was uploaded successfully
        if (!$file->isValid()) {
            throw new Exception('File upload failed. Please try again.');
        }

        // Check file size (5MB max for documents)
        if ($file->getSize() > (5 * 1024 * 1024)) {
            throw new Exception('Document size exceeds maximum allowed size of 5MB.');
        }

        // Check MIME type for documents
        $allowedDocumentTypes = [
            'application/pdf',
            'image/jpeg',
            'image/jpg',
            'image/png',
        ];

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedDocumentTypes)) {
            throw new Exception('Invalid document type. Only PDF and image files are allowed.');
        }
    }

    /**
     * Generate a secure filename
     *
     * @param UploadedFile $file
     * @return string
     */
    private function generateSecureFilename(UploadedFile $file): string
    {
        // Get original filename without extension
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        
        // Sanitize filename (remove special characters, convert to lowercase)
        $safeName = Str::slug($originalName);
        
        // Limit filename length
        $safeName = Str::limit($safeName, 50, '');
        
        // Generate unique identifier
        $uniqueId = Str::random(16);
        
        // Get extension
        $extension = strtolower($file->getClientOriginalExtension());
        
        // Combine into secure filename
        return $safeName . '-' . time() . '-' . $uniqueId . '.' . $extension;
    }

    /**
     * Delete a file from storage
     *
     * @param string $path
     * @param string $disk (default: 'public')
     * @return bool
     */
    public function deleteFile(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }
        
        return false;
    }

    /**
     * Delete multiple files from storage
     *
     * @param array $paths
     * @param string $disk
     * @return bool
     */
    public function deleteFiles(array $paths, string $disk = 'public'): bool
    {
        $existingFiles = [];
        
        foreach ($paths as $path) {
            if (Storage::disk($disk)->exists($path)) {
                $existingFiles[] = $path;
            }
        }
        
        if (empty($existingFiles)) {
            return false;
        }
        
        return Storage::disk($disk)->delete($existingFiles);
    }

    /**
     * Get file URL
     *
     * @param string $path
     * @param string $disk
     * @return string
     */
    public function getFileUrl(string $path, string $disk = 'public'): string
    {
        if ($disk === 'public') {
            return Storage::disk('public')->url($path);
        }
        
        // For private files, you would need to create a route that checks permissions
        return route('files.download', ['path' => $path]);
    }

    /**
     * Check if file exists
     *
     * @param string $path
     * @param string $disk
     * @return bool
     */
    public function fileExists(string $path, string $disk = 'public'): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Upload APK file with security checks
     *
     * @param UploadedFile $file
     * @return string The path to the uploaded file
     * @throws Exception
     */
    public function uploadApk(UploadedFile $file): string
    {
        // Validate APK file
        $this->validateApk($file);

        // Generate secure filename (keep .apk extension)
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeName = Str::slug($originalName);
        $safeName = Str::limit($safeName, 50, '');
        $filename = $safeName . '-' . time() . '.apk';

        // Define storage path
        $directory = "app-downloads";
        $path = "{$directory}/{$filename}";

        // Ensure directory exists
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store file in public storage
        $stored = Storage::disk('public')->putFileAs($directory, $file, $filename);
        
        if (!$stored) {
            throw new Exception('Failed to store APK file in storage.');
        }

        // Verify file exists after upload
        if (!Storage::disk('public')->exists($path)) {
            throw new Exception('APK file was not saved correctly. Please try again.');
        }

        return $path;
    }

    /**
     * Validate uploaded APK file
     *
     * @param UploadedFile $file
     * @throws Exception
     */
    private function validateApk(UploadedFile $file): void
    {
        // Check if file was uploaded successfully
        if (!$file->isValid()) {
            throw new Exception('APK file upload failed. Please try again.');
        }

        // Check file size (100MB max for APK files)
        $maxSize = 100 * 1024 * 1024; // 100MB
        if ($file->getSize() > $maxSize) {
            throw new Exception('APK file size exceeds maximum allowed size of 100MB.');
        }

        // Check file extension
        $extension = strtolower($file->getClientOriginalExtension());
        if ($extension !== 'apk') {
            throw new Exception('Invalid file type. Only APK files are allowed.');
        }

        // Check MIME type (APK files can have different MIME types)
        $allowedMimeTypes = [
            'application/vnd.android.package-archive',
            'application/octet-stream',
            'application/zip',
        ];

        $mimeType = $file->getMimeType();
        if (!in_array($mimeType, $allowedMimeTypes)) {
            // Still allow if extension is .apk (some servers may report different MIME types)
            if ($extension === 'apk') {
                // Allow it but log a warning
                \Log::warning("APK file uploaded with unexpected MIME type: {$mimeType}");
            } else {
                throw new Exception('Invalid file type. Only APK files are allowed.');
            }
        }
    }
}














