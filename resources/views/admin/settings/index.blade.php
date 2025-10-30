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
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    @if($setting->type === 'textarea')
                                        <textarea 
                                            name="settings[{{ $loop->index }}][value]" 
                                            class="form-control"
                                            rows="3">{{ $setting->value }}</textarea>
                                    @else
                                        <input 
                                            type="{{ $setting->type }}" 
                                            name="settings[{{ $loop->index }}][value]" 
                                            class="form-control" 
                                            value="{{ $setting->value }}">
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
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    <input 
                                        type="{{ $setting->type }}" 
                                        name="settings[{{ count($generalSettings) + $loop->index }}][value]" 
                                        class="form-control" 
                                        value="{{ $setting->value }}">
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
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    <input 
                                        type="{{ $setting->type }}" 
                                        name="settings[{{ count($generalSettings) + count($pricingSettings) + $loop->index }}][value]" 
                                        class="form-control" 
                                        value="{{ $setting->value }}">
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
                                <div class="mb-3">
                                    <label class="form-label">{{ $setting->label }}</label>
                                    <input 
                                        type="{{ $setting->type }}" 
                                        name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + $loop->index }}][value]" 
                                        class="form-control" 
                                        value="{{ $setting->value }}">
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
                                            $methods = is_string($setting->value) ? json_decode($setting->value, true) : $setting->value;
                                        @endphp
                                        <div class="payment-methods-list">
                                            @foreach($methods as $index => $method)
                                                <div class="input-group mb-2">
                                                    <input 
                                                        type="text" 
                                                        class="form-control" 
                                                        value="{{ $method }}"
                                                        readonly>
                                                    <span class="input-group-text bg-success text-white">
                                                        <i class="fas fa-check"></i>
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                        <input type="hidden" name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + count($dealerSettings) + $loop->index }}][value]" value="{{ $setting->value }}">
                                        <input type="hidden" name="settings[{{ count($generalSettings) + count($pricingSettings) + count($inventorySettings) + count($dealerSettings) + $loop->index }}][key]" value="{{ $setting->key }}">
                                    @endif
                                    @if($setting->description)
                                        <small class="form-text text-muted">{{ $setting->description }}</small>
                                    @endif
                                </div>
                            @endforeach
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





