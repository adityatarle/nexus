@extends('admin.layout')

@section('title', 'Settings - Nexus Agriculture Admin')
@section('page-title', 'Settings')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> Please check the form for errors.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#general" role="tab">
                            <i class="fas fa-cog me-2"></i>General
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#pricing" role="tab">
                            <i class="fas fa-dollar-sign me-2"></i>Pricing & Tax
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#inventory" role="tab">
                            <i class="fas fa-boxes me-2"></i>Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#dealer" role="tab">
                            <i class="fas fa-user-tie me-2"></i>Dealer Settings
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#payment" role="tab">
                            <i class="fas fa-credit-card me-2"></i>Payment Methods
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#app" role="tab">
                            <i class="fas fa-mobile-alt me-2"></i>Mobile App
                        </a>
                    </li>
                </ul>
            </div>
            
            <form action="{{ route('admin.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="card-body">
                    <div class="tab-content">
                        <!-- General Settings -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
                            <h5 class="mb-4">General Settings</h5>
                            @foreach($generalSettings as $setting)
                                @php
                                    // Handle array values (from JSON cast)
                                    if (is_array($setting->value)) {
                                        $value = $setting->type === 'json' ? json_encode($setting->value) : ($setting->getRawOriginal('value') ?? '');
                                    } else {
                                        $value = $setting->value ?? '';
                                    }
                                    // Ensure value is a string
                                    $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    @if($setting->type === 'textarea')
                                        <textarea 
                                            name="settings[{{ $loop->index }}][value]" 
                                            class="form-control"
                                            rows="3">{{ $value }}</textarea>
                                    @else
                                        <input 
                                            type="{{ $setting->type }}" 
                                            name="settings[{{ $loop->index }}][value]" 
                                            class="form-control" 
                                            value="{{ $value }}">
                                    @endif
                                    <input type="hidden" name="settings[{{ $loop->index }}][key]" value="{{ $setting->key }}">
                                    @if($setting->description)
                                        <small class="form-text text-muted">{{ $setting->description }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pricing & Tax Settings -->
                        <div class="tab-pane fade" id="pricing" role="tabpanel">
                            <h5 class="mb-4">Pricing & Tax Configuration</h5>
                            @foreach($pricingSettings as $setting)
                                @php
                                    // Handle array values (from JSON cast)
                                    if (is_array($setting->value)) {
                                        $value = $setting->type === 'json' ? json_encode($setting->value) : ($setting->getRawOriginal('value') ?? '');
                                    } else {
                                        $value = $setting->value ?? '';
                                    }
                                    // Ensure value is a string
                                    $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    <input 
                                        type="{{ $setting->type }}" 
                                        name="settings[{{ count($generalSettings) + $loop->index }}][value]" 
                                        class="form-control" 
                                        value="{{ $value }}">
                                    <input type="hidden" name="settings[{{ count($generalSettings) + $loop->index }}][key]" value="{{ $setting->key }}">
                                    @if($setting->description)
                                        <small class="form-text text-muted">{{ $setting->description }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Inventory Settings -->
                        <div class="tab-pane fade" id="inventory" role="tabpanel">
                            <h5 class="mb-4">Inventory Management</h5>
                            @foreach($inventorySettings as $setting)
                                @php
                                    // Handle array values (from JSON cast)
                                    if (is_array($setting->value)) {
                                        $value = $setting->type === 'json' ? json_encode($setting->value) : ($setting->getRawOriginal('value') ?? '');
                                    } else {
                                        $value = $setting->value ?? '';
                                    }
                                    // Ensure value is a string
                                    $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    <input 
                                        type="{{ $setting->type }}" 
                                        name="settings[{{ count($generalSettings) + count($pricingSettings) + $loop->index }}][value]" 
                                        class="form-control" 
                                        value="{{ $value }}">
                                    <input type="hidden" name="settings[{{ count($generalSettings) + count($pricingSettings) + $loop->index }}][key]" value="{{ $setting->key }}">
                                    @if($setting->description)
                                        <small class="form-text text-muted">{{ $setting->description }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Dealer Settings -->
                        <div class="tab-pane fade" id="dealer" role="tabpanel">
                            <h5 class="mb-4">Dealer Configuration</h5>
                            @foreach($dealerSettings as $setting)
                                @php
                                    // Handle array values (from JSON cast)
                                    if (is_array($setting->value)) {
                                        $value = $setting->type === 'json' ? json_encode($setting->value) : ($setting->getRawOriginal('value') ?? '');
                                    } else {
                                        $value = $setting->value ?? '';
                                    }
                                    // Ensure value is a string
                                    $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    <input 
                                        type="{{ $setting->type }}" 
                                        name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + $loop->index }}][value]" 
                                        class="form-control" 
                                        value="{{ $value }}">
                                    <input type="hidden" name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + $loop->index }}][key]" value="{{ $setting->key }}">
                                    @if($setting->description)
                                        <small class="form-text text-muted">{{ $setting->description }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Payment Methods -->
                        <div class="tab-pane fade" id="payment" role="tabpanel">
                            <h5 class="mb-4">Payment Methods</h5>
                            @foreach($paymentSettings as $setting)
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    @if($setting->type === 'json')
                                        @php
                                            // Handle JSON type settings - ensure we have an array
                                            if (is_array($setting->value)) {
                                                $methods = $setting->value;
                                            } elseif (is_string($setting->value)) {
                                                $decoded = json_decode($setting->value, true);
                                                $methods = is_array($decoded) ? $decoded : [];
                                            } else {
                                                // Try to get raw value
                                                $rawValue = $setting->getRawOriginal('value') ?? '';
                                                if (is_string($rawValue)) {
                                                    $decoded = json_decode($rawValue, true);
                                                    $methods = is_array($decoded) ? $decoded : [];
                                                } else {
                                                    $methods = is_array($rawValue) ? $rawValue : [];
                                                }
                                            }
                                            // Ensure methods is always an array
                                            $methods = is_array($methods) ? $methods : [];
                                        @endphp
                                        @if(!empty($methods))
                                            <div class="payment-methods-list">
                                                @foreach($methods as $index => $method)
                                                    <div class="input-group mb-2">
                                                        <input 
                                                            type="text" 
                                                            class="form-control" 
                                                            value="{{ is_string($method) ? $method : (is_array($method) ? json_encode($method) : '') }}"
                                                            readonly>
                                                        <span class="input-group-text bg-success text-white">
                                                            <i class="fas fa-check"></i>
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="alert alert-info">
                                                <small>No payment methods configured.</small>
                                            </div>
                                        @endif
                                        @php
                                            // For JSON type settings, always encode as JSON
                                            if (is_array($setting->value)) {
                                                $jsonValue = json_encode($setting->value);
                                            } else {
                                                $rawValue = $setting->getRawOriginal('value') ?? $setting->value ?? '';
                                                $jsonValue = is_array($rawValue) ? json_encode($rawValue) : (is_string($rawValue) ? $rawValue : json_encode($rawValue));
                                            }
                                        @endphp
                                        <input type="hidden" name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + count($dealerSettings) + $loop->index }}][value]" value="{{ $jsonValue }}">
                                        <input type="hidden" name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + count($dealerSettings) + $loop->index }}][key]" value="{{ $setting->key }}">
                                    @endif
                                    @if($setting->description)
                                        <small class="form-text text-muted">{{ $setting->description }}</small>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Mobile App Settings -->
                        <div class="tab-pane fade" id="app" role="tabpanel">
                            <h5 class="mb-4">Mobile App APK Configuration</h5>
                            
                            @php
                                $apkPath = \App\Models\Setting::get('app_apk_path', null);
                                $apkUploadedAt = \App\Models\Setting::get('app_apk_uploaded_at', null);
                                
                                // Check for manually placed APK files
                                $defaultApkNames = ['app.apk', 'nexus-agriculture.apk', 'agriculture-app.apk', 'application.apk'];
                                $foundApk = null;
                                $foundApkPath = null;
                                
                                if ($apkPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($apkPath)) {
                                    $foundApk = $apkPath;
                                    $foundApkPath = $apkPath;
                                } else {
                                    foreach ($defaultApkNames as $apkName) {
                                        $defaultPath = "app-downloads/{$apkName}";
                                        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($defaultPath)) {
                                            $foundApk = $apkName;
                                            $foundApkPath = $defaultPath;
                                            break;
                                        }
                                    }
                                    
                                    // If still not found, look for any .apk file
                                    if (!$foundApk && \Illuminate\Support\Facades\Storage::disk('public')->exists('app-downloads')) {
                                        $files = \Illuminate\Support\Facades\Storage::disk('public')->files('app-downloads');
                                        foreach ($files as $file) {
                                            if (pathinfo($file, PATHINFO_EXTENSION) === 'apk') {
                                                $foundApk = basename($file);
                                                $foundApkPath = $file;
                                                break;
                                            }
                                        }
                                    }
                                }
                            @endphp

                            <div class="card mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>APK File Status</h6>
                                </div>
                                <div class="card-body">
                                    @if($foundApk)
                                        <div class="alert alert-success">
                                            <strong><i class="fas fa-check-circle me-2"></i>APK File Found!</strong>
                                            <p class="mb-2 mt-2">
                                                <strong>File:</strong> {{ $foundApk }}<br>
                                                <strong>Path:</strong> <code>{{ $foundApkPath }}</code>
                                            </p>
                                            <a href="{{ route('app.download') }}" target="_blank" class="btn btn-success">
                                                <i class="fas fa-download me-1"></i>Test Download
                                            </a>
                                            @if($apkUploadedAt)
                                                <br><small class="text-muted mt-2 d-block">Uploaded via Admin: {{ \Carbon\Carbon::parse($apkUploadedAt)->format('M d, Y H:i') }}</small>
                                            @else
                                                <br><small class="text-muted mt-2 d-block">Manually placed in project folder</small>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <strong><i class="fas fa-exclamation-triangle me-2"></i>No APK File Found</strong>
                                            <p class="mb-0 mt-2">Please either upload an APK file below, or manually place your APK file in the project folder.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Option 1: Upload APK File</h6>
                                </div>
                                <div class="card-body">
                                    <form action="{{ route('admin.settings.upload-apk') }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        
                                        <div class="mb-3">
                                            <label for="apk_file" class="form-label">Upload APK File</label>
                                            <input 
                                                type="file" 
                                                class="form-control @error('apk_file') is-invalid @enderror" 
                                                id="apk_file" 
                                                name="apk_file" 
                                                accept=".apk"
                                                required>
                                            @error('apk_file')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Maximum file size: 100MB. Only .apk files are allowed.
                                            </small>
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-upload me-2"></i>Upload APK
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-folder me-2"></i>Option 2: Manual File Placement</h6>
                                </div>
                                <div class="card-body">
                                    <p>You can manually place your APK file in the project folder instead of uploading:</p>
                                    <ol>
                                        <li>Place your APK file in: <code>storage/app/public/app-downloads/</code></li>
                                        <li>Name it one of these (in order of preference):
                                            <ul>
                                                <li><code>app.apk</code></li>
                                                <li><code>nexus-agriculture.apk</code></li>
                                                <li><code>agriculture-app.apk</code></li>
                                                <li><code>application.apk</code></li>
                                            </ul>
                                        </li>
                                        <li>Or use any filename with <code>.apk</code> extension (first one found will be used)</li>
                                    </ol>
                                    <div class="alert alert-info">
                                        <strong>Full Path:</strong><br>
                                        <code>{{ storage_path('app/public/app-downloads') }}</code>
                                    </div>
                                    <p class="text-muted mb-0">
                                        <small>After placing the file, refresh this page to see the status update.</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection















