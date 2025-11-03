<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

        foreach ($request->settings as $settingData) {
            $setting = Setting::where('key', $settingData['key'])->first();
            
            if ($setting) {
                $value = $settingData['value'];
                
                // Handle JSON type settings
                if ($setting->type === 'json' && is_array($value)) {
                    $value = json_encode($value);
                }
                
                $setting->update(['value' => $value]);
            }
        }

        Setting::clearCache();

        return redirect()->back()->with('success', 'Settings updated successfully!');
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
}








