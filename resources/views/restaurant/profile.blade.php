<!DOCTYPE html>
<html lang="en">
<head>
    <title>Restaurant Profile</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        #restaurantProfileMap {
            height: 320px;
            width: 100%;
            border-radius: 12px;
            border: 1px solid #cbd5e1;
            margin-bottom: 15px;
            z-index: 1;
        }
        .profile-header {
            background: linear-gradient(135deg, #1A2C3E 0%, #2C3E50 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            color: white;
        }
        .form-section {
            background: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #FF6B35;
            color: #1A2C3E;
        }
        .readonly-field {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        .gst-preview {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
        }
        .password-requirements {
            font-size: 0.7rem;
            color: #6c7a8a;
            margin-top: 5px;
        }
        .info-text {
            font-size: 0.7rem;
            color: #6c7a8a;
            margin-top: 5px;
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="rounded-circle overflow-hidden shadow-sm d-flex align-items-center justify-content-center bg-white" style="width: 85px; height: 85px; border: 3px solid rgba(255,255,255,0.35);">
                        @if($restaurant->logo && file_exists(storage_path('app/public/restaurant/' . $restaurant->logo)))
                            <img id="header_logo_preview" src="{{ asset('storage/restaurant/' . $restaurant->logo) }}" alt="Restaurant Logo" style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <i class="fas fa-utensils text-primary" style="font-size: 2rem; color: #ff6a00 !important;"></i>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <h3 class="mb-1 text-white fw-bold"><i class="fas fa-store me-2"></i>{{ $restaurant->name }}</h3>
                    <p class="mb-1 text-white opacity-75">
                        <i class="fas fa-map-marker-alt me-1"></i> {{ $restaurant->address }}
                    </p>
                    <p class="mb-0 text-white opacity-75">
                        <i class="fas fa-envelope me-1"></i> {{ $restaurant->owner->email ?? 'N/A' }}
                        <i class="fas fa-user me-1"></i> {{ $restaurant->owner->name ?? Auth::user()->name ?? 'Owner' }} &nbsp;&bull;&nbsp; <i class="fas fa-envelope me-1"></i> {{ $restaurant->owner->email ?? 'N/A' }}
                    </p>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-lg-8">
                <!-- Restaurant Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-store me-2"></i> Restaurant Information
                    </div>
                    
                    <form method="POST" action="{{ route('restaurant.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="outlet_id" value="{{ $restaurant->id }}">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Restaurant Name <span class="text-danger">*</span></label>
                                    <input type="text" name="restaurant_name" class="form-control" 
                                           value="{{ old('restaurant_name', $restaurant->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Restaurant Logo</label>
                                    <input type="file" name="logo" id="restaurant_logo_input" class="form-control" accept="image/*" onchange="previewRestaurantLogo(this)">
                                    <small class="info-text">Upload brand logo (PNG, JPG, WebP, SVG - Max 3MB)</small>
                                    <div class="mt-2" id="logo_preview_container" style="display: {{ $restaurant->logo ? 'block' : 'none' }};">
                                        <div class="d-flex align-items-center gap-2">
                                            <img id="logo_preview_img" src="{{ $restaurant->logo ? asset('storage/restaurant/' . $restaurant->logo) : '' }}" alt="Logo Preview" style="max-height: 70px; max-width: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 3px; background: #fff; object-fit: contain;">
                                            <span class="text-muted small">Current Logo</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control readonly-field" 
                                           value="{{ $restaurant->owner->email ?? 'N/A' }}" readonly disabled>
                                    <small class="info-text">Email cannot be changed. Contact admin for email updates.</small>
                                    <label>Owner / User Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name', $restaurant->owner->name ?? Auth::user()->name ?? '') }}" required placeholder="Enter full name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="{{ old('phone', $restaurant->owner->phone ?? '') }}" required>
                                           value="{{ old('phone', $restaurant->owner->phone ?? Auth::user()->phone ?? '') }}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control readonly-field" 
                                   value="{{ $restaurant->owner->email ?? Auth::user()->email ?? 'N/A' }}" readonly disabled>
                            <small class="info-text">Email cannot be changed. Contact admin for email updates.</small>
                        </div>
                        
                        <div class="form-group">
                            <label>Address <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" 
                                   value="{{ old('address', $restaurant->address) }}" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Pincode <span class="text-danger">*</span></label>
                                    <input type="text" name="pincode" class="form-control" 
                                           value="{{ old('pincode', $restaurant->pincode) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>GSTIN Number</label>
                                    <input type="text" name="gstin" class="form-control" 
                                           value="{{ old('gstin', $restaurant->gstin) }}" 
                                           placeholder="22AAAAA0000A1Z">
                                    <small class="info-text">Format: 15 characters (e.g., 29ABCDE1234F1Z5)</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>GST Percentage (%)</label>
                                    <input type="number" name="gst_percentage" class="form-control" 
                                           value="{{ old('gst_percentage', $restaurant->gst_percentage ?? 0) }}" 
                                           step="0.01" min="0" max="100">
                                    <small class="info-text">Default GST rate for food billing (0-100%)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>FSSAI Number (Optional)</label>
                                    <input type="text" name="fssai_number" class="form-control" 
                                           value="{{ old('fssai_number', $restaurant->fssai_number) }}" 
                                           placeholder="14-digit FSSAI number">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>VATIN / State VAT Number <small class="text-muted">(Optional - For Bar)</small></label>
                                    <input type="text" name="vatin" class="form-control" 
                                           value="{{ old('vatin', $restaurant->vatin) }}" 
                                           placeholder="e.g. 27123456789V / Excise Lic.">
                                    <small class="info-text">State Liquor VAT TIN or Excise License number as per state excise laws</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Default Bar VAT (%) <small class="text-muted">(Optional)</small></label>
                                    <input type="number" name="vat_percentage" class="form-control" 
                                           value="{{ old('vat_percentage', $restaurant->vat_percentage ?? 0) }}" 
                                           step="0.01" min="0" max="100" placeholder="e.g. 20.00">
                                    <small class="info-text">Default State VAT rate for liquor/bar items (e.g., 10%, 14.5%, 20%)</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>UPI ID</label>
                                    <input type="text" name="upi_id" class="form-control" 
                                           value="{{ old('upi_id', $restaurant->upi_id) }}" 
                                           placeholder="example@upi">
                                    <small class="info-text">Enter UPI ID for payments</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Payment QR Code Image</label>
                                    <input type="file" name="qr_code_image" id="qr_code_input" class="form-control" accept="image/*" onchange="previewQRCode(this)">
                                    <small class="info-text">Upload a QR Code image for UPI payments (Max 2MB)</small>
                                    <div class="mt-2" id="qr_preview_container" style="display: {{ $restaurant->qr_code_image ? 'block' : 'none' }};">
                                        <div class="d-flex align-items-center gap-2">
                                            <img id="qr_preview_img" src="{{ $restaurant->qr_code_image ? asset('storage/restaurant/' . $restaurant->qr_code_image) : '' }}" alt="QR Code" style="max-height: 70px; max-width: 120px; border-radius: 8px; border: 1px solid #ddd; padding: 3px; background: #fff; object-fit: contain;">
                                            <span class="text-muted small">Current QR Code</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Thermal Print Setup (80mm vs 58mm) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <label class="d-flex align-items-center gap-2 fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                        <i class="fas fa-print text-primary"></i> Thermal Print Setup (Receipts &amp; KOT Slips)
                                    </label>
                                    <small class="text-muted d-block mb-3" style="font-size: 0.8rem;">
                                        Select the thermal paper roll width used by your counter POS and kitchen/bar receipt printers. Receipts and KOT slips will automatically format for the selected width.
                                    </small>

                                    @php
                                        $currentPrintSize = old('thermal_print_size', $restaurant->thermal_print_size ?? '80mm');
                                    @endphp
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="thermal-card-option border rounded p-3 d-flex align-items-start gap-3 w-100 mb-0" 
                                                   style="cursor: pointer; transition: all 0.2s ease; background: {{ $currentPrintSize === '80mm' ? '#eef2ff' : '#ffffff' }}; border-color: {{ $currentPrintSize === '80mm' ? '#4f46e5' : '#cbd5e1' }} !important;">
                                                <input type="radio" name="thermal_print_size" value="80mm" class="mt-1" {{ $currentPrintSize === '80mm' ? 'checked' : '' }} onchange="highlightPrintOption(this)">
                                                <div>
                                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                        <span>80mm Thermal (Standard)</span>
                                                        <span class="badge badge-primary bg-primary text-white" style="font-size: 0.65rem;">Default</span>
                                                    </div>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                        Standard 3-inch roll width (~74mm printable area). Best for desktop POS receipt printers (Epson, TVS, Star Micronics, Posiflex).
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="thermal-card-option border rounded p-3 d-flex align-items-start gap-3 w-100 mb-0" 
                                                   style="cursor: pointer; transition: all 0.2s ease; background: {{ $currentPrintSize === '58mm' ? '#eef2ff' : '#ffffff' }}; border-color: {{ $currentPrintSize === '58mm' ? '#4f46e5' : '#cbd5e1' }} !important;">
                                                <input type="radio" name="thermal_print_size" value="58mm" class="mt-1" {{ $currentPrintSize === '58mm' ? 'checked' : '' }} onchange="highlightPrintOption(this)">
                                                <div>
                                                    <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                                        <span>58mm Thermal (Compact / Mini)</span>
                                                        <span class="badge badge-secondary bg-secondary text-white" style="font-size: 0.65rem;">Narrow Roll</span>
                                                    </div>
                                                    <small class="text-muted d-block mt-1" style="font-size: 0.78rem;">
                                                        Compact 2-inch roll width (~48mm printable area). Formatted for portable, Bluetooth, or handheld mini thermal receipt printers.
                                                    </small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Restaurant Physical Location & Geofence (GPS Setup) -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4 p-3 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                                        <div>
                                            <label class="d-flex align-items-center gap-2 fw-bold text-dark mb-0" style="font-size: 0.95rem;">
                                                <i class="fas fa-map-marked-alt text-primary"></i> Restaurant Physical Location &amp; Geofence
                                            </label>
                                            <small class="text-muted d-block mt-1" style="font-size: 0.8rem;">
                                                Pinpoint the exact GPS coordinates of your restaurant. Drag the marker or click on the map to set location. Used for staff biometric attendance verification and delivery dispatch.
                                            </small>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span id="profileLocationStatusPill" class="badge {{ ($restaurant->latitude && $restaurant->longitude) ? 'badge-success bg-success' : 'badge-warning bg-warning' }} text-white px-2 py-1" style="font-size: 0.75rem;">
                                                <i class="fas {{ ($restaurant->latitude && $restaurant->longitude) ? 'fa-check-circle' : 'fa-exclamation-triangle' }} me-1"></i>
                                                {{ ($restaurant->latitude && $restaurant->longitude) ? 'Location Configured' : 'Coordinates Not Set' }}
                                            </span>
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="btnDetectProfileLocation">
                                                <i class="fas fa-crosshairs me-1"></i> Detect Current GPS
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Interactive Map --}}
                                    <div id="restaurantProfileMap"></div>

                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label class="small fw-bold text-muted mb-1">Latitude</label>
                                                <input type="number" step="any" name="latitude" id="profile_lat" class="form-control form-control-sm font-monospace" value="{{ old('latitude', $restaurant->latitude) }}" placeholder="e.g. 22.572646">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label class="small fw-bold text-muted mb-1">Longitude</label>
                                                <input type="number" step="any" name="longitude" id="profile_lng" class="form-control form-control-sm font-monospace" value="{{ old('longitude', $restaurant->longitude) }}" placeholder="e.g. 88.363895">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group mb-2">
                                                <label class="small fw-bold text-muted mb-1">Allowed Radius (Meters)</label>
                                                <input type="number" name="geofence_radius_meters" id="profile_geo_radius" class="form-control form-control-sm font-monospace" value="{{ old('geofence_radius_meters', $restaurant->geofence_radius_meters ?? 50) }}" min="10" max="1000">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2 pt-2 border-top">
                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input" type="checkbox" name="geofence_enabled" id="profile_geo_enabled" value="1" {{ old('geofence_enabled', $restaurant->geofence_enabled ?? true) ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-bold text-dark ms-1" for="profile_geo_enabled">
                                                Enforce GPS Attendance Geofencing for Staff
                                            </label>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-link text-danger p-0 text-decoration-none" id="btnClearProfileLocation">
                                                <i class="fas fa-trash-alt me-1"></i> Clear Coordinates
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- GST Information Preview -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i> GST Information
                    </div>
                    
                    <div class="gst-preview">
                        <div class="mb-3">
                            <strong><i class="fas fa-id-card me-1"></i> GSTIN:</strong><br>
                            @if($restaurant->gstin)
                                <span class="text-success">{{ $restaurant->gstin }}</span>
                                <span class="badge badge-success ml-2">Verified</span>
                            @else
                                <span class="text-muted">Not Provided</span>
                                <span class="badge badge-warning ml-2">Pending</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong><i class="fas fa-percent me-1"></i> GST Percentage:</strong><br>
                            <span class="text-primary">{{ $restaurant->gst_percentage ?? 0 }}%</span>
                        </div>
                        <div class="mb-3 pt-2 border-top">
                            <strong><i class="fas fa-print me-1"></i> Thermal Receipt Setup:</strong><br>
                            <span class="badge {{ ($restaurant->thermal_print_size ?? '80mm') === '58mm' ? 'badge-secondary bg-secondary' : 'badge-primary bg-primary' }} text-white mt-1" style="font-size: 0.75rem;">
                                {{ ($restaurant->thermal_print_size ?? '80mm') === '58mm' ? '58mm (2-inch Compact Roll)' : '80mm (3-inch Standard Roll)' }}
                            </span>
                        </div>
                        <div class="mt-2 pt-2 border-top">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                GST and thermal print settings apply automatically to receipts and KOTs.
                            </small>
                        </div>
                    </div>
                </div>
                
                <!-- Location & Geofence Preview Card -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-map-marked-alt me-2"></i> GPS &amp; Geofence
                    </div>
                    
                    <div class="gst-preview">
                        <div class="mb-3">
                            <strong><i class="fas fa-map-pin me-1 text-danger"></i> Coordinates:</strong><br>
                            @if($restaurant->latitude && $restaurant->longitude)
                                <span class="text-dark font-monospace small">{{ number_format($restaurant->latitude, 6) }}, {{ number_format($restaurant->longitude, 6) }}</span>
                                <span class="badge badge-success bg-success text-white ml-2">Active</span>
                            @else
                                <span class="text-muted small">Coordinates not set yet</span>
                                <span class="badge badge-warning bg-warning text-white ml-2">Pending</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong><i class="fas fa-circle-notch me-1 text-primary"></i> Geofence Radius:</strong><br>
                            <span class="text-primary font-weight-bold">{{ $restaurant->geofence_radius_meters ?? 50 }} meters</span>
                        </div>
                        <div class="mb-3">
                            <strong><i class="fas fa-shield-alt me-1 text-info"></i> Attendance Geofencing:</strong><br>
                            @if($restaurant->geofence_enabled ?? true)
                                <span class="badge badge-success bg-success text-white">Enabled (Strict Verification)</span>
                            @else
                                <span class="badge badge-secondary bg-secondary text-white">Disabled</span>
                            @endif
                        </div>
                        @if($restaurant->latitude && $restaurant->longitude)
                            <div class="pt-2 border-top">
                                <a href="https://www.google.com/maps?q={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" class="btn btn-sm btn-outline-info w-100">
                                    <i class="fas fa-external-link-alt me-1"></i> Open in Google Maps
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Change Password Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-key me-2"></i> Change Password
                    </div>
                    
                    <form method="POST" action="{{ route('restaurant.profile.password.update') }}">
                        @csrf
                        
                        <div class="form-group">
                            <label>Current Password <span class="text-danger">*</span></label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        
                        <div class="form-group">
                            <label>New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password" class="form-control" required>
                            <div class="password-requirements">
                                <i class="fas fa-info-circle"></i> Minimum 6 characters
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Confirm New Password <span class="text-danger">*</span></label>
                            <input type="password" name="new_password_confirmation" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-warning w-100">
                            <i class="fas fa-key me-2"></i> Change Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
// GSTIN Validation
const gstInput = document.querySelector('input[name="gstin"]');

gstInput.addEventListener('input', function () {

    // Convert to uppercase automatically
    this.value = this.value.toUpperCase().trim();

    const gstValue = this.value;

    // GSTIN Regex
    const gstRegex = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$/;

    // Empty value allowed
    if (gstValue === '') {
        this.classList.remove('is-valid');
        this.classList.remove('is-invalid');
        this.setCustomValidity('');
        return;
    }

    // Validate only when length is 15
    if (gstValue.length < 15) {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        this.setCustomValidity('GSTIN must be 15 characters');
        return;
    }

    // Final validation
    if (gstRegex.test(gstValue)) {
        this.classList.remove('is-invalid');
        this.classList.add('is-valid');
        this.setCustomValidity('');
    } else {
        this.classList.remove('is-valid');
        this.classList.add('is-invalid');
        this.setCustomValidity('Invalid GSTIN format');
    }
});

// GST Percentage Validation
const gstPercentage = document.querySelector('input[name="gst_percentage"]');

gstPercentage.addEventListener('input', function () {

    let value = parseFloat(this.value);

    if (isNaN(value)) {
        this.value = 0;
        return;
    }

    if (value < 0) {
        this.value = 0;
    }

    if (value > 100) {
        this.value = 100;
    }
});

// Logo live preview
function previewRestaurantLogo(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 3 * 1024 * 1024) {
            alert('File size exceeds 3MB limit. Please choose a smaller image.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('logo_preview_img').src = e.target.result;
            document.getElementById('logo_preview_container').style.display = 'block';
            const headerPreview = document.getElementById('header_logo_preview');
            if (headerPreview) {
                headerPreview.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
}

// QR Code live preview
function previewQRCode(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            alert('File size exceeds 2MB limit. Please choose a smaller image.');
            input.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('qr_preview_img').src = e.target.result;
            document.getElementById('qr_preview_container').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}

// Thermal print size radio selector highlight
function highlightPrintOption(radio) {
    document.querySelectorAll('.thermal-card-option').forEach(card => {
        card.style.background = '#ffffff';
        card.style.borderColor = '#cbd5e1';
    });
    const parentCard = radio.closest('.thermal-card-option');
    if (parentCard) {
        parentCard.style.background = '#eef2ff';
        parentCard.style.borderColor = '#4f46e5';
    }
}
</script>

<!-- Leaflet Map JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let profileMap = null;
let profileMarker = null;
let profileCircle = null;

function initProfileLocationMap() {
    const latInput = document.getElementById('profile_lat');
    const lngInput = document.getElementById('profile_lng');
    const radiusInput = document.getElementById('profile_geo_radius');
    const pill = document.getElementById('profileLocationStatusPill');

    if (!document.getElementById('restaurantProfileMap') || !latInput || !lngInput) {
        return;
    }

    const rawLat = parseFloat(latInput.value);
    const rawLng = parseFloat(lngInput.value);
    const hasExisting = !isNaN(rawLat) && !isNaN(rawLng) && rawLat !== 0;

    const defaultLat = hasExisting ? rawLat : 22.572646; // Fallback center
    const defaultLng = hasExisting ? rawLng : 88.363895;

    profileMap = L.map('restaurantProfileMap').setView([defaultLat, defaultLng], hasExisting ? 17 : 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(profileMap);

    profileMarker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(profileMap);
    profileMarker.bindPopup('<b>{{ addslashes($restaurant->name) }}</b><br>Drag marker or click on map to set location.');

    if (hasExisting) {
        profileMarker.openPopup();
    }

    const currentRadius = parseInt(radiusInput ? radiusInput.value : 50, 10) || 50;
    profileCircle = L.circle([defaultLat, defaultLng], {
        color: '#ff6a00',
        fillColor: '#ff8c42',
        fillOpacity: 0.22,
        radius: currentRadius
    }).addTo(profileMap);

    function updateInputs(lat, lng) {
        latInput.value = parseFloat(lat).toFixed(8);
        lngInput.value = parseFloat(lng).toFixed(8);
        if (pill) {
            pill.className = 'badge badge-success bg-success text-white px-2 py-1';
            pill.innerHTML = '<i class="fas fa-check-circle me-1"></i> Location Configured (' + parseFloat(lat).toFixed(4) + ', ' + parseFloat(lng).toFixed(4) + ')';
        }
    }

    // Drag marker
    profileMarker.on('dragend', function(e) {
        const pos = profileMarker.getLatLng();
        updateInputs(pos.lat, pos.lng);
        profileCircle.setLatLng(pos);
    });

    // Click map to reposition
    profileMap.on('click', function(e) {
        profileMarker.setLatLng(e.latlng);
        profileCircle.setLatLng(e.latlng);
        updateInputs(e.latlng.lat, e.latlng.lng);
    });

    // Manual input typing
    function onManualCoordsChange() {
        const newLat = parseFloat(latInput.value);
        const newLng = parseFloat(lngInput.value);
        if (!isNaN(newLat) && !isNaN(newLng) && newLat >= -90 && newLat <= 90 && newLng >= -180 && newLng <= 180) {
            profileMarker.setLatLng([newLat, newLng]);
            profileCircle.setLatLng([newLat, newLng]);
            profileMap.panTo([newLat, newLng]);
            if (pill) {
                pill.className = 'badge badge-success bg-success text-white px-2 py-1';
                pill.innerHTML = '<i class="fas fa-check-circle me-1"></i> Location Configured';
            }
        }
    }
    latInput.addEventListener('input', onManualCoordsChange);
    lngInput.addEventListener('input', onManualCoordsChange);

    // Dynamic radius update
    if (radiusInput) {
        radiusInput.addEventListener('input', function() {
            const r = parseInt(this.value, 10) || 50;
            profileCircle.setRadius(r);
        });
    }

    // Auto-detect GPS button
    const btnDetect = document.getElementById('btnDetectProfileLocation');
    if (btnDetect) {
        btnDetect.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your current browser.');
                return;
            }
            btnDetect.disabled = true;
            btnDetect.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Detecting GPS...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const uLat = position.coords.latitude;
                    const uLng = position.coords.longitude;
                    profileMap.flyTo([uLat, uLng], 18);
                    profileMarker.setLatLng([uLat, uLng]);
                    profileCircle.setLatLng([uLat, uLng]);
                    updateInputs(uLat, uLng);
                    profileMarker.openPopup();
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = '<i class="fas fa-crosshairs me-1"></i> Detect Current GPS';
                },
                function(error) {
                    btnDetect.disabled = false;
                    btnDetect.innerHTML = '<i class="fas fa-crosshairs me-1"></i> Detect Current GPS';
                    alert('Could not acquire location: ' + error.message);
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    }

    // Clear coordinates button
    const btnClear = document.getElementById('btnClearProfileLocation');
    if (btnClear) {
        btnClear.addEventListener('click', function() {
            if (confirm('Clear coordinates? Staff will not be restricted by geofencing until set again.')) {
                latInput.value = '';
                lngInput.value = '';
                if (pill) {
                    pill.className = 'badge badge-warning bg-warning text-white px-2 py-1';
                    pill.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i> Coordinates Not Set';
                }
            }
        });
    }

    // Trigger map resize check
    setTimeout(function() {
        if (profileMap) {
            profileMap.invalidateSize();
        }
    }, 400);
}

document.addEventListener('DOMContentLoaded', function() {
    initProfileLocationMap();
});
</script>

</body>
</html>