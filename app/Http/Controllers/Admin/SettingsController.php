<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    /**
     * Display settings page
     */
    public function index()
    {
        $generalSettings = Setting::where('group', 'general')->get();
        $pricingSettings = Setting::where('group', 'pricing')->get();
        $inventorySettings = Setting::where('group', 'inventory')->get();
        $dealerSettings = Setting::where('group', 'dealer')->get();
        $paymentSettings = Setting::where('group', 'payment')->get();

        return view('admin.settings.index', compact(
            'generalSettings',
            'pricingSettings',
            'inventorySettings',
            'dealerSettings',
            'paymentSettings'
        ));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updatedCount = 0;
            foreach ($request->settings as $settingData) {
                if (!isset($settingData['key'])) {
                    continue;
                }
                
                $setting = Setting::where('key', $settingData['key'])->first();
                
                if ($setting) {
                    $value = $settingData['value'] ?? null;
                    
                    // Handle JSON type settings
                    if ($setting->type === 'json') {
                        if (is_array($value)) {
                            $value = json_encode($value);
                        } elseif (is_string($value) && !empty($value)) {
                            // Try to decode to validate JSON
                            $decoded = json_decode($value, true);
                            if (json_last_error() === JSON_ERROR_NONE) {
                                $value = json_encode($decoded);
                            }
                        }
                    }
                    
                    // Update using DB facade to bypass model casting
                    DB::table('settings')
                        ->where('key', $settingData['key'])
                        ->update(['value' => $value, 'updated_at' => now()]);
                    
                    $updatedCount++;
                }
            }

            Setting::clearCache();

            if ($updatedCount > 0) {
                return redirect()->back()->with('success', 'Settings updated successfully!');
            } else {
                return redirect()->back()->with('warning', 'No settings were updated.');
            }
        } catch (\Exception $e) {
            \Log::error('Settings update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update settings: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update a single setting
     */
    public function updateSingle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
            'value' => 'nullable',
            'group' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        Setting::set($request->key, $request->value, $request->group ?? 'general');

        return response()->json(['success' => true, 'message' => 'Setting updated successfully']);
    }

    /**
     * Upload APK file
     */
    public function uploadApk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'apk_file' => 'required|file|mimes:apk|max:102400', // 100MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $fileUploadService = new FileUploadService();
            
            // Delete old APK if exists
            $oldApkPath = Setting::get('app_apk_path', null);
            if ($oldApkPath && Storage::disk('public')->exists($oldApkPath)) {
                Storage::disk('public')->delete($oldApkPath);
            }

            // Upload new APK
            $apkPath = $fileUploadService->uploadApk($request->file('apk_file'));

            // Verify file was uploaded
            if (!Storage::disk('public')->exists($apkPath)) {
                throw new \Exception('APK file upload failed. File not found in storage.');
            }

            // Save APK path to settings (bypass array cast by using updateOrCreate directly)
            $setting = Setting::updateOrCreate(
                ['key' => 'app_apk_path'],
                [
                    'value' => $apkPath,
                    'group' => 'app',
                    'type' => 'text',
                    'label' => 'App APK Path'
                ]
            );
            
            // Also save upload timestamp
            Setting::updateOrCreate(
                ['key' => 'app_apk_uploaded_at'],
                [
                    'value' => now()->toDateTimeString(),
                    'group' => 'app',
                    'type' => 'text',
                    'label' => 'APK Upload Date'
                ]
            );

            Setting::clearCache();
            
            // Log for debugging
            \Log::info('APK uploaded successfully', [
                'path' => $apkPath,
                'exists' => Storage::disk('public')->exists($apkPath),
                'size' => Storage::disk('public')->size($apkPath)
            ]);

            return redirect()->back()->with('success', 'APK file uploaded successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['apk_file' => $e->getMessage()])
                ->withInput();
        }
    }
}















