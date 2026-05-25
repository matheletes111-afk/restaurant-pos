<!DOCTYPE html>
<html lang="en">
<head>
    <title>Restaurant Profile</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <style>
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
                <div class="col-md-12">
                    <h3 class="mb-2 text-white"><i class="fas fa-store me-2"></i>{{ $restaurant->name }}</h3>
                    <p class="mb-0 text-white">
                        <i class="fas fa-map-marker-alt me-1"></i> {{ $restaurant->address }}
                    </p>
                    <p class="mb-0 text-white">
                        <i class="fas fa-envelope me-1"></i> {{ $restaurant->owner->email ?? 'N/A' }}
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
                    
                    <form method="POST" action="{{ route('restaurant.profile.update') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Restaurant Name <span class="text-danger">*</span></label>
                                    <input type="text" name="restaurant_name" class="form-control" 
                                           value="{{ old('restaurant_name', $restaurant->name) }}" required>
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
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="{{ old('phone', $restaurant->owner->phone ?? '') }}" required>
                                </div>
                            </div>
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
                                    <small class="info-text">Default GST rate for billing (0-100%)</small>
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
                        <div class="mt-3 pt-2 border-top">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                GST will be applied on all bills based on this percentage.
                            </small>
                        </div>
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
</script>

</body>
</html>