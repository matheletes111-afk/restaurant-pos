<!DOCTYPE html>
<html lang="en">
<head>
    <title>Menu Availability | Manage Product Status</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #FF6B35;
            --primary-dark: #E85D2C;
            --success: #2E9E4F;
            --danger: #E76F51;
            --gray: #6C7A8A;
            --dark: #1A2C3E;
            --light: #F7F9FC;
        }
        
        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #F5F7FB 0%, #EEF2F8 100%);
        }
        
        /* Page Header */
        .page-header-custom {
            background: linear-gradient(135deg, #1A2C3E 0%, #2C3E50 100%);
            border-radius: 24px;
            padding: 24px 32px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        
        .page-header-custom::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(255,107,53,0.15), transparent);
            border-radius: 50%;
        }
        
        .page-header-custom h5 {
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-header-custom p {
            color: rgba(255,255,255,0.7);
            margin-top: 8px;
            font-size: 0.9rem;
        }
        
        /* Stats Cards */
        .stats-row {
            margin-bottom: 28px;
        }
        
        .stat-mini-card {
            background: white;
            border-radius: 20px;
            padding: 1rem 1.25rem;
            text-align: center;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        
        .stat-mini-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        
        .stat-mini-number {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
        }
        
        .stat-mini-label {
            font-size: 0.75rem;
            color: var(--gray);
            font-weight: 500;
        }
        
        /* Filter Bar */
        .filter-bar {
            background: white;
            border-radius: 20px;
            padding: 1rem 1.5rem;
            margin-bottom: 28px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        }
        
        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }
        
        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        .search-box input {
            width: 100%;
            padding: 10px 12px 10px 38px;
            border: 1px solid #E2E8F0;
            border-radius: 40px;
            font-size: 0.85rem;
            transition: all 0.2s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(255,107,53,0.1);
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
        }
        
        .filter-btn {
            padding: 8px 20px;
            border-radius: 40px;
            border: 1px solid #E2E8F0;
            background: white;
            font-size: 0.8rem;
            font-weight: 500;
            color: var(--gray);
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .filter-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .filter-btn:hover:not(.active) {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        /* Product Cards Grid */
        .product-grid {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -12px;
        }
        
        .product-grid-item {
            flex: 0 0 25%;
            max-width: 25%;
            padding: 0 12px;
            margin-bottom: 24px;
        }
        
        /* Product Card */
        .product-card {
            background: white;
            border-radius: 24px;
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(0, 0, 0, 0.04);
            position: relative;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.1);
        }
        
        /* Food Type Badge */
        .food-type-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 10;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }
        
        .food-type-badge.veg {
            background: #4caf50;
            color: white;
        }
        
        .food-type-badge.non-veg {
            background: #f44336;
            color: white;
        }
        
        /* Product Image */
        .product-card-img {
            height: 180px;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .product-card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.4s ease;
        }
        
        .product-card:hover .product-card-img img {
            transform: scale(1.05);
        }
        
        .product-card-img .no-image {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .product-card-img .no-image i {
            font-size: 2.5rem;
            margin-bottom: 8px;
        }
        
        /* Card Body */
        .product-card-body {
            padding: 1rem 1rem 1.25rem;
        }
        
        .category-name {
            font-size: 0.7rem;
            color: var(--primary);
            background: rgba(255,107,53,0.1);
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            margin-bottom: 8px;
            font-weight: 600;
        }
        
        .product-card-title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
            line-height: 1.4;
            min-height: 44px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 12px;
        }
        
        /* Toggle Switch - Capsule Design */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.3s;
            border-radius: 30px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        input:checked + .toggle-slider {
            background-color: var(--success);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }
        
        input:disabled + .toggle-slider {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        /* Status Labels */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .status-badge.active {
            background: rgba(46,158,79,0.15);
            color: var(--success);
        }
        
        .status-badge.inactive {
            background: rgba(231,111,81,0.15);
            color: var(--danger);
        }
        
        /* Card Footer */
        .card-footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 0.75rem;
            border-top: 1px solid #E2E8F0;
        }
        
        /* Toast Notification */
        .toast-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            border-radius: 16px;
            padding: 14px 24px;
            box-shadow: 0 8px 28px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.3s ease;
            border-left: 4px solid var(--success);
        }
        
        .toast-notification.show {
            transform: translateX(0);
        }
        
        .toast-notification.error {
            border-left-color: var(--danger);
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .product-grid-item { flex: 0 0 33.333%; max-width: 33.333%; }
        }
        
        @media (max-width: 992px) {
            .product-grid-item { flex: 0 0 50%; max-width: 50%; }
        }
        
        @media (max-width: 576px) {
            .product-grid-item { flex: 0 0 100%; max-width: 100%; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .search-box { max-width: 100%; }
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 24px;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 1rem;
        }
        
        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            transition: all 0.2s;
        }
        
        .loading-overlay.active {
            visibility: visible;
            opacity: 1;
        }
        
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="loader-bg">
        <div class="loader-track"><div class="loader-fill"></div></div>
    </div>

    @include('includes.sidebar')

    <div class="pc-container">
        <div class="pc-content">
            
            <!-- Page Header -->
            <div class="page-header-custom">
                <h5>
                    <i class="fas fa-toggle-on"></i> 
                    Menu Availability
                </h5>
                <p>Manage your menu items visibility. Toggle ON/OFF to show/hide items from customers.</p>
            </div>
            
            <!-- Stats Row -->
            <div class="row stats-row">
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini-card">
                        <div class="stat-mini-number" id="totalCount">0</div>
                        <div class="stat-mini-label">Total Items</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini-card">
                        <div class="stat-mini-number text-success" id="activeCount">0</div>
                        <div class="stat-mini-label">Available Items</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini-card">
                        <div class="stat-mini-number text-danger" id="inactiveCount">0</div>
                        <div class="stat-mini-label">Unavialable Items</div>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="stat-mini-card">
                        <div class="stat-mini-number" id="vegCount">0</div>
                        <div class="stat-mini-label">Veg Items</div>
                    </div>
                </div>
            </div>
            
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search by product or category...">
                </div>
                <div class="filter-buttons">
                    <button class="filter-btn active" data-filter="all">All</button>
                    <button class="filter-btn" data-filter="active">Available</button>
                    <button class="filter-btn" data-filter="inactive">Unavialable</button>
                    <button class="filter-btn" data-filter="veg">Veg</button>
                    <button class="filter-btn" data-filter="nonveg">Non-Veg</button>
                </div>
            </div>
            
            <!-- Product Cards Grid -->
            <div class="product-grid" id="productGrid">
                @forelse($data as $product)
                <div class="product-grid-item" 
                     data-name="{{ strtolower($product->name) }}" 
                     data-category="{{ strtolower($product->category->name ?? '') }}"
                     data-status="{{ $product->status }}"
                     data-foodtype="{{ $product->food_type }}">
                    <div class="product-card">
                        <!-- Food Type Badge -->
                        <div class="food-type-badge {{ $product->food_type == 'VEG' ? 'veg' : 'non-veg' }}">
                            @if($product->food_type == 'VEG')
                                <i class="fa fa-leaf"></i>
                            @else
                                <i class="fa fa-utensils"></i>
                            @endif
                        </div>
                        
                        <!-- Product Image -->
                        <div class="product-card-img">
                            @if($product->image)
                                <img src="{{ URL::to('storage/app/public/category') }}/{{ $product->image }}" alt="{{ $product->name }}">
                            @else
                                <div class="no-image">
                                    <i class="fa fa-hamburger"></i>
                                    <span>No Image</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Details -->
                        <div class="product-card-body">
                            <div class="category-name">
                                <i class="fa fa-folder"></i> {{ $product->category->name ?? 'Uncategorized' }}
                            </div>
                            <h6 class="product-card-title">{{ $product->name }}</h6>
                            <div class="product-price">
                                ₹{{ number_format($product->price, 2) }}
                                <small class="text-muted">GST {{ $product->gst_rate }}%</small>
                            </div>
                            
                            <!-- Toggle & Status -->
                            <div class="card-footer-actions">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="status-badge {{ $product->status == 'A' ? 'active' : 'inactive' }}" id="statusText_{{ $product->id }}">
                                        <i class="fa {{ $product->status == 'A' ? 'fa-check-circle' : 'fa-circle' }}"></i>
                                        {{ $product->status == 'A' ? 'Available' : 'Unavailable' }}
                                    </span>
                                </div>
                                
                                <label class="toggle-switch">
                                    <input type="checkbox" class="status-toggle" 
                                           data-id="{{ $product->id }}"
                                           {{ $product->status == 'A' ? 'checked' : '' }}>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="empty-state">
                        <i class="fa fa-utensils"></i>
                        <p>No products found. Add some products to manage availability.</p>
                    </div>
                </div>
                @endforelse
            </div>
            
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>
    
    <!-- Toast Notification -->
    <div class="toast-notification" id="toastNotification">
        <i class="fas fa-check-circle" style="color: #2E9E4F; font-size: 1.2rem;"></i>
        <span id="toastMessage">Status updated successfully!</span>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('includes.script')
    
    <script>
        $(document).ready(function() {
            // Setup CSRF Token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // Update Stats
            function updateStats() {
                let total = $('.product-grid-item').length;
                let active = $('.product-grid-item[data-status="A"]').length;
                let inactive = $('.product-grid-item[data-status="I"]').length;
                let veg = $('.product-grid-item[data-foodtype="VEG"]').length;
                
                $('#totalCount').text(total);
                $('#activeCount').text(active);
                $('#inactiveCount').text(inactive);
                $('#vegCount').text(veg);
            }
            
            // Show Toast
            function showToast(message, isError = false) {
                let toast = $('#toastNotification');
                toast.find('#toastMessage').text(message);
                
                if (isError) {
                    toast.addClass('error');
                    toast.find('i').attr('class', 'fas fa-exclamation-circle').css('color', '#E76F51');
                } else {
                    toast.removeClass('error');
                    toast.find('i').attr('class', 'fas fa-check-circle').css('color', '#2E9E4F');
                }
                
                toast.addClass('show');
                setTimeout(() => {
                    toast.removeClass('show');
                }, 3000);
            }
            
            // Toggle Status via AJAX
            $('.status-toggle').on('change', function() {
                let toggle = $(this);
                let id = toggle.data('id');
                let isChecked = toggle.is(':checked');
                let card = toggle.closest('.product-grid-item');
                let statusBadge = $('#statusText_' + id);
                
                // Show loading
                $('#loadingOverlay').addClass('active');
                toggle.prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("menu.availability.toggle") }}',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Update data-status attribute
                            card.attr('data-status', response.status);
                            
                            // Update status badge
                            if (response.status === 'A') {
                                statusBadge.html('<i class="fa fa-check-circle"></i> Available');
                                statusBadge.removeClass('inactive').addClass('active');
                            } else {
                                statusBadge.html('<i class="fa fa-circle"></i> Unavailable');
                                statusBadge.removeClass('active').addClass('inactive');
                            }
                            
                            // Update stats
                            updateStats();
                            
                            // Show success toast
                            showToast(response.message);
                            
                            // Re-apply current filter if active
                            let activeFilter = $('.filter-btn.active').data('filter');
                            applyFilter(activeFilter);
                        } else {
                            // Revert toggle if failed
                            toggle.prop('checked', !isChecked);
                            showToast(response.message || 'Something went wrong', true);
                        }
                    },
                    error: function(xhr) {
                        // Revert toggle on error
                        toggle.prop('checked', !isChecked);
                        let errorMsg = xhr.responseJSON?.message || 'Network error. Please try again.';
                        showToast(errorMsg, true);
                    },
                    complete: function() {
                        $('#loadingOverlay').removeClass('active');
                        toggle.prop('disabled', false);
                    }
                });
            });
            
            // Search & Filter Functionality
            function applyFilter(filter) {
                let searchTerm = $('#searchInput').val().toLowerCase();
                
                $('.product-grid-item').each(function() {
                    let item = $(this);
                    let name = item.data('name');
                    let category = item.data('category');
                    let status = item.data('status');
                    let foodType = item.data('foodtype');
                    
                    let matchesSearch = (name.includes(searchTerm) || category.includes(searchTerm));
                    let matchesFilter = true;
                    
                    switch(filter) {
                        case 'active':
                            matchesFilter = (status === 'A');
                            break;
                        case 'inactive':
                            matchesFilter = (status === 'I');
                            break;
                        case 'veg':
                            matchesFilter = (foodType === 'VEG');
                            break;
                        case 'nonveg':
                            matchesFilter = (foodType === 'NON-VEG');
                            break;
                        default:
                            matchesFilter = true;
                    }
                    
                    if (matchesSearch && matchesFilter) {
                        item.show();
                    } else {
                        item.hide();
                    }
                });
                
                // Update stats based on visible items? Or keep original? Keep original stats from DB
                // But we can update counts for filtered view if needed
                let visibleCount = $('.product-grid-item:visible').length;
                if (visibleCount === 0) {
                    if ($('.product-grid .empty-message').length === 0) {
                        $('.product-grid').append('<div class="col-12 empty-message"><div class="empty-state"><i class="fa fa-search"></i><p>No matching items found</p></div></div>');
                    }
                } else {
                    $('.empty-message').remove();
                }
            }
            
            // Search input handler
            $('#searchInput').on('keyup', function() {
                let activeFilter = $('.filter-btn.active').data('filter');
                applyFilter(activeFilter);
            });
            
            // Filter button handler
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');
                let filter = $(this).data('filter');
                applyFilter(filter);
            });
            
            // Initial stats
            updateStats();
        });
    </script>
</body>
</html>