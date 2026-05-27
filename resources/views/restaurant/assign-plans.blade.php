<!DOCTYPE html>
<html lang="en">
<head>
    <title>Assign Plans - {{ $restaurant->name }}</title>
    @include('includes.style')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <style>
        .plan-card {
            border: 1px solid #e0e6ed;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            transition: all 0.3s;
            background: white;
            cursor: pointer;
        }
        .plan-card:hover {
            border-color: #FF6B35;
            box-shadow: 0 4px 12px rgba(255,107,53,0.15);
            transform: translateY(-2px);
        }
        .plan-card.selected {
            border-color: #FF6B35;
            background: #fff5f0;
        }
        .plan-checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #FF6B35;
        }
        .plan-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: #FF6B35;
        }
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 15px 0 0;
        }
        .plan-features li {
            padding: 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.85rem;
        }
        .plan-features i {
            width: 20px;
            color: #10b981;
        }
        .restaurant-info {
            background: linear-gradient(135deg, #1A2C3E, #2C3E50);
            color: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .save-btn {
            background: linear-gradient(135deg, #FF6B35, #E85D2C);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            transition: all 0.3s;
        }
        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,107,53,0.3);
        }
        .selected-count {
            background: #FF6B35;
            color: white;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
@include('includes.sidebar')

<div class="pc-container">
    <div class="pc-content">
        
        <!-- Restaurant Info Header -->
        <div class="restaurant-info">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h4 class="text-white"><i class="fas fa-store me-2"></i>{{ $restaurant->name }}</h4>
                    <p class="mb-0 text-white-50">
                        <i class="fas fa-user me-1"></i> Owner: {{ $restaurant->owner->name ?? 'N/A' }} |
                        <i class="fas fa-envelope me-1"></i> {{ $restaurant->owner->email ?? 'N/A' }} |
                        <i class="fas fa-phone me-1"></i> {{ $restaurant->owner->phone ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <span class="badge badge-light">{{ $restaurant->pincode }}</span>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        @endif

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-tags me-2"></i>Assign Custom Plans</h5>
                <p class="text-muted mb-0">Select the plans you want to assign to this restaurant</p>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('manage.restaurant.save.plans') }}">
                    @csrf
                    <input type="hidden" name="restaurant_id" value="{{ $restaurant->id }}">
                    
                    <div class="text-center mb-4">
                        <span class="selected-count" id="selectedCount">
                            <i class="fas fa-check-circle"></i> <span id="selectedPlansCount">{{ count($assignedPlanIds) }}</span> Plans Selected
                        </span>
                    </div>

                    <div class="row">
                        @forelse($plans as $plan)
                            @php
                                $isChecked = in_array($plan->id, $assignedPlanIds);
                            @endphp
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="plan-card {{ $isChecked ? 'selected' : '' }}" data-plan-id="{{ $plan->id }}">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="mb-1">{{ $plan->name }}</h5>
                                            <div class="plan-price">
                                                @if($plan->price == 0)
                                                    <span class="text-success">FREE</span>
                                                @else
                                                    ₹{{ number_format($plan->price, 2) }}
                                                    <small class="text-muted">/ {{ ucfirst($plan->billing_cycle) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <input type="checkbox" 
                                                   name="plan_ids[]" 
                                                   value="{{ $plan->id }}" 
                                                   class="plan-checkbox"
                                                   {{ $isChecked ? 'checked' : '' }}
                                                   data-plan-id="{{ $plan->id }}">
                                        </div>
                                    </div>
                                    
                                    <ul class="plan-features">
                                        <li><i class="fas fa-folder"></i> {{ $plan->category_number == 0 ? 'Unlimited' : $plan->category_number }} Categories</li>
                                        <li><i class="fas fa-utensils"></i> {{ $plan->total_number_of_dishes == 0 ? 'Unlimited' : $plan->total_number_of_dishes }} Dishes</li>
                                        <li><i class="fas fa-table"></i> {{ $plan->total_number_of_table == 0 ? 'Unlimited' : $plan->total_number_of_table }} Tables</li>
                                        <li><i class="fas fa-boxes"></i> Inventory {{ $plan->inventory_checkbox == 'Y' ? 'Enabled' : 'Disabled' }}</li>
                                        <li><i class="fas fa-clock"></i> {{ ucfirst($plan->billing_cycle) }} subscription</li>
                                    </ul>
                                    
                                    @if($plan->description)
                                        <p class="text-muted small mt-2 mb-0">{{ \Str::limit($plan->description, 80) }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open" style="font-size: 48px; color: #cbd5e1;"></i>
                                    <h5 class="mt-3">No Custom Plans Available</h5>
                                    <p class="text-muted">No custom plans found. Please create plans first.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    @if($plans->count() > 0)
                    <div class="text-center mt-4">
                        <button type="submit" class="save-btn">
                            <i class="fas fa-save me-2"></i> Save Assigned Plans
                        </button>
                        <a href="{{ route('manage.restaurant') }}" class="btn btn-secondary ml-2">
                            <i class="fas fa-arrow-left me-2"></i> Back to Restaurants
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
@include('includes.script')

<script>
$(document).ready(function() {
    // Update selected count function
    function updateSelectedCount() {
        let count = $('.plan-checkbox:checked').length;
        $('#selectedPlansCount').text(count);
    }
    
    // Initial count
    updateSelectedCount();
    
    // Handle checkbox change
    $('.plan-checkbox').on('change', function() {
        let planCard = $(this).closest('.plan-card');
        
        if ($(this).is(':checked')) {
            planCard.addClass('selected');
        } else {
            planCard.removeClass('selected');
        }
        
        updateSelectedCount();
    });
    
    // Handle card click (toggle checkbox)
    $('.plan-card').on('click', function(e) {
        // Don't trigger if clicking on checkbox directly
        if ($(e.target).is('.plan-checkbox')) {
            return;
        }
        
        let checkbox = $(this).find('.plan-checkbox');
        checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
    });
    
    // Prevent checkbox click from bubbling to card click
    $('.plan-checkbox').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>

</body>
</html>